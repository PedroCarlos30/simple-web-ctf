// CyberPulse CTF Interactive Logic

// Gerador de Som Sintetizado (Web Audio API)
const AudioFX = {
    playSuccess: () => {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;
            
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(523.25, now); // C5
            osc.frequency.exponentialRampToValueAtTime(659.25, now + 0.1); // E5
            osc.frequency.exponentialRampToValueAtTime(783.99, now + 0.2); // G5
            osc.frequency.exponentialRampToValueAtTime(1046.50, now + 0.3); // C6
            
            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.5);
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            osc.start(now);
            osc.stop(now + 0.5);
        } catch(e) {}
    },
    playError: () => {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const now = ctx.currentTime;
            
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(220, now);
            osc.frequency.exponentialRampToValueAtTime(110, now + 0.25);
            
            gain.gain.setValueAtTime(0.15, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.3);
            
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            osc.start(now);
            osc.stop(now + 0.3);
        } catch(e) {}
    }
};

// Exibir Toast de Notificação
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto flex items-center gap-3 px-5 py-3.5 rounded-xl border shadow-xl backdrop-blur-md transition-all duration-300 transform translate-y-4 opacity-0 max-w-md ${
        type === 'success' 
            ? 'bg-slate-900/95 border-emerald-500/40 text-emerald-300 shadow-emerald-500/10' 
            : 'bg-slate-900/95 border-rose-500/40 text-rose-300 shadow-rose-500/10'
    }`;

    const icon = type === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-rose-400';
    toast.innerHTML = `
        <i class="fa-solid ${icon} text-lg shrink-0"></i>
        <div class="text-sm font-medium text-slate-200 leading-tight">${message}</div>
    `;

    container.appendChild(toast);

    // Animate in
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    });

    // Auto remove
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 4500);
}

// Modal Global de Flag
function openGlobalFlagModal(prefillChallengeId = '') {
    const modal = document.getElementById('flagModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    const input = document.getElementById('modalFlagInput');
    if (input) {
        input.value = '';
        input.focus();
        if (prefillChallengeId) {
            input.dataset.challengeId = prefillChallengeId;
        } else {
            delete input.dataset.challengeId;
        }
    }
}

function closeGlobalFlagModal() {
    const modal = document.getElementById('flagModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Submeter Flag Global
async function handleGlobalFlagSubmit(event) {
    event.preventDefault();
    const input = document.getElementById('modalFlagInput');
    const flag = input ? input.value.trim() : '';
    const challengeId = input?.dataset?.challengeId || '';

    if (!flag) return;

    const btn = document.getElementById('btnSubmitModal');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Validando...';

    try {
        const res = await fetch('/api/submit_flag.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ flag, challenge_id: challengeId })
        });
        const data = await res.json();

        if (data.success) {
            AudioFX.playSuccess();
            if (window.triggerConfetti) window.triggerConfetti();
            showToast(data.message, 'success');
            closeGlobalFlagModal();
            updateScoresUI(data);

            // Se estiver na tela principal ou na tela do desafio, atualizar visual
            markChallengeAsSolved(data.challenge_id);
        } else {
            AudioFX.playError();
            showToast(data.message, 'error');
        }
    } catch (err) {
        showToast('Erro de conexão com o servidor.', 'error');
    } finally {
        if (btn) btn.innerHTML = originalText;
    }
}

// Submissão rápida direto no card do desafio
async function submitChallengeFlag(challengeId, inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const flag = input.value.trim();
    if (!flag) {
        showToast('Por favor, informe a flag.', 'error');
        return;
    }

    try {
        const res = await fetch('/api/submit_flag.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ flag, challenge_id: challengeId })
        });
        const data = await res.json();

        if (data.success) {
            AudioFX.playSuccess();
            if (window.triggerConfetti) window.triggerConfetti();
            showToast(data.message, 'success');
            updateScoresUI(data);
            markChallengeAsSolved(data.challenge_id);
            input.value = '';
        } else {
            AudioFX.playError();
            showToast(data.message, 'error');
        }
    } catch (err) {
        showToast('Erro ao validar flag.', 'error');
    }
}

// Atualizar interface de estatísticas
function updateScoresUI(data) {
    const scoreEl = document.getElementById('nav-total-score');
    if (scoreEl && data.total_score !== undefined) {
        scoreEl.innerText = data.total_score;
    }

    const countEl = document.getElementById('nav-solved-count');
    if (countEl && data.solved_count !== undefined && data.total_challenges !== undefined) {
        countEl.innerText = `${data.solved_count}/${data.total_challenges}`;
        
        const progressBar = document.getElementById('nav-progress-bar');
        if (progressBar) {
            const pct = Math.round((data.solved_count / data.total_challenges) * 100);
            progressBar.style.width = `${pct}%`;
        }
    }
}

// Marcar card como resolvido visualmente
function markChallengeAsSolved(challengeId) {
    const card = document.getElementById(`challenge-card-${challengeId}`);
    if (card) {
        card.classList.add('border-emerald-500/50', 'bg-emerald-950/10');
        const badge = card.querySelector('.challenge-status-badge');
        if (badge) {
            badge.className = 'challenge-status-badge px-2.5 py-1 rounded-full text-xs font-mono font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5';
            badge.innerHTML = '<i class="fa-solid fa-check"></i> RESOLVIDO';
        }
    }

    const banner = document.getElementById('challenge-solved-banner');
    if (banner) {
        banner.classList.remove('hidden');
    }
}

// Resetar Laboratório
async function confirmResetLab() {
    if (!confirm('Deseja realmente reiniciar todo o progresso do laboratório e restaurar os bancos de dados ao estado inicial?')) {
        return;
    }

    try {
        const res = await fetch('/api/reset.php', { method: 'POST' });
        const data = await res.json();
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                window.location.href = '/index.php';
            }, 800);
        } else {
            showToast(data.message, 'error');
        }
    } catch (e) {
        showToast('Falha ao resetar laboratório.', 'error');
    }
}

// Toggle Dicas & Mitigação
function toggleAccordion(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.toggle('hidden');
}

