<?php
require_once __DIR__ . '/../config.php';

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
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>CyberPulse CTF Lab</title>
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
                            accent: '#10b981', // emerald
                            neon: '#06b6d4',   // cyan
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
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Cyber Styles -->
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans selection:bg-emerald-500 selection:text-black">
    <!-- Navbar Superior -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-slate-950/80 border-b border-slate-800/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Nome -->
                <div class="flex items-center gap-3">
                    <a href="/index.php" class="flex items-center gap-2 group">
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

                    <!-- Botão Menu / Reset -->
                    <button onclick="confirmResetLab()" title="Reiniciar Laboratório" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 border border-slate-800 hover:border-rose-500/30 transition-all">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Barra de Progresso Fina no Topo -->
    <div class="w-full bg-slate-900 h-1">
        <div id="nav-progress-bar" class="h-1 bg-gradient-to-r from-emerald-500 to-cyan-500 transition-all duration-500" style="width: <?= $progressPercent ?>%;"></div>
    </div>

    <!-- Conteúdo Principal -->
    <main class="flex-grow">

