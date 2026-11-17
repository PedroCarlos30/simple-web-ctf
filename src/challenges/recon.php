<?php
$pageTitle = "Desafio 1: Reconhecimento";
require_once __DIR__ . '/../components/header.php';

$challenge = $CHALLENGES['recon_01'];
$solved = isSolved('recon_01');
?>

<!-- Comentário do Desenvolvedor (Vazamento de Informação):
     TODO: Não esquecer de apagar o arquivo de backup /challenges/backup_admin_2026.bak.php 
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

    <!-- Banner de Resolvido se aplicável -->
    <?php if ($solved): ?>
        <div class="p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 text-emerald-300 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-xl text-emerald-400"></i>
                <span class="font-medium text-sm">Parabéns! Você já capturou a flag deste desafio: <code class="font-mono bg-slate-900 px-2 py-0.5 rounded text-emerald-400 font-bold"><?= $challenge['flag'] ?></code></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- Descrição e Missão -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <i class="fa-solid fa-bullseye text-rose-400"></i> Objetivo da Missão
        </h3>
        <p class="text-slate-300 text-sm leading-relaxed">
            Seu objetivo como auditor de segurança é inspecionar este portal corporativo e encontrar informações confidenciais vazadas acidentalmente pelos desenvolvedores. Procure por diretórios protegidos, arquivos de configuração ou comentários ocultos no código-fonte.
        </p>
    </div>

    <!-- Aplicação Alvo: Portal Corporativo -->
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

            <!-- Dica visual para quem inspeciona -->
            <div class="p-4 rounded-xl bg-slate-950/40 border border-dashed border-slate-800 text-xs text-slate-400 flex items-center justify-between">
                <span>Dica de Pentest: Pressione <kbd class="px-2 py-0.5 rounded bg-slate-800 text-slate-200 font-mono text-[10px]">Ctrl + U</kbd> ou abra o <kbd class="px-2 py-0.5 rounded bg-slate-800 text-slate-200 font-mono text-[10px]">F12 (DevTools)</kbd> para inspecionar os comentários HTML desta página.</span>
                <a href="/robots.txt" target="_blank" class="text-emerald-400 hover:underline flex items-center gap-1 font-mono">
                    Acessar /robots.txt <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Caixa de Submissão da Flag do Desafio -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 space-y-4">
        <h4 class="font-bold text-sm text-white flex items-center gap-2">
            <i class="fa-solid fa-key text-emerald-400"></i> Encontrou a Flag? Submeta Aqui:
        </h4>
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="recon-flag-input" placeholder="FLAG{...}" 
                class="flex-grow px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono placeholder:text-slate-600 focus:border-emerald-500 outline-none">
            <button onclick="submitChallengeFlag('recon_01', 'recon-flag-input')" 
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
                <i class="fa-solid fa-lightbulb"></i> Dicas Progressivas
            </h4>
            <ul class="text-xs text-slate-300 space-y-2 font-mono">
                <li>1. Acesse o arquivo <code class="text-emerald-400">/robots.txt</code> no navegador.</li>
                <li>2. Observe quais arquivos ou extensões (.bak) foram desautorizados para robôs de busca.</li>
            </ul>
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

<?php require_once __DIR__ . '/../components/footer.php'; ?>

