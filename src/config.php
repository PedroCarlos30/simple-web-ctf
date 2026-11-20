<?php
declare(strict_types=1);

// ================================================================
// CONFIGURAÇÕES GLOBAIS — CyberPulse CTF Lab
// ================================================================

// Chave secreta para assinatura do cookie de progresso e CSRF
// (uso local — não publicar em repositório público)
define('COOKIE_SECRET', 'CyberPulse_CTF_S3cr3t_K3y_2026!@#$');
define('PROGRESS_COOKIE', 'cp_progress');
define('PROGRESS_COOKIE_TTL', 60 * 60 * 24 * 30); // 30 dias

// ================================================================
// SISTEMA DE PROGRESSO — Cookie HMAC-SHA256
// ================================================================

/**
 * Lê e valida o cookie de progresso.
 * Retorna array de IDs de desafios resolvidos.
 */
function getProgress(): array {
    $raw = $_COOKIE[PROGRESS_COOKIE] ?? '';
    if (empty($raw)) {
        return [];
    }

    // Formato: base64url(json) . '.' . hmac
    $parts = explode('.', $raw, 2);
    if (count($parts) !== 2) {
        return [];
    }

    [$b64, $sig] = $parts;
    $expected = hash_hmac('sha256', $b64, COOKIE_SECRET);

    if (!hash_equals($expected, $sig)) {
        // Cookie adulterado — ignorar
        return [];
    }

    $json = base64_decode(strtr($b64, '-_', '+/'));
    if ($json === false) {
        return [];
    }

    $data = json_decode($json, true);
    if (!is_array($data) || !isset($data['solved']) || !is_array($data['solved'])) {
        return [];
    }

    return $data['solved'];
}

/**
 * Persiste o array de IDs resolvidos num cookie assinado.
 */
function saveProgress(array $solved): void {
    $json  = json_encode(['solved' => array_values($solved)], JSON_UNESCAPED_UNICODE);
    $b64   = strtr(base64_encode($json), '+/', '-_');
    $sig   = hash_hmac('sha256', $b64, COOKIE_SECRET);
    $value = $b64 . '.' . $sig;

    setcookie(PROGRESS_COOKIE, $value, [
        'expires'  => time() + PROGRESS_COOKIE_TTL,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    // Atualiza também o superglobal para a requisição atual
    $_COOKIE[PROGRESS_COOKIE] = $value;
}

/**
 * Verifica se um desafio já foi resolvido.
 */
function isSolved(string $id): bool {
    return in_array($id, getProgress(), true);
}

/**
 * Marca um desafio como resolvido e persiste.
 * Retorna true se foi adicionado agora, false se já estava resolvido.
 */
function addSolved(string $id): bool {
    $solved = getProgress();
    if (in_array($id, $solved, true)) {
        return false; // já resolvido
    }
    $solved[] = $id;
    saveProgress($solved);
    return true;
}

// ================================================================
// CSRF TOKEN (determinístico, sem sessão)
// ================================================================

function getCsrfToken(): string {
    return hash_hmac('sha256', 'csrf_reset_lab', COOKIE_SECRET);
}

function validateCsrfToken(string $token): bool {
    return hash_equals(getCsrfToken(), $token);
}

// ================================================================
// BANCO DE DADOS SQLite
// ================================================================

$dbDir  = __DIR__ . '/data';
$dbPath = $dbDir . '/ctf.db';

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

function getDb(): PDO {
    global $dbPath;
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die('Erro ao conectar ao banco SQLite: ' . $e->getMessage());
    }
}

function initDatabase(): void {
    $pdo = getDb();

    // Tabela de usuários para SQLi e Brute Force
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
    $stmt = $pdo->query('SELECT COUNT(*) FROM users');
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO users (username, password, role, secret_notes) VALUES
            ('admin',    'Secr3t_P\@ssw0rd_99812!', 'Administrator',    'FLAG{sql1_byp4ss_l0g1n_succ3ss}'),
            ('analyst',  'analyst2026',              'Security Analyst', 'Nada suspeito por aqui.'),
            ('guest',    'guest',                    'Guest',            'Acesso somente leitura.'),
            ('carlos',   'password123',              'Colaborador',      'FLAG{brut3f0rc3_n0_r4t3_l1m1t}')
        ");
    }

    // Popular guestbook se vazio
    $stmt = $pdo->query('SELECT COUNT(*) FROM guestbook');
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO guestbook (author, message) VALUES
            ('Admin',   'Bem-vindo ao mural de recados da empresa! Deixe sua mensagem.'),
            ('Suporte', 'Lembre-se de não publicar credenciais aqui.')
        ");
    }

    // Popular perfis para IDOR se vazio
    $stmt = $pdo->query('SELECT COUNT(*) FROM profiles');
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO profiles (id, full_name, email, role, salary, confidential_data) VALUES
            (1, 'Carlos Silva (Você)',  'carlos.silva@cyberpulse.local', 'Estagiário de TI',      'R\$ 1.800',  'Suas férias estão aprovadas para Dezembro.'),
            (2, 'Beatriz Souza',        'beatriz.rh@cyberpulse.local',   'Analista de RH',        'R\$ 4.500',  'Processo seletivo aberto para 3 vagas.'),
            (3, 'Dr. Roberto Matos',    'ceo@cyberpulse.local',          'Diretor Executivo (CEO)','R\$ 48.000', 'FLAG{1d0r_pr1v1l3g3_3sc4l4t10n} - Chave mestre de aprovação financeira.')
        ");
    }
}

