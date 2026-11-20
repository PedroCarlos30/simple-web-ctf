<?php
declare(strict_types=1);

$pageTitle = "Desafio 6: Command Injection (RCE)";
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['cmdi_06'];
$solved = isSolved('cmdi_06');

$targetHost = $_POST['host'] ?? '127.0.0.1';
$commandOutput = '';
$executedCmd = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ping') {
    // VULNERABILIDADE PROPOSITAL: Concatenação direta no shell do Linux
    $executedCmd = "ping -c 2 " . $targetHost . " 2>&1";
    $commandOutput = shell_exec($executedCmd);
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

    <!-- Banner de Resolvido se aplicável -->
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
            <i class="fa-solid fa-terminal text-cyan-400"></i> Cenário Vulnerável: Injeção de Comandos do SO
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            Esta ferramenta de diagnóstico executa o binário do sistema <code class="text-emerald-400">ping</code> para validar a conectividade com servidores remotos. Como a entrada não é sanitizada com <code class="text-slate-400">escapeshellarg()</code>, um operador malicioso pode injetar delimitadores de comando do Linux (como <code class="text-cyan-400">;</code> ou <code class="text-cyan-400">&&</code>) e executar comandos arbitrários no container Docker.
        </p>
    </div>

    <!-- Aplicação Alvo: Utilitário de Ping -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
        <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-nettools.internal/ping</span>
            </div>
            <span class="text-xs text-slate-500 font-mono">Linux Bash Shell Engine</span>
        </div>

        <div class="p-8 space-y-6">
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="ping">
                <div>
                    <label class="block text-xs font-mono uppercase text-slate-400 mb-1">Endereço IP ou Hostname para Teste</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="host" value="<?= htmlspecialchars($targetHost) ?>" 
                            placeholder="ex: 127.0.0.1 ou 127.0.0.1; cat ../secret_flag_system.txt" required
                            class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-emerald-400 font-mono text-sm outline-none focus:border-emerald-500">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20">
                            <i class="fa-solid fa-play"></i>
                            Executar Diagnóstico
                        </button>
                    </div>
                </div>
            </form>

            <?php if (!empty($executedCmd)): ?>
                <!-- Comando Executado -->
                <div class="space-y-1">
                    <span class="text-xs font-mono text-slate-400">Linha de Comando Executada no SO:</span>
                    <div class="bg-slate-950 px-4 py-2 rounded-lg border border-slate-800 font-mono text-xs text-cyan-400">
                        $ <?= htmlspecialchars($executedCmd) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Terminal com Saída do Comando -->
            <div class="rounded-2xl terminal-window border border-slate-800 overflow-hidden shadow-inner">
                <div class="bg-slate-950/90 px-4 py-2 border-b border-slate-800/80 flex items-center justify-between text-xs text-slate-500 font-mono">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-laptop-code text-emerald-400"></i>
                        <span>terminal_output.log</span>
                    </div>
                    <span>stdout / stderr</span>
                </div>
                <div class="p-5 font-mono text-xs leading-relaxed overflow-x-auto min-h-[140px] text-emerald-400">
                    <?php if (!empty($commandOutput)): ?>
                        <pre class="whitespace-pre-wrap"><?= htmlspecialchars($commandOutput) ?></pre>
                    <?php else: ?>
                        <span class="text-slate-600 italic">Aguardando execução de diagnóstico de rede...</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Caixa de Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Capturou a Flag RCE via Shell? Submeta Aqui:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="cmdi-flag-input" placeholder="FLAG{...}" 
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('cmdi_06', 'cmdi-flag-input')" 
                class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-paper-plane"></i>
                Validar Flag
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
                <div id="hint2-wrapper-cmdi_06">
                    <button id="hint2-btn-cmdi_06" onclick="revealHint2('cmdi_06')"
                        class="mt-2 px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 text-xs flex items-center gap-2 hint2-lock-btn opacity-50 cursor-not-allowed"
                        disabled>
                        <i class="fa-solid fa-lock text-amber-400"></i> Revelar Dica 2 (tente primeiro)
                    </button>
                    <div id="hint2-cmdi_06" class="hidden mt-2 p-3 rounded-lg bg-amber-950/30 border border-amber-500/30">
                        <p class="text-amber-300 font-semibold mb-1">Dica 2:</p>
                        <p class="text-slate-300">• <?= htmlspecialchars($challenge['hint2']) ?></p>
                        <p class="text-slate-400 mt-1">• Ou liste arquivos: <code class="text-cyan-400">127.0.0.1; ls -la ..</code></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-emerald-400 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Correção Defensiva
            </h4>
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto leading-relaxed">
// 1. VALIDAÇÃO ESTRITA DE IP:
if (!filter_var($host, FILTER_VALIDATE_IP)) {
    die("Formato de IP inválido!");
}

// 2. ESCAPE DE ARGUMENTOS DE SHELL:
$safeHost = escapeshellarg($host);
$output = shell_exec("ping -c 2 " . $safeHost);</pre>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => { initHint2State('cmdi_06'); });
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>

