<?php
declare(strict_types=1);

$pageTitle = 'Desafio 4: IDOR (Controle de Acesso Quebrado)';
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['idor_04'];
$solved    = isSolved('idor_04');

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 1;

$pdo = getDb();
// VULNERABILIDADE PROPOSITAL: Nenhuma validação se o usuário atual tem permissão
$stmt = $pdo->prepare('SELECT * FROM profiles WHERE id = :id');
$stmt->execute([':id' => $userId]);
$profile = $stmt->fetch();

// HTTP 404 correto para IDs inexistentes
if ($profile === false) {
    http_response_code(404);
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

    <?php if ($solved): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 text-emerald-300 flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl text-emerald-400"></i>
            <span class="font-medium text-sm">Desafio Concluído! Flag: <code class="font-mono bg-slate-900 px-2 py-0.5 rounded text-emerald-400 font-bold"><?= $challenge['flag'] ?></code></span>
        </div>
    <?php endif; ?>

    <!-- Descrição -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-3">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-id-card-clip text-cyan-400"></i> Cenário Vulnerável: IDOR (Insecure Direct Object Reference)
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            Você está logado no sistema como <strong>Carlos Silva (ID: 1)</strong>, um estagiário de TI. A aplicação carrega dados pelo parâmetro <code class="text-emerald-400">?user_id=1</code>. O backend não verifica se você possui autorização para consultar outros funcionários.
        </p>
    </div>

    <!-- Visualizador de Perfil -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
        <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-hr.internal/profile?user_id=<?= $userId ?></span>
            </div>
            <span class="text-xs text-slate-500 font-mono">Controle de Acesso Restrito</span>
        </div>

        <div class="p-8 space-y-6">
            <!-- Seletor de parâmetro -->
            <form method="GET" class="p-4 rounded-xl bg-slate-950 border border-slate-800 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-mono uppercase text-slate-400">Parâmetro de Consulta:</span>
                    <div class="flex items-center bg-slate-900 border border-slate-700 rounded-lg px-3 py-1 font-mono text-sm">
                        <span class="text-slate-500">?user_id=</span>
                        <input type="number" name="user_id" value="<?= $userId ?>" class="w-16 bg-transparent text-emerald-400 font-bold outline-none text-center">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-xs transition-colors">
                        Requisitar Perfil
                    </button>
                    <a href="/api/idor_user.php?id=<?= $userId ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300 hover:text-white font-mono flex items-center gap-1">
                        <i class="fa-solid fa-code"></i> API JSON
                    </a>
                </div>
            </form>

            <?php if ($profile !== false): ?>
                <!-- Cartão do Perfil -->
                <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 space-y-6">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr <?= $profile['id'] == 3 ? 'from-amber-500 to-rose-500' : 'from-emerald-500 to-cyan-500' ?> flex items-center justify-center text-slate-950 font-black text-2xl shadow-lg">
                                <?= strtoupper(substr((string)$profile['full_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl font-bold text-white"><?= htmlspecialchars((string)$profile['full_name']) ?></h3>
                                    <?php if ($profile['id'] == 3): ?>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono bg-amber-500/20 text-amber-400 border border-amber-500/40">VIP / C-LEVEL</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs font-mono text-slate-400"><?= htmlspecialchars((string)$profile['email']) ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-slate-500 font-mono">Registro ID:</span>
                            <span class="text-sm font-bold font-mono text-emerald-400 ml-1">#00<?= $profile['id'] ?></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm font-mono">
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <span class="text-xs text-slate-400 block mb-1">Cargo / Função:</span>
                            <span class="text-white font-semibold"><?= htmlspecialchars((string)$profile['role']) ?></span>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <span class="text-xs text-slate-400 block mb-1">Remuneração Registrada:</span>
                            <span class="text-emerald-400 font-bold"><?= htmlspecialchars((string)$profile['salary']) ?></span>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                        <span class="text-xs text-slate-400 block font-mono flex items-center gap-1.5">
                            <i class="fa-solid fa-lock text-rose-400"></i> Dados Confidenciais Internos:
                        </span>
                        <div class="p-3 rounded-lg <?= $profile['id'] == 3 ? 'bg-amber-950/40 border border-amber-500/40 text-amber-300 font-bold' : 'bg-slate-950 text-slate-300' ?> text-xs font-mono break-all">
                            <?= htmlspecialchars((string)$profile['confidential_data']) ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="p-8 text-center font-mono bg-slate-950 rounded-xl border border-rose-900/40">
                    <i class="fa-solid fa-triangle-exclamation text-2xl mb-2 block text-rose-400"></i>
                    <p class="text-rose-400 font-bold text-sm">HTTP 404 — Recurso não encontrado</p>
                    <p class="text-slate-500 text-xs mt-1">Nenhum colaborador com o identificador <code class="text-slate-300">#<?= $userId ?></code> foi encontrado no sistema.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Capturou a Flag do Perfil do CEO? Submeta Aqui:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="idor-flag-input" placeholder="FLAG{...}"
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('idor_04', 'idor-flag-input')"
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
                <div id="hint2-wrapper-idor_04">
                    <button id="hint2-btn-idor_04" onclick="revealHint2('idor_04')"
                        class="mt-2 px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 text-xs flex items-center gap-2 hint2-lock-btn opacity-50 cursor-not-allowed"
                        disabled>
                        <i class="fa-solid fa-lock text-amber-400"></i> Revelar Dica 2 (tente primeiro)
                    </button>
                    <div id="hint2-idor_04" class="hidden mt-2 p-3 rounded-lg bg-amber-950/30 border border-amber-500/30">
                        <p class="text-amber-300 font-semibold mb-1">Dica 2:</p>
                        <p class="text-slate-300">• <?= htmlspecialchars($challenge['hint2']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-emerald-400 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Como Mitigar (Controle de Acesso)
            </h4>
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto leading-relaxed">// CÓDIGO SEGURO:
$currentUserId = $_SESSION['user_id'];
// Só permite acesso ao próprio perfil ou admins:
if ($reqId != $currentUserId && !isAdmin()) {
    http_response_code(403);
    die("Acesso Negado.");
}</pre>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => { initHint2State('idor_04'); });
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
