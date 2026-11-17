<?php
$pageTitle = "Desafio 7: Adulteração de Cookies & Sessão";

// Tratamento de Cookies antes de emitir HTML
$defaultToken = base64_encode(json_encode([
    'user' => 'colaborador_convidado',
    'role' => 'guest',
    'issued_at' => date('Y-m-d H:i:s')
]));

if (isset($_GET['reset_cookie']) && $_GET['reset_cookie'] === '1') {
    setcookie('auth_token', $defaultToken, time() + 86400, '/');
    header("Location: /challenges/crypto.php");
    exit;
}

if (!isset($_COOKIE['auth_token'])) {
    setcookie('auth_token', $defaultToken, time() + 86400, '/');
    $_COOKIE['auth_token'] = $defaultToken;
}

require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['crypto_07'];
$solved = isSolved('crypto_07');

// Decodificar Token Base64 Inseguro
$rawCookie = $_COOKIE['auth_token'] ?? $defaultToken;
$decodedJson = base64_decode($rawCookie);
$tokenPayload = json_decode($decodedJson, true) ?? [];

$currentRole = $tokenPayload['role'] ?? 'guest';
$currentUser = $tokenPayload['user'] ?? 'Desconhecido';
$isAdmin = ($currentRole === 'admin');
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
            <i class="fa-solid fa-cookie-bite text-amber-400"></i> Cenário Vulnerável: Armazenamento Inseguro de Privilégios
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            O cofre de segurança corporativa valida se o operador tem acesso através do cookie de navegador <code class="text-emerald-400">auth_token</code>. Em vez de utilizar uma sessão assinada no servidor ou um JWT com segredo criptográfico HMAC, o sistema apenas serializou um objeto JSON codificado em <strong>Base64</strong>.
        </p>
    </div>

    <!-- Aplicação Alvo: Cofre de Alta Segurança -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
        <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-vault.internal/vip</span>
            </div>
            <a href="?reset_cookie=1" class="text-xs text-slate-400 hover:text-white font-mono flex items-center gap-1">
                <i class="fa-solid fa-rotate-left"></i> Restaurar Cookie Padrão
            </a>
        </div>

        <div class="p-8 space-y-8">
            <?php if ($isAdmin): ?>
                <!-- Cofre Desbloqueado (Admin) -->
                <div class="p-8 rounded-2xl bg-amber-950/30 border border-amber-500/50 space-y-6 text-center">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-4xl shadow-xl shadow-amber-500/20 animate-bounce">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-2xl font-black text-amber-300">Cofre de Alta Segurança Desbloqueado!</h2>
                        <p class="text-slate-300 text-sm">Privilégios de Administrador confirmados através do cookie.</p>
                    </div>

                    <div class="max-w-lg mx-auto bg-slate-950 p-5 rounded-2xl border border-amber-500/30 space-y-2">
                        <span class="text-xs font-mono uppercase text-amber-400 font-bold block">Chave Mestra de Acesso (Flag):</span>
                        <div class="text-emerald-400 font-mono text-base font-black break-all">
                            <?= $challenge['flag'] ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Cofre Bloqueado (Guest) -->
                <div class="p-8 rounded-2xl bg-slate-950 border border-slate-800 space-y-6 text-center">
                    <div class="w-20 h-20 mx-auto rounded-3xl bg-slate-800 flex items-center justify-center text-rose-400 text-4xl border border-slate-700">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div class="space-y-2">
                        <h2 class="text-xl font-bold text-white">Acesso Negado ao Cofre VIP</h2>
                        <p class="text-xs text-slate-400">
                            Identidade atual: <span class="text-slate-200 font-mono font-bold"><?= htmlspecialchars($currentUser) ?></span> | Papel: <span class="text-rose-400 font-mono font-bold"><?= htmlspecialchars($currentRole) ?></span>
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-rose-950/20 border border-rose-500/30 text-rose-300 text-xs font-mono max-w-md mx-auto">
                        ERRO 403: O cofre exige credenciais com <strong class="text-white">"role": "admin"</strong>.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Inspetor e Modificador de Cookie em Tempo Real -->
            <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-mono uppercase text-slate-400 font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass-chart text-cyan-400"></i> Inspetor de Token (Cookie auth_token)
                    </span>
                    <span class="text-xs text-slate-500 font-mono">Modifique via DevTools (F12) ou abaixo</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Valor Bruto em Base64 -->
                    <div class="space-y-1">
                        <label class="text-[11px] font-mono text-slate-400">Valor Bruto do Cookie (Base64):</label>
                        <textarea id="cookieRaw" readonly class="w-full h-24 p-3 rounded-xl bg-slate-900 border border-slate-800 text-cyan-400 font-mono text-xs outline-none resize-none"><?= htmlspecialchars($rawCookie) ?></textarea>
                    </div>

                    <!-- JSON Decodificado -->
                    <div class="space-y-1">
                        <label class="text-[11px] font-mono text-slate-400">Conteúdo JSON Decodificado:</label>
                        <textarea id="cookieJson" class="w-full h-24 p-3 rounded-xl bg-slate-900 border border-slate-800 text-emerald-400 font-mono text-xs outline-none"><?= htmlspecialchars(json_encode($tokenPayload, JSON_PRETTY_PRINT)) ?></textarea>
                    </div>
                </div>

                <!-- Botão Auxiliar para Salvar Cookie Modificado -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button onclick="applyTamperedCookie()" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-slate-950 font-bold text-xs flex items-center gap-2 transition-all shadow-md">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        Codificar e Aplicar Cookie ao Navegador
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Caixa de Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Desbloqueou o Cofre? Submeta a Flag Aqui:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="crypto-flag-input" placeholder="FLAG{...}" 
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('crypto_07', 'crypto-flag-input')" 
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
                <i class="fa-solid fa-lightbulb"></i> Passo a Passo da Exploração
            </h4>
            <ul class="text-xs text-slate-300 space-y-2 font-mono">
                <li>1. No editor de JSON acima, altere <code class="text-rose-400">"role": "guest"</code> para <code class="text-emerald-400">"role": "admin"</code>.</li>
                <li>2. Clique em <strong>"Codificar e Aplicar Cookie"</strong> para atualizar o cookie no navegador.</li>
                <li>3. Ou faça pelo DevTools (F12) -> Application -> Cookies -> alterando o valor de <code class="text-cyan-400">auth_token</code>!</li>
            </ul>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-emerald-400 flex items-center gap-2">
                <i class="fa-solid fa-shield-halved"></i> Correção Segura
            </h4>
            <pre class="bg-slate-950 p-3 rounded-xl border border-slate-800 text-[11px] font-mono text-slate-300 overflow-x-auto leading-relaxed">
// NUNCA ARMAZENE ROLES EM COOKIES CRUS!
// UTILIZE SESSÕES DO SERVIDOR OU JWT ASSINADO:
$jwt = JWT::encode($payload, $SECRET_KEY, 'HS256');

// E SEMPRE CONFIGURE COOKIES COM FLAGS SEGURAS:
setcookie('session_id', $val, [
    'httponly' => true,
    'secure' => true,
    'samesite' => 'Strict'
]);</pre>
        </div>
    </div>
</div>

<script>
function applyTamperedCookie() {
    try {
        const jsonText = document.getElementById('cookieJson').value;
        const parsed = JSON.parse(jsonText);
        const encoded = btoa(JSON.stringify(parsed));
        document.cookie = `auth_token=${encoded}; path=/`;
        showToast('Cookie adulterado gravado com sucesso! Recarregando página...', 'success');
        setTimeout(() => window.location.reload(), 800);
    } catch(e) {
        showToast('JSON inválido! Verifique a sintaxe.', 'error');
    }
}
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>

