<?php
declare(strict_types=1);

$pageTitle = 'Desafio 8: SSRF (Falsificação de Requisição pelo Servidor)';
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['ssrf_08'];
$solved    = isSolved('ssrf_08');

$ssrfUrl      = '';
$ssrfOutput   = '';
$ssrfError    = '';
$ssrfStatus   = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'fetch') {
    $ssrfUrl = trim((string)($_POST['url'] ?? ''));

    if (!empty($ssrfUrl)) {
        // VULNERABILIDADE PROPOSITAL: Nenhuma validação de URL interna/privada
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $ssrfUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_USERAGENT      => 'CyberPulse-LinkChecker/1.0',
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $result   = curl_exec($ch);
        $ssrfStatus = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($result !== false) {
            // Truncar saída para exibição
            $ssrfOutput = mb_substr((string)$result, 0, 3000);
        } else {
            $ssrfError = 'Erro ao buscar URL: ' . $curlError;
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
            <i class="fa-solid fa-server text-cyan-400"></i> Cenário Vulnerável: Server-Side Request Forgery
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            A ferramenta corporativa de verificação de links faz requisições HTTP a partir do <strong>servidor</strong>, não do seu navegador. Sem validação de destino, é possível forçar o servidor a acessar serviços internos que são inacessíveis diretamente pelo browser — incluindo metadados de cloud, APIs internas e endpoints administrativos.
        </p>
        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs font-mono text-slate-400">
            <span class="text-cyan-400">Diferença chave:</span> Quando você acessa <code class="text-rose-400">http://127.0.0.1/api/secret/</code> pelo browser → <span class="text-rose-400">403 Forbidden</span>. Quando o <span class="text-emerald-400">servidor</span> acessa → retorna dados internos.
        </div>
    </div>

    <!-- Ferramenta Vulnerável -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
        <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-linkchecker.internal</span>
            </div>
            <span class="text-xs text-slate-500 font-mono">URL Preview Engine</span>
        </div>

        <div class="p-8 space-y-6">
            <!-- Formulário -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="fetch">
                <div>
                    <label class="block text-xs font-mono uppercase text-slate-400 mb-1">URL para Verificar Disponibilidade</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="url"
                            value="<?= htmlspecialchars($ssrfUrl) ?>"
                            placeholder="https://exemplo.com ou http://127.0.0.1/api/secret/?token=internal"
                            class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-emerald-400 font-mono text-sm outline-none focus:border-emerald-500">
                        <button type="submit" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20">
                            <i class="fa-solid fa-satellite-dish"></i>
                            Verificar Link
                        </button>
                    </div>
                    <p class="text-xs text-slate-500 font-mono mt-1">O servidor fará a requisição HTTP a partir da rede interna do container.</p>
                </div>
            </form>

            <?php if (!empty($ssrfUrl)): ?>
                <!-- Informações da Requisição -->
                <div class="space-y-1">
                    <span class="text-xs font-mono text-slate-400">Requisição realizada pelo servidor:</span>
                    <div class="bg-slate-950 px-4 py-2 rounded-lg border border-slate-800 font-mono text-xs flex items-center justify-between">
                        <span class="text-cyan-400">GET <?= htmlspecialchars($ssrfUrl) ?></span>
                        <?php if ($ssrfStatus > 0): ?>
                            <span class="px-2 py-0.5 rounded text-xs font-bold <?= $ssrfStatus === 200 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' ?>">
                                HTTP <?= $ssrfStatus ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Área de Resposta -->
            <div class="rounded-2xl terminal-window border border-slate-800 overflow-hidden shadow-inner">
                <div class="bg-slate-950/90 px-4 py-2 border-b border-slate-800/80 flex items-center justify-between text-xs text-slate-500 font-mono">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-code text-cyan-400"></i>
                        <span>response_body.txt</span>
                    </div>
                    <span>stdout</span>
                </div>
                <div class="p-5 font-mono text-xs leading-relaxed overflow-x-auto min-h-[140px] text-emerald-400">
                    <?php if (!empty($ssrfError)): ?>
                        <span class="text-rose-400"><?= htmlspecialchars($ssrfError) ?></span>
                    <?php elseif (!empty($ssrfOutput)): ?>
                        <pre class="whitespace-pre-wrap"><?= htmlspecialchars($ssrfOutput) ?></pre>
                    <?php else: ?>
                        <span class="text-slate-600 italic">Aguardando URL para verificar...</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Conseguiu acessar o serviço interno? Submeta a Flag:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="ssrf-flag-input" placeholder="FLAG{...}"
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('ssrf_08', 'ssrf-flag-input')"
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
                <div id="hint2-wrapper-ssrf_08">
                    <button id="hint2-btn-ssrf_08" onclick="revealHint2('ssrf_08')"
                        class="mt-2 px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 text-xs flex items-center gap-2 hint2-lock-btn opacity-50 cursor-not-allowed"
                        disabled>
                        <i class="fa-solid fa-lock text-amber-400"></i> Revelar Dica 2 (tente primeiro)
                    </button>
                    <div id="hint2-ssrf_08" class="hidden mt-2 p-3 rounded-lg bg-amber-950/30 border border-amber-500/30">
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
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto leading-relaxed">// VALIDAR com allowlist de domínios:
$parsed = parse_url($url);
$allowed = ['exemplo.com', 'parceiro.com'];
if (!in_array($parsed['host'], $allowed)) {
    die("Domínio não permitido.");
}

// BLOQUEAR IPs privados:
$ip = gethostbyname($parsed['host']);
if (filter_var($ip, FILTER_VALIDATE_IP,
    FILTER_FLAG_NO_PRIV_RANGE |
    FILTER_FLAG_NO_RES_RANGE) === false) {
    die("IP interno bloqueado.");
}</pre>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => { initHint2State('ssrf_08'); });
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
