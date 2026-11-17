<?php
$pageTitle = "Desafio 2: SQL Injection";
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['sqli_02'];
$solved = isSolved('sqli_02');

$loginError = '';
$loggedInUser = null;
$executedQuery = '';

// Processar tentativa de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // VULNERABILIDADE PROPOSITAL: Concatenação direta sem sanitização em SQLite
    $executedQuery = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";

    try {
        $pdo = getDb();
        $stmt = $pdo->query($executedQuery);
        $user = $stmt ? $stmt->fetch() : null;

        if ($user) {
            $loggedInUser = $user;
        } else {
            $loginError = 'Credenciais inválidas! Usuário ou senha incorretos.';
        }
    } catch (PDOException $e) {
        $loginError = 'Erro de sintaxe SQL: ' . $e->getMessage();
    }
}
?>

<div class="max-w-5xl mx-auto px-4 py-8 space-y-8">
    <!-- Cabeçalho do Desafio -->
    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="/index.php" class="text-xs text-slate-400 hover:text-emerald-400 font-mono flex items-center gap-1 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i> Voltar ao Dashboard
                </a>
                <span class="text-slate-600">•</span>
                <span class="text-xs font-mono text-emerald-400 uppercase tracking-wider"><?= $challenge['category'] ?></span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white"><?= htmlspecialchars($challenge['title']) ?></h1>
        </div>
        
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full text-xs font-bold font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30">
                +<?= $challenge['points'] ?> PTS
            </span>
            <span class="px-3 py-1 rounded-full text-xs font-mono font-bold <?= $solved ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700' ?>">
                <?= $solved ? '<i class="fa-solid fa-check"></i> RESOLVIDO' : 'PENDENTE' ?>
            </span>
        </div>
    </div>

    <!-- Banner de Resolvido -->
    <?php if ($solved): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 text-emerald-300 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl text-emerald-400"></i>
                <span class="font-medium text-sm">Desafio Concluído! Flag: <code class="font-mono bg-slate-900 px-2 py-0.5 rounded text-emerald-400 font-bold"><?= $challenge['flag'] ?></code></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Descrição -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-3">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-terminal text-cyan-400"></i> Cenário Vulnerável
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            O formulário de login abaixo conecta-se ao banco de dados SQLite. Como a aplicação interpola diretamente os valores sem utilizar declarações preparadas (Prepared Statements), é possível manipular a lógica booleana da consulta SQL e realizar login como administrador sem saber a senha.
        </p>
    </div>

    <!-- Área de Aplicação: Login ou Painel Logado -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
        <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-auth-service.internal</span>
            </div>
            <span class="text-xs text-slate-500 font-mono">SQLite 3 Database</span>
        </div>

        <div class="p-8">
            <?php if ($loggedInUser): ?>
                <!-- Sucesso: Usuário Logado -->
                <div class="space-y-6">
                    <div class="p-6 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl font-bold">
                                    <i class="fa-solid fa-user-shield"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">Sessão Autenticada com Sucesso!</h3>
                                    <p class="text-xs text-emerald-400 font-mono">Papel: <?= htmlspecialchars($loggedInUser['role']) ?></p>
                                </div>
                            </div>
                            <a href="/challenges/sqli.php" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300 hover:text-white transition-colors">
                                Fazer Logout
                            </a>
                        </div>

                        <div class="bg-slate-950 p-4 rounded-xl border border-slate-800 font-mono text-sm space-y-2">
                            <div class="text-slate-400 text-xs">Dados do Usuário Autenticado:</div>
                            <div class="text-slate-200"><strong class="text-slate-400">Usuário:</strong> <?= htmlspecialchars($loggedInUser['username']) ?></div>
                            <div class="text-slate-200"><strong class="text-slate-400">Notas Confidenciais:</strong></div>
                            <div class="p-3 bg-emerald-900/30 border border-emerald-500/40 rounded-lg text-emerald-300 font-bold break-all">
                                <?= htmlspecialchars($loggedInUser['secret_notes']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Formulário de Login -->
                <div class="max-w-md mx-auto space-y-6">
                    <div class="text-center space-y-2">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-800 flex items-center justify-center text-emerald-400 text-xl border border-slate-700">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Acesso Restrito ao Painel Admin</h3>
                        <p class="text-xs text-slate-400">Insira suas credenciais corporativas</p>
                    </div>

                    <?php if (!empty($loginError)): ?>
                        <div class="p-3 rounded-xl bg-rose-950/40 border border-rose-500/40 text-rose-300 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                            <span><?= htmlspecialchars($loginError) ?></span>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="login">
                        <div>
                            <label class="block text-xs font-mono uppercase text-slate-400 mb-1">Nome de Usuário</label>
                            <input type="text" name="username" placeholder="ex: admin ou ' OR 1=1 --" required
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-emerald-500 text-white font-mono text-sm outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-mono uppercase text-slate-400 mb-1">Senha</label>
                            <input type="password" name="password" placeholder="••••••••"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-emerald-500 text-white font-mono text-sm outline-none">
                        </div>

                        <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm transition-all shadow-lg shadow-emerald-600/20">
                            Entrar no Sistema
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Inspetor da Query Executada (Didático) -->
            <?php if (!empty($executedQuery)): ?>
                <div class="mt-8 pt-6 border-t border-slate-800 space-y-2">
                    <span class="text-xs font-mono uppercase text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-code text-cyan-400"></i> Query SQL Executada no Backend:
                    </span>
                    <pre class="bg-slate-950 p-4 rounded-xl border border-slate-800 text-xs font-mono text-emerald-400 overflow-x-auto"><?= htmlspecialchars($executedQuery) ?></pre>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Caixa de Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Capturou a Flag do Admin? Submeta Aqui:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="sqli-flag-input" placeholder="FLAG{...}" 
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('sqli_02', 'sqli-flag-input')" 
                class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-paper-plane"></i>
                Validar Flag
            </button>
        </div>
    </div>

    <!-- Dicas & Mitigação -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-amber-400 flex items-center gap-2">
                <i class="fa-solid fa-lightbulb"></i> Dicas de Exploração
            </h4>
            <ul class="text-xs text-slate-300 space-y-2 font-mono">
                <li>• No campo de usuário, insira: <code class="text-emerald-400">admin' --</code></li>
                <li>• Ou use a condição universal: <code class="text-emerald-400">' OR 1=1 --</code> com qualquer senha.</li>
                <li>• O <code class="text-cyan-400">--</code> no SQLite comenta o restante da query, ignorando a verificação de senha!</li>
            </ul>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-emerald-400 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Correção Segura (Código PHP)
            </h4>
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto leading-relaxed">
// CÓDIGO CORRETO (PREPARED STATEMENTS):
$stmt = $pdo->prepare(
    "SELECT * FROM users WHERE username = :u AND password = :p"
);
$stmt->execute([':u' => $username, ':p' => $password]);
$user = $stmt->fetch();</pre>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../components/footer.php'; ?>

