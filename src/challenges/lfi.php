<?php
declare(strict_types=1);

$pageTitle = "Desafio 5: Local File Inclusion (LFI)";
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['lfi_05'];
$solved = isSolved('lfi_05');

$doc = $_GET['doc'] ?? 'about.php';
$lfiOutput = '';
$lfiError = '';

// VULNERABILIDADE PROPOSITAL: Concatenação de caminho sem sanitização de traversal (../)
$targetPath = __DIR__ . '/pages/' . $doc;

if (!empty($doc)) {
    // Permitir também caminhos absolutos como /etc/passwd se informados
    if (str_starts_with($doc, '/')) {
        $actualPath = $doc;
    } else {
        $actualPath = $targetPath;
    }

    if (file_exists($actualPath)) {
        // Se for PHP, capturamos a execução via output buffering
        ob_start();
        include $actualPath;
        $lfiOutput = ob_get_clean();
    } else {
        $lfiError = "Arquivo não encontrado no caminho: " . htmlspecialchars($actualPath);
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
            <i class="fa-solid fa-folder-open text-cyan-400"></i> Cenário Vulnerável: Directory Traversal / LFI
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            Este visualizador de documentos corporativos carrega páginas dinamicamente concatenando o parâmetro <code class="text-emerald-400">?doc=</code> à pasta local <code class="text-slate-400">/pages/</code>. Como o código não neutraliza os operadores de diretório pai (<code class="text-emerald-400">../</code>), um atacante pode escapar da pasta e incluir arquivos confidenciais do servidor (como <code class="text-cyan-400">secret_flag_system.txt</code> ou <code class="text-cyan-400">/etc/passwd</code>).
        </p>
    </div>

    <!-- Aplicação Alvo: Visualizador de Documentos -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
        <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-docs.internal/view.php?doc=<?= htmlspecialchars($doc) ?></span>
            </div>
            <span class="text-xs text-slate-500 font-mono">Dynamic Page Loader</span>
        </div>

        <div class="p-8 space-y-6">
            <!-- Navegação Rápida de Documentos Legítimos -->
            <div class="flex flex-wrap items-center justify-between gap-4 p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-400 font-mono">Documentos:</span>
                    <a href="?doc=about.php" class="px-3 py-1 rounded-lg text-xs font-mono <?= $doc === 'about.php' ? 'bg-emerald-600 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 hover:text-white' ?>">
                        about.php
                    </a>
                    <a href="?doc=terms.php" class="px-3 py-1 rounded-lg text-xs font-mono <?= $doc === 'terms.php' ? 'bg-emerald-600 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 hover:text-white' ?>">
                        terms.php
                    </a>
                    <a href="?doc=status.php" class="px-3 py-1 rounded-lg text-xs font-mono <?= $doc === 'status.php' ? 'bg-emerald-600 text-slate-950 font-bold' : 'bg-slate-900 text-slate-300 hover:text-white' ?>">
                        status.php
                    </a>
                </div>

                <!-- Input Customizado de Path Traversal -->
                <form method="GET" class="flex items-center gap-2 flex-grow sm:flex-grow-0">
                    <span class="text-xs font-mono text-slate-400">?doc=</span>
                    <input type="text" name="doc" value="<?= htmlspecialchars($doc) ?>" placeholder="ex: ../../secret_flag_system.txt"
                        class="px-3 py-1 rounded-lg bg-slate-900 border border-slate-700 text-emerald-400 font-mono text-xs w-48 sm:w-64 outline-none focus:border-emerald-500">
                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-xs transition-colors">
                        Carregar
                    </button>
                </form>
            </div>

            <!-- Área de Conteúdo Renderizado -->
            <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800">
                <?php if (!empty($lfiError)): ?>
                    <div class="p-4 rounded-xl bg-rose-950/40 border border-rose-500/40 text-rose-300 text-xs font-mono flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span><?= $lfiError ?></span>
                    </div>
                <?php else: ?>
                    <div class="font-mono text-sm leading-relaxed text-slate-200">
                        <pre class="whitespace-pre-wrap font-mono text-xs bg-slate-900/60 p-4 rounded-xl border border-slate-800 text-emerald-300"><?= htmlspecialchars($lfiOutput) ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Caixa de Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Encontrou a Flag no Arquivo Secreto? Submeta Aqui:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="lfi-flag-input" placeholder="FLAG{...}" 
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('lfi_05', 'lfi-flag-input')" 
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
                <div id="hint2-wrapper-lfi_05">
                    <button id="hint2-btn-lfi_05" onclick="revealHint2('lfi_05')"
                        class="mt-2 px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 text-xs flex items-center gap-2 hint2-lock-btn opacity-50 cursor-not-allowed"
                        disabled>
                        <i class="fa-solid fa-lock text-amber-400"></i> Revelar Dica 2 (tente primeiro)
                    </button>
                    <div id="hint2-lfi_05" class="hidden mt-2 p-3 rounded-lg bg-amber-950/30 border border-amber-500/30">
                        <p class="text-amber-300 font-semibold mb-1">Dica 2:</p>
                        <p class="text-slate-300">• <?= htmlspecialchars($challenge['hint2']) ?></p>
                        <p class="text-slate-400 mt-1">• Também funciona: <code class="text-cyan-400">?doc=/etc/passwd</code></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-emerald-400 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Correção Segura (Whitelist / Basename)
            </h4>
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto leading-relaxed">
// ABORDAGEM 1: WHITELIST RESTRITA (RECOMENDADA)
$allowed = ['about.php', 'terms.php', 'status.php'];
if (!in_array($doc, $allowed, true)) {
    die("Arquivo não permitido!");
}

// ABORDAGEM 2: BASENAME (ELIMINA PATH TRAVERSAL ../)
$safeDoc = basename($_GET['doc']);</pre>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => { initHint2State('lfi_05'); });
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>

