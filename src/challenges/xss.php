<?php
declare(strict_types=1);

$pageTitle = 'Desafio 3: Cross-Site Scripting (XSS)';
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['xss_03'];
$solved    = isSolved('xss_03');

$pdo = getDb();

// Postar mensagem no mural (Stored XSS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_comment') {
    $author  = trim((string)($_POST['author'] ?? 'Anônimo'));
    $message = trim((string)($_POST['message'] ?? ''));

    if (!empty($message)) {
        // Armazenado diretamente sem sanitização — vulnerabilidade proposital
        $stmt = $pdo->prepare('INSERT INTO guestbook (author, message) VALUES (:author, :msg)');
        $stmt->execute([':author' => $author, ':msg' => $message]);
    }
}

// Limpar mural
if (isset($_GET['clear']) && $_GET['clear'] === '1') {
    $pdo->exec("DELETE FROM guestbook WHERE author NOT IN ('Admin', 'Suporte')");
    header('Location: /challenges/xss.php');
    exit;
}

// Busca refletida (Reflected XSS)
$searchQuery = (string)($_GET['q'] ?? '');

// Carregar mensagens
$comments = $pdo->query('SELECT * FROM guestbook ORDER BY id DESC LIMIT 20')->fetchAll();
?>

<!-- =====================================================================
     NOTA PARA DESENVOLVEDORES: Corrigir escape de saída antes do deploy!
     FLAG_HIDDEN: FLAG{xss_4l3rt_b0und4ry_br34k}
     ===================================================================== -->

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

    <!-- Instrução Educacional -->
    <div class="p-4 rounded-2xl bg-cyan-950/30 border border-cyan-500/30 flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-cyan-400 mt-0.5 shrink-0"></i>
        <div class="text-sm text-slate-300 leading-relaxed">
            <strong class="text-cyan-400">Como funciona:</strong> Injete um payload XSS no campo de busca ou no mural. Quando o JavaScript for executado, isso confirma a vulnerabilidade.
            A flag está escondida no <strong class="text-white">código-fonte desta página</strong> — inspecione com
            <kbd class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-200 font-mono text-xs">Ctrl+U</kbd> ou
            <kbd class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-200 font-mono text-xs">F12 → Elements</kbd>
            e procure por <code class="text-emerald-400 text-xs">FLAG_HIDDEN</code>.
        </div>
    </div>

    <!-- Descrição -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-3">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-code text-cyan-400"></i> Cenário Vulnerável
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            Esta aplicação possui dois vetores de injeção de script:<br>
            1. <strong>XSS Refletido</strong>: O campo de busca imprime o termo diretamente no DOM.<br>
            2. <strong>XSS Armazenado (Stored)</strong>: Os comentários são gravados no banco e renderizados sem <code class="text-emerald-400">htmlspecialchars()</code>.
        </p>
    </div>

    <!-- Aplicação Alvo -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
        <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-community.internal</span>
            </div>
            <a href="?clear=1" class="text-xs text-rose-400 hover:text-rose-300 font-mono flex items-center gap-1">
                <i class="fa-solid fa-trash-can"></i> Limpar Mural
            </a>
        </div>

        <div class="p-8 space-y-8">
            <!-- Barra de Busca (Reflected XSS) -->
            <form method="GET" class="space-y-3">
                <label class="block text-xs font-mono uppercase text-slate-400">Pesquisar Tópicos no Fórum (Reflected XSS)</label>
                <div class="flex gap-2">
                    <input type="text" name="q" placeholder='Ex: <script>alert(1)</script>'
                        value="<?= htmlspecialchars($searchQuery) ?>"
                        class="flex-grow px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono text-sm outline-none focus:border-cyan-500">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-slate-950 font-bold text-sm transition-colors">
                        Pesquisar
                    </button>
                </div>
            </form>

            <?php if (!empty($searchQuery)): ?>
                <!-- VULNERABILIDADE PROPOSITAL: Renderização sem escape -->
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-sm">
                    <span class="text-slate-400">Resultados da pesquisa para: </span>
                    <span class="text-cyan-400 font-semibold"><?= $searchQuery ?></span>
                    <span class="text-xs text-slate-500 block mt-1">(Nenhum tópico correspondente encontrado)</span>
                </div>
            <?php endif; ?>

            <div class="border-t border-slate-800 pt-6">
                <h3 class="text-lg font-bold text-white mb-4">Mural de Mensagens da Empresa (Stored XSS)</h3>

                <!-- Formulário de Comentário -->
                <form method="POST" class="space-y-4 mb-8 bg-slate-950/60 p-5 rounded-xl border border-slate-800">
                    <input type="hidden" name="action" value="post_comment">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-mono text-slate-400 mb-1">Seu Nome / Cargo</label>
                            <input type="text" name="author" placeholder="ex: João Silva" required
                                class="w-full px-4 py-2 rounded-lg bg-slate-950 border border-slate-700 text-white text-sm outline-none focus:border-emerald-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-mono text-slate-400 mb-1">Mensagem (campo inseguro)</label>
                            <input type="text" name="message" placeholder="Escreva algo ou injete HTML/JavaScript..." required
                                class="w-full px-4 py-2 rounded-lg bg-slate-950 border border-slate-700 text-white text-sm outline-none focus:border-emerald-500">
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm transition-all shadow-md">
                            Publicar Recado
                        </button>
                    </div>
                </form>

                <!-- Lista de Comentários — SEM ESCAPE (vulnerabilidade proposital) -->
                <div class="space-y-3">
                    <?php foreach ($comments as $c): ?>
                        <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 space-y-1">
                            <div class="flex items-center justify-between text-xs">
                                <!-- VULNERABILIDADE: author e message sem htmlspecialchars -->
                                <span class="font-bold text-emerald-400"><?= $c['author'] ?></span>
                                <span class="text-slate-500 font-mono"><?= $c['created_at'] ?></span>
                            </div>
                            <div class="text-slate-200 text-sm break-words">
                                <?= $c['message'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Encontrou a Flag no Código-Fonte? Submeta Aqui:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="xss-flag-input" placeholder="FLAG{...}"
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('xss_03', 'xss-flag-input')"
                class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-paper-plane"></i> Validar Flag
            </button>
        </div>
    </div>

    <!-- Dicas Progressivas & Mitigação -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-amber-400 flex items-center gap-2">
                <i class="fa-solid fa-lightbulb"></i> Payloads & Dicas
            </h4>
            <div class="text-xs text-slate-300 space-y-3 font-mono">
                <p class="text-emerald-400 font-semibold">Dica 1:</p>
                <p>• <?= htmlspecialchars($challenge['hint1']) ?></p>
                <div id="hint2-wrapper-xss_03">
                    <button id="hint2-btn-xss_03" onclick="revealHint2('xss_03')"
                        class="mt-2 px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 text-xs flex items-center gap-2 hint2-lock-btn opacity-50 cursor-not-allowed"
                        disabled>
                        <i class="fa-solid fa-lock text-amber-400"></i> Revelar Dica 2 (tente primeiro)
                    </button>
                    <div id="hint2-xss_03" class="hidden mt-2 p-3 rounded-lg bg-amber-950/30 border border-amber-500/30">
                        <p class="text-amber-300 font-semibold mb-1">Dica 2:</p>
                        <p class="text-slate-300">• <?= htmlspecialchars($challenge['hint2']) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-emerald-400 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Como Mitigar (PHP & CSP)
            </h4>
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto leading-relaxed">// CÓDIGO SEGURO:
&lt;?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?&gt;

// CABEÇALHOS CSP NO APACHE:
Header set Content-Security-Policy \
  "default-src 'self';"</pre>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => { initHint2State('xss_03'); });
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
