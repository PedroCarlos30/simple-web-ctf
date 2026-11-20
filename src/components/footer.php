<?php declare(strict_types=1); ?>
    </main>

    <!-- Footer -->
    <footer class="mt-16 border-t border-slate-800/80 bg-slate-950/60 backdrop-blur-sm py-8 text-slate-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-terminal text-emerald-400"></i>
                <span class="font-mono text-slate-300">CyberPulse Security CTF Lab</span>
                <span class="text-xs text-slate-600">| Apache + Docker Edition v3.0</span>
            </div>
            <div class="flex items-center gap-4 text-xs text-slate-500 font-mono">
                <span>Ambiente Seguro Local</span>
                <span>•</span>
                <span>Foco Educacional</span>
                <span>•</span>
                <a href="/robots.txt" target="_blank" class="hover:text-emerald-400 transition-colors">robots.txt</a>
            </div>
        </div>
    </footer>

    <!-- Modal Global de Submissão de Flag -->
    <div id="flagModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-700/80 rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
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

    <!-- Scripts -->
    <script src="/assets/js/confetti.min.js"></script>
    <script src="/assets/js/app.js"></script>
</body>
</html>
