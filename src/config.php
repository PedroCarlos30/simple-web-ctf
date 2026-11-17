<?php
// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do Banco de Dados SQLite
$dbDir = __DIR__ . '/data';
if (!is_dir($dbDir)) {
    mkdir($dbDir, 0777, true);
}
$dbPath = $dbDir . '/ctf.db';

function getDb() {
    global $dbPath;
    try {
        $pdo = new PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro ao conectar ao banco SQLite: " . $e->getMessage());
    }
}

// Inicializar banco de dados se não existir
function initDatabase() {
    $pdo = getDb();
    
    // Tabela de usuários para SQLi
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL,
        secret_notes TEXT
    )");

    // Tabela de comentários para XSS
    $pdo->exec("CREATE TABLE IF NOT EXISTS guestbook (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        author TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Tabela de perfis para IDOR
    $pdo->exec("CREATE TABLE IF NOT EXISTS profiles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        email TEXT NOT NULL,
        role TEXT NOT NULL,
        salary TEXT,
        confidential_data TEXT
    )");

    // Popular usuários se vazio
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO users (username, password, role, secret_notes) VALUES 
            ('admin', 'Secr3t_P@ssw0rd_99812!', 'Administrator', 'FLAG{sql1_byp4ss_l0g1n_succ3ss}'),
            ('analyst', 'analyst2026', 'Security Analyst', 'Nada suspeito por aqui.'),
            ('guest', 'guest', 'Guest', 'Acesso somente leitura.')
        ");
    }

    // Popular guestbook se vazio
    $stmt = $pdo->query("SELECT COUNT(*) FROM guestbook");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO guestbook (author, message) VALUES 
            ('Admin', 'Bem-vindo ao mural de recados da empresa! Deixe sua mensagem.'),
            ('Suporte', 'Lembre-se de não publicar credenciais aqui.')
        ");
    }

    // Popular perfis para IDOR se vazio
    $stmt = $pdo->query("SELECT COUNT(*) FROM profiles");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("INSERT INTO profiles (id, full_name, email, role, salary, confidential_data) VALUES 
            (1, 'Carlos Silva (Você)', 'carlos.silva@cyberpulse.local', 'Estagiário de TI', 'R$ 1.800', 'Suas férias estão aprovadas para Dezembro.'),
            (2, 'Beatriz Souza', 'beatriz.rh@cyberpulse.local', 'Analista de RH', 'R$ 4.500', 'Processo seletivo aberto para 3 vagas.'),
            (3, 'Dr. Roberto Matos', 'ceo@cyberpulse.local', 'Diretor Executivo (CEO)', 'R$ 48.000', 'FLAG{1d0r_pr1v1l3g3_3sc4l4t10n} - Chave mestre de aprovação financeira.')
        ");
    }
}

// Executar inicialização
initDatabase();

