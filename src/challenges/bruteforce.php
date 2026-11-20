<?php
declare(strict_types=1);

$pageTitle = 'Desafio 9: Brute Force & Enumeração de Usuários';
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['bruteforce_09'];
$solved    = isSolved('bruteforce_09');

$loginMsg     = '';
$loginType    = ''; // 'success' | 'user_not_found' | 'wrong_password'
$loggedInUser = null;
$attemptCount = 0;

// Wordlist de senhas comuns (exibida na página como "arquivo capturado")
$commonPasswords = [
    'password123', '123456', 'admin', 'qwerty', 'letmein',
    'monkey', 'dragon', 'master', 'abc123', 'iloveyou',
    'sunshine', 'princess', 'welcome', 'shadow', 'password',
    '123456789', 'football', 'baseball', 'soccer', 'charlie',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $username    = trim((string)($_POST['username'] ?? ''));
    $password    = (string)($_POST['password'] ?? '');
    $attemptCount = max(1, (int)($_POST['attempt_count'] ?? 1));

    if (!empty($username)) {
        $pdo = getDb();

        // VULNERABILIDADE PROPOSITAL: mensagens de erro diferenciadas permitem enumeração
        // Buscar apenas por username primeiro
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :user');
        $stmt->execute([':user' => $username]);
        $user = $stmt->fetch();

        if ($user === false) {
            // Usuário não existe
            $loginMsg  = 'Usuário não encontrado no sistema.';
            $loginType = 'user_not_found';
        } elseif ($user['password'] !== $password) {
            // Usuário existe, senha incorreta
            $loginMsg  = 'Senha incorreta para este usuário.';
            $loginType = 'wrong_password';
        } else {
            // Login bem-sucedido
            $loggedInUser = $user;
            $loginType    = 'success';
        }
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
                <span class="text-xs font-mono text-emerald-400 uppercase tracking-wider"><?= htmlspecialchars($challenge['category']) ?></span>
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

    <?php if ($solved): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 text-emerald-300 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl text-emerald-400"></i>
            <span class="font-medium text-sm">Desafio Concluído! Flag: <code class="font-mono bg-slate-900 px-2 py-0.5 rounded text-emerald-400 font-bold"><?= $challenge['flag'] ?></code></span>
        </div>
    <?php endif; ?>

    <!-- Descrição -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-3">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-cyan-400"></i> Cenário Vulnerável: Ausência de Rate Limiting & Enumeração
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            O portal de folha de pagamento retorna mensagens de erro <strong>diferentes</strong> para usuário inexistente vs. senha incorreta. Isso permite enumerar nomes de usuário válidos sem autenticação. Combinado com a ausência de rate limiting e bloqueio por tentativas, um ataque de força bruta manual ou automatizado é trivial.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Portal de Login -->
        <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
            <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                    <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                    <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-payroll.internal</span>
                </div>
                <span id="attempt-counter-display" class="text-xs text-slate-500 font-mono">
                    Tentativas: <span id="attempt-num" class="text-amber-400 font-bold"><?= $attemptCount ?></span>
                </span>
            </div>

            <div class="p-8">
                <?php if ($loggedInUser !== null): ?>
                    <!-- Sucesso -->
                    <div class="space-y-5">
                        <div class="p-6 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-user-check"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">Acesso Concedido!</h3>
                                    <p class="text-xs text-emerald-400 font-mono">Usuário: <?= htmlspecialchars((string)$loggedInUser['username']) ?></p>
                                </div>
                            </div>
                            <div class="bg-slate-950 p-4 rounded-xl border border-emerald-500/30 font-mono text-sm space-y-2">
                                <p class="text-xs text-slate-400">Dados do Colaborador:</p>
                                <p class="text-slate-200"><strong class="text-slate-400">Cargo:</strong> <?= htmlspecialchars((string)$loggedInUser['role']) ?></p>
                                <p class="text-slate-200"><strong class="text-slate-400">Notas Sigilosas:</strong></p>
                                <div class="p-3 bg-emerald-900/30 border border-emerald-500/40 rounded-lg text-emerald-300 font-bold break-all">
                                    <?= htmlspecialchars((string)$loggedInUser['secret_notes']) ?>
                                </div>
                            </div>
                        </div>
                        <a href="/challenges/bruteforce.php" class="block text-center px-4 py-2 rounded-lg bg-slate-800 text-slate-300 text-sm hover:bg-slate-700 transition-colors">
                            Tentar Novamente
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Formulário de Login -->
                    <div class="space-y-5">
                        <div class="text-center space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-800 flex items-center justify-center text-cyan-400 text-xl border border-slate-700">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <h3 class="text-xl font-bold text-white">Portal Folha de Pagamento</h3>
                            <p class="text-xs text-slate-400">Acesso restrito a colaboradores</p>
                        </div>

                        <?php if (!empty($loginMsg)): ?>
                            <div class="p-3 rounded-xl border text-xs flex items-center gap-2 font-mono
                                <?= $loginType === 'user_not_found'
                                    ? 'bg-orange-950/40 border-orange-500/40 text-orange-300'
                                    : 'bg-rose-950/40 border-rose-500/40 text-rose-300' ?>">
                                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                                <span><?= htmlspecialchars($loginMsg) ?></span>
                                <?php if ($loginType === 'user_not_found'): ?>
                                    <span class="ml-auto text-orange-400 font-bold">← Enumera usuários!</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="bruteforce-form" class="space-y-4">
                            <input type="hidden" name="action" value="login">
                            <input type="hidden" name="attempt_count" id="attempt-count-field" value="<?= $attemptCount ?>">
                            <div>
                                <label class="block text-xs font-mono uppercase text-slate-400 mb-1">Nome de Usuário</label>
                                <input type="text" name="username" id="bf-username"
                                    value="<?= htmlspecialchars((string)($_POST['username'] ?? '')) ?>"
                                    placeholder="ex: admin, carlos, guest..."
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-emerald-500 text-white font-mono text-sm outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-mono uppercase text-slate-400 mb-1">Senha</label>
                                <input type="password" name="password" id="bf-password"
                                    placeholder="••••••••"
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 focus:border-emerald-500 text-white font-mono text-sm outline-none">
                            </div>
                            <button type="submit"
                                class="w-full py-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-slate-950 font-bold text-sm transition-all">
                                Acessar Sistema
                            </button>
                        </form>

                        <!-- Terminal de Log de Tentativas -->
                        <div class="rounded-xl terminal-window border border-slate-800 overflow-hidden">
                            <div class="bg-slate-950/90 px-3 py-1.5 border-b border-slate-800 flex items-center gap-2 text-xs text-slate-500 font-mono">
                                <i class="fa-solid fa-terminal text-emerald-400 text-xs"></i>
                                <span>auth_attempts.log</span>
                            </div>
                            <div id="attempt-log" class="p-3 font-mono text-xs space-y-0.5 max-h-28 overflow-y-auto text-slate-400 min-h-[60px]">
                                <?php if (!empty($loginMsg)): ?>
                                    <div class="<?= $loginType === 'success' ? 'text-emerald-400' : 'text-rose-400' ?>">
                                        [<?= date('H:i:s') ?>] user=<?= htmlspecialchars((string)($_POST['username'] ?? '')) ?> → <?= htmlspecialchars($loginMsg) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-600 italic">Aguardando tentativas de login...</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Wordlist Capturada -->
        <div class="space-y-4">
            <div class="bg-slate-900 border border-amber-500/30 rounded-2xl overflow-hidden shadow-2xl">
                <div class="bg-amber-950/30 px-5 py-3 border-b border-amber-500/30 flex items-center gap-2">
                    <i class="fa-solid fa-file-code text-amber-400"></i>
                    <span class="text-xs font-mono text-amber-400 font-bold">rockyou_top20.txt — Wordlist Capturada</span>
                </div>
                <div class="p-4 font-mono text-xs space-y-0.5">
                    <?php foreach ($commonPasswords as $i => $pwd): ?>
                        <div class="flex items-center gap-3 py-0.5 hover:bg-slate-800/60 px-2 rounded cursor-pointer group"
                             onclick="document.getElementById('bf-password').value='<?= htmlspecialchars($pwd) ?>'; showToast('Senha aplicada: <?= htmlspecialchars($pwd) ?>', 'success');">
                            <span class="text-slate-600 w-4 text-right"><?= $i + 1 ?>.</span>
                            <span class="text-emerald-400 group-hover:text-white transition-colors"><?= htmlspecialchars($pwd) ?></span>
                            <span class="ml-auto text-slate-700 group-hover:text-slate-500 text-[10px]">↵ aplicar</span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="px-4 pb-3 text-[10px] text-slate-600 font-mono">
                    Clique em uma senha para aplicá-la automaticamente no campo de login.
                </div>
            </div>

            <!-- Instrução educacional -->
            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 text-xs text-slate-400 font-mono space-y-2">
                <p class="text-cyan-400 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-info-circle"></i> Como explorar:</p>
                <ol class="space-y-1 text-slate-300 list-decimal list-inside">
                    <li>Tente <code class="text-orange-400">inexistente</code> → observe a mensagem de erro</li>
                    <li>Tente <code class="text-cyan-400">admin</code> → observe a diferença</li>
                    <li>Use os usuários conhecidos e teste senhas da wordlist</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Conseguiu acesso? Submeta a Flag:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="bruteforce-flag-input" placeholder="FLAG{...}"
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('bruteforce_09', 'bruteforce-flag-input')"
                class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-paper-plane"></i> Validar Flag
            </button>
        </div>
    </div>

    <!-- Dicas Progressivas & Mitigação -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-amber-400 flex items-center gap-2">
                <i class="fa-solid fa-lightbulb"></i> Dicas de Exploração
            </h4>
            <div class="text-xs text-slate-300 space-y-3 font-mono">
                <p class="text-emerald-400 font-semibold">Dica 1:</p>
                <p>• <?= htmlspecialchars($challenge['hint1']) ?></p>
                <div id="hint2-wrapper-bruteforce_09">
                    <button id="hint2-btn-bruteforce_09" onclick="revealHint2('bruteforce_09')"
                        class="mt-2 px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 text-xs flex items-center gap-2 hint2-lock-btn opacity-50 cursor-not-allowed"
                        disabled>
                        <i class="fa-solid fa-lock text-amber-400"></i> Revelar Dica 2 (tente primeiro)
                    </button>
                    <div id="hint2-bruteforce_09" class="hidden mt-2 p-3 rounded-lg bg-amber-950/30 border border-amber-500/30">
                        <p class="text-amber-300 font-semibold mb-1">Dica 2:</p>
                        <p class="text-slate-300">• <?= htmlspecialchars($challenge['hint2']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-emerald-400 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Correção Defensiva
            </h4>
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto leading-relaxed">// MENSAGEM GENÉRICA (não enuemra usuários):
die("Credenciais inválidas.");

// RATE LIMITING simples (PHP + sessão):
$_SESSION['attempts'] = ($_SESSION['attempts'] ?? 0) + 1;
if ($_SESSION['attempts'] > 5) {
    sleep(2); // penalidade crescente
    die("Muitas tentativas. Tente em 15 min.");
}

// Use também: CAPTCHA e MFA</pre>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    initHint2State('bruteforce_09');

    // Contador de tentativas no lado do cliente
    const form = document.getElementById('bruteforce-form');
    if (form) {
        form.addEventListener('submit', () => {
            const countField = document.getElementById('attempt-count-field');
            const currentCount = parseInt(countField.value || '0', 10);
            countField.value = String(currentCount + 1);
        });
    }
});
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