initDatabase();

// ================================================================
// DEFINIÇÃO DOS DESAFIOS CTF
// ================================================================

$CHALLENGES = [
    'recon_01' => [
        'id'         => 'recon_01',
        'title'      => 'Reconhecimento & Vazamento de Código',
        'category'   => 'Reconnaissance',
        'difficulty' => 'Fácil',
        'points'     => 50,
        'description'=> 'Desenvolvedores frequentemente deixam arquivos de backup temporários (.bak) ou esquecem referências em arquivos públicos do sistema como robots.txt e comentários no código-fonte.',
        'target_url' => 'challenges/recon.php',
        'flag'       => 'FLAG{r3c0n_m4st3r_d1sc0v3ry}',
        'hint1'      => 'Verifique arquivos canônicos de rastreadores web como robots.txt na raiz do servidor.',
        'hint2'      => 'Procure por arquivos com extensão .bak listados no robots.txt e acesse-os diretamente no navegador.',
        'mitigation' => 'Nunca mantenha backups, arquivos temporários (.bak, .old, .swp) ou comentários com caminhos sensíveis em pastas públicas do servidor web. Configure o Apache para bloquear acesso direto a extensões sensíveis.',
    ],
    'sqli_02' => [
        'id'         => 'sqli_02',
        'title'      => 'SQL Injection: Bypass de Autenticação',
        'category'   => 'Injection',
        'difficulty' => 'Fácil',
        'points'     => 100,
        'description'=> 'O portal de administração autentica usuários concatenando diretamente o login e a senha na consulta SQL sem sanitização ou Prepared Statements.',
        'target_url' => 'challenges/sqli.php',
        'flag'       => 'FLAG{sql1_byp4ss_l0g1n_succ3ss}',
        'hint1'      => 'Tente quebrar a sintaxe SQL no campo de usuário usando aspas simples: \'',
        'hint2'      => 'Um payload clássico como \' OR 1=1 -- força a cláusula WHERE a ser sempre verdadeira, ignorando a verificação de senha.',
        'mitigation' => 'Utilize sempre Prepared Statements (PDO com parâmetros vinculados como :user e :pass). Nunca concatene variáveis de entrada diretamente em queries SQL.',
    ],
    'xss_03' => [
        'id'         => 'xss_03',
        'title'      => 'Cross-Site Scripting (XSS Stored & Reflected)',
        'category'   => 'Client-Side',
        'difficulty' => 'Médio',
        'points'     => 100,
        'description'=> 'O mural de recados da empresa salva e exibe os comentários dos usuários diretamente no navegador sem escapar caracteres especiais HTML.',
        'target_url' => 'challenges/xss.php',
        'flag'       => 'FLAG{xss_4l3rt_b0und4ry_br34k}',
        'hint1'      => 'Teste se tags HTML como <b>, <i> ou <script> são renderizadas sem filtro no campo de busca ou no mural.',
        'hint2'      => 'Injete <script>alert(1)</script> ou <img src=x onerror=alert(1)>. Depois inspecione o código-fonte da página (Ctrl+U) para encontrar a flag escondida.',
        'mitigation' => "Escape todas as saídas no HTML com htmlspecialchars(\$input, ENT_QUOTES, 'UTF-8') e adicione cabeçalhos Content-Security-Policy (CSP).",
    ],
    'idor_04' => [
        'id'         => 'idor_04',
        'title'      => 'IDOR: Controle de Acesso Quebrado',
        'category'   => 'Access Control',
        'difficulty' => 'Médio',
        'points'     => 150,
        'description'=> 'Ao visualizar seu próprio perfil interno (ID 1), o sistema aceita um parâmetro numérico no endereço sem verificar se você possui autorização para ver registros de outros usuários.',
        'target_url' => 'challenges/idor.php?user_id=1',
        'flag'       => 'FLAG{1d0r_pr1v1l3g3_3sc4l4t10n}',
        'hint1'      => 'Observe o parâmetro user_id na URL ao acessar seu perfil.',
        'hint2'      => 'O que acontece se você mudar user_id=1 para user_id=2 ou user_id=3? O backend não verifica se você tem permissão para acessar outros perfis.',
        'mitigation' => 'Implemente controle de acesso rigoroso no backend: verifique sempre se o usuário autenticado na sessão possui permissão para acessar o recurso solicitado.',
    ],
    'lfi_05' => [
        'id'         => 'lfi_05',
        'title'      => 'Local File Inclusion (LFI) & Path Traversal',
        'category'   => 'File Inclusion',
        'difficulty' => 'Médio',
        'points'     => 150,
        'description'=> 'Um visualizador dinâmico de documentação carrega arquivos do sistema com base no parâmetro ?doc= sem validar o caminho relativo passado.',
        'target_url' => 'challenges/lfi.php?doc=about.php',
        'flag'       => 'FLAG{lfi_tr4v3rs4l_s3cr3t_f1l3}',
        'hint1'      => 'Tente subir diretórios utilizando a sequência ../ (ex: ../../)',
        'hint2'      => 'O arquivo secreto está na raiz web. Tente: ?doc=../../secret_flag_system.txt para lê-lo via path traversal.',
        'mitigation' => 'Utilize uma lista branca restrita (whitelist) de arquivos permitidos, ou a função basename() para descartar qualquer tentativa de path traversal (../), combinando com realpath().',
    ],
    'cmdi_06' => [
        'id'         => 'cmdi_06',
        'title'      => 'Command Injection (RCE no Host)',
        'category'   => 'Injection',
        'difficulty' => 'Difícil',
        'points'     => 200,
        'description'=> 'A ferramenta interna de diagnóstico de rede executa o utilitário do sistema operacional (ping) passando diretamente o IP informado pelo operador sem sanitização.',
        'target_url' => 'challenges/cmdi.php',
        'flag'       => 'FLAG{rce_c0mm4nd_1nj3ct10n_pwn3d}',
        'hint1'      => 'No Linux, você pode encadear comandos no shell usando delimitadores como ; , && ou ||.',
        'hint2'      => 'Tente: 127.0.0.1; cat ../secret_flag_system.txt — o ponto e vírgula executa um segundo comando após o ping.',
        'mitigation' => 'Evite ao máximo chamar comandos do sistema via shell_exec/system. Se necessário, valide com filter_var(FILTER_VALIDATE_IP) e envolva os argumentos com escapeshellarg().',
    ],
    'crypto_07' => [
        'id'         => 'crypto_07',
        'title'      => 'Adulteração de Cookies & Sessão Criptográfica',
        'category'   => 'Cryptography',
        'difficulty' => 'Médio',
        'points'     => 150,
        'description'=> 'O cofre VIP valida seu nível de privilégio com base em um cookie chamado "auth_token". A segurança do token depende apenas de uma codificação fraca em Base64.',
        'target_url' => 'challenges/crypto.php',
        'flag'       => 'FLAG{c00k13_m4n1pul4t10n_adm1n}',
        'hint1'      => 'Abra o Inspetor Web (F12) → Armazenamento / Cookies e examine o cookie "auth_token". Base64 não é criptografia.',
        'hint2'      => 'Decodifique o Base64, altere "role": "guest" para "role": "admin", codifique novamente e atualize o cookie. Use o editor embutido na página.',
        'mitigation' => 'Nunca confie em dados do lado do cliente para controle de privilégios. Use sessões do servidor ou tokens JWT assinados com HMAC-SHA256.',
    ],
    'ssrf_08' => [
        'id'         => 'ssrf_08',
        'title'      => 'SSRF: Falsificação de Requisição pelo Servidor',
        'category'   => 'SSRF',
        'difficulty' => 'Difícil',
        'points'     => 200,
        'description'=> 'A ferramenta de verificação de URLs corporativa não valida se o destino é interno ou externo. Um atacante pode forçar o servidor a acessar serviços internos inacessíveis pelo navegador.',
        'target_url' => 'challenges/ssrf.php',
        'flag'       => 'FLAG{ssrf_1nt3rn4l_s3rv1c3_pwn3d}',
        'hint1'      => 'A requisição é feita pelo servidor, não pelo seu navegador. Tente apontar a URL para o próprio servidor (127.0.0.1).',
        'hint2'      => 'Tente a URL: http://127.0.0.1/api/secret/?token=internal — esse endpoint retorna 403 pelo navegador, mas o servidor pode acessá-lo.',
        'mitigation' => 'Valide e filtre URLs de entrada com allowlist de domínios. Bloqueie requisições para IPs privados (127.x, 10.x, 192.168.x, 172.16-31.x). Use bibliotecas dedicadas como SafeCURL.',
    ],
    'bruteforce_09' => [
        'id'         => 'bruteforce_09',
        'title'      => 'Brute Force & Enumeração de Usuários',
        'category'   => 'Auth Failures',
        'difficulty' => 'Médio',
        'points'     => 150,
        'description'=> 'O portal de folha de pagamento retorna mensagens de erro diferentes para "usuário inexistente" e "senha incorreta", permitindo enumerar usuários válidos. Sem rate limiting, um ataque de força bruta é viável.',
        'target_url' => 'challenges/bruteforce.php',
        'flag'       => 'FLAG{brut3f0rc3_n0_r4t3_l1m1t}',
        'hint1'      => 'Tente nomes de usuário diferentes e observe se a mensagem de erro muda. Isso revela quais usuários existem no sistema.',
        'hint2'      => 'O usuário "carlos" existe no sistema. Tente as senhas da wordlist exibida na página até encontrar a correta.',
        'mitigation' => 'Use mensagens genéricas ("Credenciais inválidas"), implemente rate limiting (máx. 5 tentativas/15min por IP), adicione CAPTCHA após falhas e considere autenticação multifator.',
    ],
];

// ================================================================
// FUNÇÕES DE PONTUAÇÃO
// ================================================================

function getTotalScore(): int {
    global $CHALLENGES;
    $score = 0;
    foreach (getProgress() as $chId) {
        if (isset($CHALLENGES[$chId])) {
            $score += $CHALLENGES[$chId]['points'];
        }
    }
    return $score;
}

function getTotalPossibleScore(): int {
    global $CHALLENGES;
    $total = 0;
    foreach ($CHALLENGES as $ch) {
        $total += $ch['points'];
    }
    return $total;
}