// Definição dos Desafios do CTF
$CHALLENGES = [
    'recon_01' => [
        'id' => 'recon_01',
        'title' => 'Reconhecimento & Vazamento de Código',
        'category' => 'Reconnaissance',
        'difficulty' => 'Fácil',
        'points' => 50,
        'description' => 'Desenvolvedores frequentemente deixam arquivos de backup temporários (.bak) ou esquecem referências em arquivos públicos do sistema como robots.txt e comentários no código-fonte.',
        'target_url' => 'challenges/recon.php',
        'flag' => 'FLAG{r3c0n_m4st3r_d1sc0v3ry}',
        'hints' => [
            'Dica 1: Verifique arquivos canônicos de rastreadores web como robots.txt na raiz do servidor.',
            'Dica 2: Procure por arquivos com extensão .bak ou comentários no HTML que apontem para backups esquecidos.'
        ],
        'mitigation' => "Nunca mantenha backups, arquivos temporários (.bak, .old, .swp) ou comentários com caminhos sensíveis em pastas públicas do servidor web. Configure o Apache (.htaccess ou VirtualHost) para bloquear acesso direto a extensões sensíveis."
    ],
    'sqli_02' => [
        'id' => 'sqli_02',
        'title' => 'SQL Injection: Bypass de Autenticação',
        'category' => 'Injection',
        'difficulty' => 'Fácil',
        'points' => 100,
        'description' => 'O portal de administração autentica usuários concatenando diretamente o login e a senha na consulta SQL sem sanitização ou Prepared Statements.',
        'target_url' => 'challenges/sqli.php',
        'flag' => 'FLAG{sql1_byp4ss_l0g1n_succ3ss}',
        'hints' => [
            'Dica 1: Tente quebrar a sintaxe SQL no campo de usuário usando aspas simples: \'',
            'Dica 2: Um payload clássico como \' OR 1=1 -- força a cláusula WHERE a ser sempre verdadeira.'
        ],
        'mitigation' => "Utilize sempre Prepared Statements (PDO com parâmetros vinculados como :user e :pass). Nunca concatene variáveis de entrada diretamente em queries SQL."
    ],
    'xss_03' => [
        'id' => 'xss_03',
        'title' => 'Cross-Site Scripting (XSS Stored & Reflected)',
        'category' => 'Client-Side',
        'difficulty' => 'Médio',
        'points' => 100,
        'description' => 'O mural de recados da empresa salva e exibe os comentários dos usuários diretamente no navegador sem escapar caracteres especiais HTML.',
        'target_url' => 'challenges/xss.php',
        'flag' => 'FLAG{xss_4l3rt_b0und4ry_br34k}',
        'hints' => [
            'Dica 1: Teste se tags HTML como <b>, <i> ou <script> são renderizadas sem filtro.',
            'Dica 2: Um script simples de teste como <script>alert(1)</script> ou <img src=x onerror=alert(1)> revela a vulnerabilidade.'
        ],
        'mitigation' => "Escape todas as saídas no HTML utilizando funções adequadas de contextual encoding como htmlspecialchars(\$input, ENT_QUOTES, 'UTF-8') e adicione cabeçalhos Content-Security-Policy (CSP)."
    ],
    'idor_04' => [
        'id' => 'idor_04',
        'title' => 'IDOR: Controle de Acesso Quebrado',
        'category' => 'Access Control',
        'difficulty' => 'Médio',
        'points' => 150,
        'description' => 'Ao visualizar seu próprio perfil interno (ID 1), o sistema aceita um parâmetro numérico no endereço sem verificar se você possui autorização para ver registros de outros usuários.',
        'target_url' => 'challenges/idor.php?user_id=1',
        'flag' => 'FLAG{1d0r_pr1v1l3g3_3sc4l4t10n}',
        'hints' => [
            'Dica 1: Observe o parâmetro user_id na URL ao acessar seu perfil.',
            'Dica 2: O que acontece se você mudar user_id=1 para user_id=2 ou user_id=3?'
        ],
        'mitigation' => "Implemente controle de acesso rigoroso no backend: verifique sempre se o usuário autenticado na sessão possui permissão para acessar o recurso solicitado, em vez de confiar cegamente no ID enviado pela requisição."
    ],
    'lfi_05' => [
        'id' => 'lfi_05',
        'title' => 'Local File Inclusion (LFI) & Path Traversal',
        'category' => 'File Inclusion',
        'difficulty' => 'Médio',
        'points' => 150,
        'description' => 'Um visualizador dinâmico de documentação carrega arquivos do sistema com base no parâmetro ?doc= sem validar o caminho relativo passado.',
        'target_url' => 'challenges/lfi.php?doc=about.php',
        'flag' => 'FLAG{lfi_tr4v3rs4l_s3cr3t_f1l3}',
        'hints' => [
            'Dica 1: Tente subir diretórios utilizando a sequência ../ (ex: ../../)',
            'Dica 2: Tente ler o arquivo secreto na raiz do site (ex: ../secret_flag_system.txt) ou até /etc/passwd.'
        ],
        'mitigation' => "Utilize uma lista branca restrita (whitelist) de arquivos permitidos, ou a função basename() para descartar qualquer tentativa de path traversal (../), combinando com realpath() para garantir que o arquivo esteja restrito à pasta segura."
    ],
    'cmdi_06' => [
        'id' => 'cmdi_06',
        'title' => 'Command Injection (RCE no Host)',
        'category' => 'Injection',
        'difficulty' => 'Difícil',
        'points' => 200,
        'description' => 'A ferramenta interna de diagnóstico de rede executa o utilitário do sistema operacional (ping) passando diretamente o IP informado pelo operador sem sanitização.',
        'target_url' => 'challenges/cmdi.php',
        'flag' => 'FLAG{rce_c0mm4nd_1nj3ct10n_pwn3d}',
        'hints' => [
            'Dica 1: No Linux, você pode encadear comandos no shell usando delimitadores como ; , && ou ||.',
            'Dica 2: Tente pingar 127.0.0.1; ls -la ou 127.0.0.1; cat secret_flag_system.txt'
        ],
        'mitigation' => "Evite ao máximo chamar comandos do sistema via shell_exec/system. Se estritamente necessário, valide o formato com expressões regulares (ex: filter_var com FILTER_VALIDATE_IP) e envolva os argumentos com escapeshellcmd() e escapeshellarg()."
    ],
    'crypto_07' => [
        'id' => 'crypto_07',
        'title' => 'Adulteração de Cookies & Sessão Criptográfica',
        'category' => 'Cryptography',
        'difficulty' => 'Médio',
        'points' => 150,
        'description' => 'O cofre VIP valida seu nível de privilégio com base em um cookie chamado "auth_token". No entanto, a segurança do token depende apenas de uma codificação fraca em Base64.',
        'target_url' => 'challenges/crypto.php',
        'flag' => 'FLAG{c00k13_m4n1pul4t10n_adm1n}',
        'hints' => [
            'Dica 1: Abra o Inspetor Web (F12) -> Armazenamento / Cookies e examine o cookie "auth_token".',
            'Dica 2: Decodifique a string de Base64, altere o papel "role" de "guest" para "admin", codifique novamente em Base64 e atualize seu cookie.'
        ],
        'mitigation' => "Nunca confie em dados do lado do cliente para controle de privilégios. Armazene o status de autorização na sessão do servidor ou utilize tokens criptograficamente assinados (como JWT com HMAC-SHA256 e chave secreta robusta)."
    ]
];

// Gerenciar progresso do usuário via sessão
if (!isset($_SESSION['solved_flags'])) {
    $_SESSION['solved_flags'] = [];
}

function isSolved($challengeId) {
    return in_array($challengeId, $_SESSION['solved_flags']);
}

function getTotalScore() {
    global $CHALLENGES;
    $score = 0;
    foreach ($_SESSION['solved_flags'] as $chId) {
        if (isset($CHALLENGES[$chId])) {
            $score += $CHALLENGES[$chId]['points'];
        }
    }
    return $score;
}

function getTotalPossibleScore() {
    global $CHALLENGES;
    $total = 0;
    foreach ($CHALLENGES as $ch) {
        $total += $ch['points'];
    }
    return $total;
}

