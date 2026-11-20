<?php
declare(strict_types=1);

$pageTitle = 'Desafio 1: Reconhecimento';
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['recon_01'];
$solved    = isSolved('recon_01');
?>

<!-- Comentário do Desenvolvedor (Vazamento de Informação):
     TODO: Não esquecer de apagar o arquivo de backup /challenges/backup_admin_2026.bak
     antes da homologação final! Também listado em /robots.txt
-->

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
            <span class="font-medium text-sm">Parabéns! Você já capturou a flag: <code class="font-mono bg-slate-900 px-2 py-0.5 rounded text-emerald-400 font-bold"><?= $challenge['flag'] ?></code></span>
        </div>
    <?php endif; ?>

    <!-- Descrição -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-3">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-bullseye text-rose-400"></i> Objetivo da Missão
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            Seu objetivo como auditor de segurança é inspecionar este portal corporativo e encontrar informações confidenciais vazadas acidentalmente pelos desenvolvedores. Procure por diretórios protegidos, arquivos de configuração ou comentários ocultos no código-fonte.
        </p>
    </div>

    <!-- Portal Corporativo Simulado -->
    <div class="bg-slate-900 border border-slate-700 rounded-2xl overflow-hidden shadow-2xl">
        <div class="bg-slate-950 px-6 py-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                <span class="text-xs text-slate-400 font-mono ml-2">cyberpulse-portal.internal</span>
            </div>
            <span class="text-xs text-slate-500 font-mono">Simulação de Portal Público</span>
        </div>

        <div class="p-8 space-y-6">
            <div class="max-w-2xl space-y-3">
                <h2 class="text-2xl font-bold text-white">Portal de Serviços & Transparência</h2>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Bem-vindo ao repositório público de serviços institucionais da CyberPulse. Nossos sistemas operam sob padrões rígidos de auditoria.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-2">
                    <i class="fa-solid fa-network-wired text-cyan-400 text-xl"></i>
                    <h4 class="font-bold text-sm text-white">Infraestrutura</h4>
                    <p class="text-xs text-slate-400">Servidores Apache 2.4 com isolamento de contêineres.</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-2">
                    <i class="fa-solid fa-database text-emerald-400 text-xl"></i>
                    <h4 class="font-bold text-sm text-white">Persistência</h4>
                    <p class="text-xs text-slate-400">Bancos de dados locais com políticas de integridade.</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800 space-y-2">
                    <i class="fa-solid fa-lock text-purple-400 text-xl"></i>
                    <h4 class="font-bold text-sm text-white">Auditoria</h4>
                    <p class="text-xs text-slate-400">Rotinas automatizadas de backup e relatórios.</p>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-950/40 border border-dashed border-slate-800 text-xs text-slate-400 flex items-center justify-between gap-4 flex-wrap">
                <span>Dica de Pentest: Pressione <kbd class="px-2 py-0.5 rounded bg-slate-800 text-slate-200 font-mono text-[10px]">Ctrl + U</kbd> para inspecionar os comentários HTML desta página.</span>
                <a href="/robots.txt" target="_blank" class="text-emerald-400 hover:underline flex items-center gap-1 font-mono shrink-0">
                    Acessar /robots.txt <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Submissão da Flag -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Encontrou a Flag? Submeta Aqui:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="recon-flag-input" placeholder="FLAG{...}"
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('recon_01', 'recon-flag-input')"
                class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20">
                <i class="fa-solid fa-paper-plane"></i> Validar Flag
            </button>
        </div>
    </div>

    <!-- Dicas Progressivas & Mitigação -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <h4 class="font-bold text-sm text-amber-400 flex items-center gap-2">
                <i class="fa-solid fa-lightbulb"></i> Dicas Progressivas
            </h4>
            <div class="text-xs text-slate-300 space-y-3 font-mono">
                <p class="text-emerald-400 font-semibold">Dica 1:</p>
                <p>• <?= htmlspecialchars($challenge['hint1']) ?></p>

                <div id="hint2-wrapper-recon_01">
                    <button id="hint2-btn-recon_01"
                        onclick="revealHint2('recon_01')"
                        class="mt-2 px-3 py-1.5 rounded-lg border border-slate-700 text-slate-400 text-xs flex items-center gap-2 hint2-lock-btn opacity-50 cursor-not-allowed"
                        disabled title="Tente submeter uma flag incorreta para desbloquear">
                        <i class="fa-solid fa-lock text-amber-400"></i> Revelar Dica 2 (tente primeiro)
                    </button>
                    <div id="hint2-recon_01" class="hidden mt-2 p-3 rounded-lg bg-amber-950/30 border border-amber-500/30">
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
            <p class="text-xs text-slate-300 leading-relaxed font-mono">
                <?= htmlspecialchars($challenge['mitigation']) ?>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => { initHint2State('recon_01'); });
</script>

<?php require_once __DIR__ . '/../components/footer.php'; ?>
