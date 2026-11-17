<?php
require_once __DIR__ . '/config.php';

$totalScore = getTotalScore();
$maxScore = getTotalPossibleScore();
$solvedCount = count($_SESSION['solved_flags']);
$totalChallenges = count($CHALLENGES);
$progressPercent = $totalChallenges > 0 ? round(($solvedCount / $totalChallenges) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CyberPulse CTF Lab | Plataforma de Cibersegurança</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        cyber: {
                            bg: '#0a0d14',
                            card: '#111726',
                            border: '#1e293b',
                            accent: '#10b981',
                            neon: '#06b6d4',
                            danger: '#ef4444',
                            warning: '#f59e0b',
                            purple: '#8b5cf6'
                        }
                    },
                    fontFamily: {
                        mono: ['JetBrains Mono', 'Fira Code', 'Courier New', 'monospace']
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Conexão com Estilo Personalizado Local -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans selection:bg-emerald-500 selection:text-black">

    <!-- Navbar Superior -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-slate-950/80 border-b border-slate-800/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Nome -->
                <div class="flex items-center gap-3">
                    <a href="index.php" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-600 flex items-center justify-center text-black font-black text-xl shadow-lg shadow-emerald-500/20 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-shield-halved text-slate-950"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-lg tracking-wider bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400">
                                CYBERPULSE
                            </span>
                            <span class="text-xs px-2 py-0.5 ml-1.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-mono">
                                CTF v2.0
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Estatísticas Rápidas & Ações -->
                <div class="flex items-center gap-3 sm:gap-6">
                    <!-- Pontuação -->
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-sm">
                        <i class="fa-solid fa-trophy text-amber-400"></i>
                        <span class="text-slate-400">Pontos:</span>
                        <span id="nav-total-score" class="font-mono font-bold text-amber-400"><?= $totalScore ?></span>
                        <span class="text-xs text-slate-500">/ <?= $maxScore ?></span>
                    </div>

                    <!-- Progresso -->
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-sm">
                        <i class="fa-solid fa-flag text-emerald-400"></i>
                        <span id="nav-solved-count" class="font-mono font-bold text-emerald-400"><?= $solvedCount ?>/<?= $totalChallenges ?></span>
                        <span class="hidden md:inline text-xs text-slate-400">Resolvidos</span>
                    </div>

                    <!-- Botão Submeter Flag Global -->
                    <button onclick="openGlobalFlagModal()" class="px-3 sm:px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center gap-2 transition-all shadow-lg shadow-emerald-600/20 active:scale-95">
                        <i class="fa-solid fa-key"></i>
                        <span class="hidden sm:inline">Submeter Flag</span>
                    </button>

                    <!-- Botão Resetar Laboratório -->
                    <button onclick="confirmResetLab()" title="Reiniciar Laboratório" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 border border-slate-800 hover:border-rose-500/30 transition-all">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Barra de Progresso no Topo -->
    <div class="w-full bg-slate-900 h-1">
        <div id="nav-progress-bar" class="h-1 bg-gradient-to-r from-emerald-500 to-cyan-500 transition-all duration-500" style="width: <?= $progressPercent ?>%;"></div>
    </div>

    <!-- Conteúdo Principal -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            
            <!-- Hero / Boas-vindas -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-slate-800 p-8 sm:p-10 shadow-2xl">
                <div class="absolute -right-10 -bottom-10 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-1/3 -top-10 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 max-w-3xl space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-mono">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Laboratório Local Docker + Apache Ativo
                    </div>
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white">
                        Domine Vulnerabilidades Web na Prática
                    </h1>
                    <p class="text-slate-300 text-base sm:text-lg leading-relaxed">
                        Bem-vindo ao <strong class="text-emerald-400 font-semibold">CyberPulse CTF Lab</strong>. Explore cenários reais de segurança ofensiva (Pentest) e defensiva (Secure Coding) cobrindo os principais riscos da <strong class="text-cyan-400 font-semibold">OWASP Top 10</strong>. Encontre as flags no formato <code class="bg-slate-800 px-2 py-0.5 rounded text-emerald-400 font-mono text-sm">FLAG{...}</code>.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-4">
                        <button onclick="openGlobalFlagModal()" class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-bold text-sm flex items-center gap-2 shadow-lg shadow-emerald-500/25 transition-all">
                            <i class="fa-solid fa-flag"></i>
                            Submeter Flag Capturada
                        </button>
                        <a href="#desafios" class="px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-sm flex items-center gap-2 border border-slate-700 transition-all">
                            <i class="fa-solid fa-crosshairs text-emerald-400"></i>
                            Ver Todos os Desafios
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Rápidas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Pontuação Total</p>
                        <h3 class="text-2xl font-bold font-mono text-white"><?= $totalScore ?> <span class="text-xs text-slate-500 font-normal">/ <?= $maxScore ?></span></h3>
                    </div>
                </div>

                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl">
                        <i class="fa-solid fa-shield-virus"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Desafios Concluídos</p>
                        <h3 class="text-2xl font-bold font-mono text-white"><?= $solvedCount ?> <span class="text-xs text-slate-500 font-normal">/ <?= $totalChallenges ?></span></h3>
                    </div>
                </div>

                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-xl">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Taxa de Sucesso</p>
                        <h3 class="text-2xl font-bold font-mono text-cyan-400"><?= $progressPercent ?>%</h3>
                    </div>
                </div>

                <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400 text-xl">
                        <i class="fa-solid fa-server"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Engine Local</p>
                        <h3 class="text-sm font-bold font-mono text-slate-200">Apache 2.4 + PHP 8.2</h3>
                    </div>
                </div>
            </div>

            <!-- Lista de Desafios -->
            <div id="desafios" class="space-y-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                            <i class="fa-solid fa-list-check text-emerald-400"></i>
                            Matriz de Desafios Cibernéticos
                        </h2>
                        <p class="text-sm text-slate-400 mt-1">Selecione um desafio para acessar a aplicação vulnerável e iniciar o teste.</p>
                    </div>
                </div>

                <!-- Grid de Cards de Desafios -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($CHALLENGES as $id => $ch): 
                        $solved = isSolved($id);
                        $diffColor = match($ch['difficulty']) {
                            'Fácil' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                            'Médio' => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                            'Difícil' => 'bg-rose-500/15 text-rose-400 border-rose-500/30',
                            default => 'bg-slate-700 text-slate-300'
                        };
                    ?>
                        <div id="challenge-card-<?= $id ?>" class="flex flex-col justify-between rounded-2xl bg-slate-900/90 border transition-all duration-300 hover:shadow-xl hover:border-slate-700 <?= $solved ? 'border-emerald-500/50 bg-emerald-950/10' : 'border-slate-800' ?> p-6">
                            <!-- Topo do Card -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                        <?= htmlspecialchars($ch['category']) ?>
                                    </span>

                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold border <?= $diffColor ?>">
                                            <?= htmlspecialchars($ch['difficulty']) ?>
                                        </span>
                                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            +<?= $ch['points'] ?> pts
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors">
                                        <?= htmlspecialchars($ch['title']) ?>
                                    </h3>
                                    <p class="text-sm text-slate-400 mt-2 line-clamp-3">
                                        <?= htmlspecialchars($ch['description']) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Rodapé do Card com Ações -->
                            <div class="mt-6 pt-4 border-t border-slate-800/80 space-y-4">
                                <!-- Status Badge -->
                                <div class="flex items-center justify-between">
                                    <span class="challenge-status-badge px-2.5 py-1 rounded-full text-xs font-mono font-semibold <?= $solved ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700' ?> flex items-center gap-1.5">
                                        <i class="fa-solid <?= $solved ? 'fa-check text-emerald-400' : 'fa-hourglass-start' ?>"></i>
                                        <?= $solved ? 'RESOLVIDO' : 'PENDENTE' ?>
                                    </span>

                                    <a href="<?= htmlspecialchars($ch['target_url']) ?>" class="px-3.5 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-semibold text-xs flex items-center gap-2 transition-all">
                                        <span>Explorar</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                </div>

                                <!-- Submissão Direta no Card -->
                                <div class="flex items-center gap-2">
                                    <input type="text" id="flag-input-<?= $id ?>" placeholder="FLAG{...}" 
                                        class="w-full px-3 py-1.5 text-xs font-mono rounded-lg bg-slate-950 border border-slate-800 focus:border-emerald-500 text-slate-200 outline-none">
                                    <button onclick="submitChallengeFlag('<?= $id ?>', 'flag-input-<?= $id ?>')" 
                                        class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 hover:text-black text-slate-200 font-bold text-xs transition-colors shrink-0" title="Submeter">
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </button>
                                </div>

                                <!-- Dropdown Dica e Mitigação -->
                                <div class="flex items-center justify-between text-xs pt-1">
                                    <button onclick="toggleAccordion('hints-<?= $id ?>')" class="text-slate-400 hover:text-cyan-400 transition-colors flex items-center gap-1">
                                        <i class="fa-solid fa-lightbulb text-amber-400"></i>
                                        Dicas (2)
                                    </button>
                                    <button onclick="toggleAccordion('mitigation-<?= $id ?>')" class="text-slate-400 hover:text-emerald-400 transition-colors flex items-center gap-1">
                                        <i class="fa-solid fa-shield-check text-emerald-400"></i>
                                        Como Mitigar
                                    </button>
                                </div>

                                <!-- Caixa de Dicas Acordeão -->
                                <div id="hints-<?= $id ?>" class="hidden bg-slate-950/80 rounded-xl p-3 border border-slate-800 text-xs space-y-2 font-mono">
                                    <p class="text-amber-400 font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-circle-info"></i> Dicas para Exploração:
                                    </p>
                                    <?php foreach ($ch['hints'] as $hint): ?>
                                        <p class="text-slate-300">• <?= htmlspecialchars($hint) ?></p>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Caixa de Mitigação Acordeão -->
                                <div id="mitigation-<?= $id ?>" class="hidden bg-slate-950/80 rounded-xl p-3 border border-emerald-900/40 text-xs space-y-1">
                                    <p class="text-emerald-400 font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-lock"></i> Boas Práticas Defensivas:
                                    </p>
                                    <p class="text-slate-300 font-mono text-[11px] leading-relaxed">
                                        <?= htmlspecialchars($ch['mitigation']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Guia de Conceitos Educacionais -->
            <div class="rounded-3xl bg-slate-900/60 border border-slate-800 p-8 space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400">
                        <i class="fa-solid fa-book-skull text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Metodologia e Conceitos de Segurança</h3>
                        <p class="text-xs text-slate-400">Compreenda o ciclo de exploração e correção das vulnerabilidades</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-sm">
                    <div class="space-y-2 p-4 rounded-xl bg-slate-950 border border-slate-800/80">
                        <span class="text-emerald-400 font-bold font-mono flex items-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> 1. Reconhecimento
                        </span>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            Mapeamento de ativos, leitura de arquivos <code class="text-slate-300">robots.txt</code>, inspeção de código-fonte HTML/JS e detecção de arquivos de backup esquecidos em produção.
                        </p>
                    </div>

                    <div class="space-y-2 p-4 rounded-xl bg-slate-950 border border-slate-800/80">
                        <span class="text-cyan-400 font-bold font-mono flex items-center gap-2">
                            <i class="fa-solid fa-code"></i> 2. Injeções & Quebra de Lógica
                        </span>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            Manipulação de comandos (Command Injection), queries SQL (SQLi) e renderização de scripts no navegador (XSS) através de entradas não sanitizadas do usuário.
                        </p>
                    </div>

                    <div class="space-y-2 p-4 rounded-xl bg-slate-950 border border-slate-800/80">
                        <span class="text-purple-400 font-bold font-mono flex items-center gap-2">
                            <i class="fa-solid fa-user-shield"></i> 3. Controle de Acesso & Sessão
                        </span>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            Adulteração de identificadores de objeto (IDOR) e cookies fracos para obter privilégios administrativos sem possuir as credenciais devidas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-16 border-t border-slate-800/80 bg-slate-950/60 backdrop-blur-sm py-8 text-slate-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-terminal text-emerald-400"></i>
                <span class="font-mono text-slate-300">CyberPulse Security CTF Lab</span>
                <span class="text-xs text-slate-600">| Apache + Docker Edition</span>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-500 font-mono">
                <span>Ambiente Seguro Local</span>
                <span>•</span>
                <span>Foco Educacional</span>
                <span>•</span>
                <a href="robots.txt" target="_blank" class="hover:text-emerald-400 transition-colors">robots.txt</a>
            </div>
        </div>
    </footer>

    <!-- Modal Global de Submissão de Flag -->
    <div id="flagModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700/80 rounded-2xl max-w-md w-full p-6 shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
            <button onclick="closeGlobalFlagModal()" class="absolute top-4 right-4 text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                    <i class="fa-solid fa-flag-checkered text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Submeter Flag</h3>
                    <p class="text-xs text-slate-400">Insira a flag encontrada no formato FLAG{...}</p>
                </div>
            </div>

            <form id="globalFlagForm" onsubmit="handleGlobalFlagSubmit(event)" class="space-y-4">
                <div>
                    <label for="modalFlagInput" class="block text-xs font-mono uppercase text-slate-400 mb-1">Flag de Captura</label>
                    <input type="text" id="modalFlagInput" placeholder="FLAG{exemplo_de_flag_aqui}" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-white font-mono placeholder:text-slate-600 outline-none transition-all">
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeGlobalFlagModal()" class="px-4 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:bg-slate-800 text-sm font-semibold transition-all">
                        Cancelar
                    </button>
                    <button type="submit" id="btnSubmitModal" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-slate-950 font-bold text-sm flex items-center gap-2 transition-all shadow-lg shadow-emerald-600/30">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Validar Flag</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Container de Notificações Toast -->
    <div id="toastContainer" class="fixed bottom-6 right-6 z-50 flex flex-col gap-3 pointer-events-none"></div>

    <!-- Conexão com os Scripts Existentes -->
    <script src="assets/js/confetti.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
