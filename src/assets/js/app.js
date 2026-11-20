// CyberPulse CTF Lab — Interactive Logic v3.0

// ================================================================
// WEB AUDIO API — Efeitos Sonoros
// ================================================================
const AudioFX = {
    getContext: () => {
        if (!AudioFX._ctx || AudioFX._ctx.state === 'closed') {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (AudioCtx) AudioFX._ctx = new AudioCtx();
        }
        if (AudioFX._ctx && AudioFX._ctx.state === 'suspended') {
            AudioFX._ctx.resume();
        }
        return AudioFX._ctx;
    },

    playCyberPulse: () => {
        try {
            const ctx = AudioFX.getContext();
            if (!ctx) return;
            const now = ctx.currentTime;

            const clickOsc = ctx.createOscillator();
            const clickGain = ctx.createGain();
            clickOsc.type = 'triangle';
            clickOsc.frequency.setValueAtTime(2600, now);
            clickOsc.frequency.exponentialRampToValueAtTime(450, now + 0.02);
            clickGain.gain.setValueAtTime(0.35, now);
            clickGain.gain.exponentialRampToValueAtTime(0.001, now + 0.025);
            clickOsc.connect(clickGain);
            clickGain.connect(ctx.destination);
            clickOsc.start(now);
            clickOsc.stop(now + 0.025);

            const pulseOsc = ctx.createOscillator();
            const pulseGain = ctx.createGain();
            const filter = ctx.createBiquadFilter();
            pulseOsc.type = 'sine';
            pulseOsc.frequency.setValueAtTime(1150, now);
            pulseOsc.frequency.exponentialRampToValueAtTime(1680, now + 0.015);
            pulseOsc.frequency.exponentialRampToValueAtTime(480, now + 0.085);
            filter.type = 'bandpass';
            filter.Q.setValueAtTime(3.8, now);
            filter.frequency.setValueAtTime(1400, now);
            filter.frequency.exponentialRampToValueAtTime(750, now + 0.085);
            pulseGain.gain.setValueAtTime(0.32, now);
            pulseGain.gain.linearRampToValueAtTime(0.42, now + 0.01);
            pulseGain.gain.exponentialRampToValueAtTime(0.001, now + 0.09);
            pulseOsc.connect(filter);
            filter.connect(pulseGain);
            pulseGain.connect(ctx.destination);
            pulseOsc.start(now);
            pulseOsc.stop(now + 0.09);

            const subOsc = ctx.createOscillator();
            const subGain = ctx.createGain();
            subOsc.type = 'sine';
            subOsc.frequency.setValueAtTime(125, now);
            subOsc.frequency.exponentialRampToValueAtTime(35, now + 0.045);
            subGain.gain.setValueAtTime(0.22, now);
            subGain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);
            subOsc.connect(subGain);
            subGain.connect(ctx.destination);
            subOsc.start(now);
            subOsc.stop(now + 0.05);
        } catch (e) {
            console.warn('AudioFX pulse error:', e);
        }
    },

    playFire: () => AudioFX.playCyberPulse(),

    playSuccess: () => {
        try {
            const ctx = AudioFX.getContext();
            if (!ctx) return;
            const now = ctx.currentTime;
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(523.25, now);
            osc.frequency.exponentialRampToValueAtTime(659.25, now + 0.1);
            osc.frequency.exponentialRampToValueAtTime(783.99, now + 0.2);
            osc.frequency.exponentialRampToValueAtTime(1046.50, now + 0.3);
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
            const ctx = AudioFX.getContext();
            if (!ctx) return;
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
    },
};

// ================================================================
// TOAST DE NOTIFICAÇÃO
// ================================================================
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
    requestAnimationFrame(() => toast.classList.remove('translate-y-4', 'opacity-0'));
    setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
        setTimeout(() => toast.remove(), 300);
    }, 4500);
}

// ================================================================
// MODAL GLOBAL DE FLAG
// ================================================================
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

async function handleGlobalFlagSubmit(event) {
    event.preventDefault();
    AudioFX.playCyberPulse();

    const btn   = document.getElementById('btnSubmitModal');
    const input = document.getElementById('modalFlagInput');
    const flag  = input ? input.value.trim() : '';
    const challengeId = input?.dataset?.challengeId || '';

    if (!flag) return;

    if (btn) btn.classList.add('cyber-pulse-active');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) btn.innerHTML = '<i class="fa-solid fa-satellite-dish text-cyan-400 animate-pulse"></i> Transmitindo...';

    try {
        const res  = await fetch('/api/submit_flag.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ flag, challenge_id: challengeId }),
        });
        const data = await res.json();

        if (data.success) {
            AudioFX.playSuccess();
            if (window.triggerConfetti) window.triggerConfetti();
            showToast(data.message, 'success');
            closeGlobalFlagModal();
            updateScoresUI(data);
            markChallengeAsSolved(data.challenge_id);
        } else {
            AudioFX.playError();
            showToast(data.message, 'error');
            if (challengeId) unlockHint2(challengeId);
        }
    } catch (err) {
        showToast('Erro de conexão com o servidor.', 'error');
    } finally {
        if (btn) {
            btn.innerHTML = originalText;
            setTimeout(() => btn.classList.remove('cyber-pulse-active'), 600);
        }
    }
}

// ================================================================
// SUBMISSÃO RÁPIDA NO CARD DO DESAFIO
// ================================================================
async function submitChallengeFlag(challengeId, inputId) {
    AudioFX.playCyberPulse();

    const input = document.getElementById(inputId);
    if (!input) return;
    const flag = input.value.trim();
    if (!flag) {
        showToast('Por favor, informe a flag.', 'error');
        return;
    }

    const parentContainer = input.parentElement;
    const btn = parentContainer ? parentContainer.querySelector('button') : null;
    if (btn) {
        btn.classList.add('cyber-pulse-active');
        setTimeout(() => btn.classList.remove('cyber-pulse-active'), 600);
    }

    try {
        const res  = await fetch('/api/submit_flag.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ flag, challenge_id: challengeId }),
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
            // Desbloquear Dica 2 após submit incorreto
            unlockHint2(challengeId);
        }
    } catch (err) {
        showToast('Erro ao validar flag.', 'error');
    }
}

// ================================================================
// ATUALIZAR INTERFACE DE ESTATÍSTICAS
// ================================================================
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

// ================================================================
// MARCAR CARD COMO RESOLVIDO
// ================================================================
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
    if (banner) banner.classList.remove('hidden');
}

// ================================================================
// SISTEMA DE DICAS PROGRESSIVAS (localStorage)
// ================================================================

/**
 * Inicializa o estado do botão de Dica 2 ao carregar a página.
 * Se o aluno já tentou antes (localStorage), habilita o botão.
 */
function initHint2State(challengeId) {
    const unlocked = localStorage.getItem('hint2_unlocked_' + challengeId) === '1';
    if (unlocked) {
        enableHint2Button(challengeId);
    }
}

/**
 * Habilita o botão de revelar Dica 2.
 */
function enableHint2Button(challengeId) {
    const btn = document.getElementById('hint2-btn-' + challengeId);
    if (btn) {
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
        btn.classList.add('hover:border-amber-500/50', 'hover:text-amber-400', 'cursor-pointer');
        btn.innerHTML = '<i class="fa-solid fa-lock-open text-amber-400"></i> Revelar Dica 2';
        btn.title = 'Clique para revelar a dica prática';
    }
}

/**
 * Desbloqueia a Dica 2 após um submit incorreto e persiste no localStorage.
 */
function unlockHint2(challengeId) {
    if (!challengeId) return;
    localStorage.setItem('hint2_unlocked_' + challengeId, '1');
    enableHint2Button(challengeId);
}

/**
 * Alterna a visibilidade da Dica 2.
 */
function revealHint2(challengeId) {
    const div = document.getElementById('hint2-' + challengeId);
    if (div) div.classList.toggle('hidden');
}

// ================================================================
// RESETAR LABORATÓRIO (com CSRF token)
// ================================================================
async function confirmResetLab() {
    if (!confirm('Deseja realmente reiniciar todo o progresso do laboratório e restaurar os bancos de dados ao estado inicial?')) {
        return;
    }

    // Ler CSRF token do meta-tag
    const csrfMeta  = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    try {
        const res  = await fetch('/api/reset.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken,
            },
            body: JSON.stringify({ csrf_token: csrfToken }),
        });
        const data = await res.json();

        if (data.success) {
            // Limpar localStorage de dicas progressivas
            Object.keys(localStorage)
                .filter(k => k.startsWith('hint2_unlocked_'))
                .forEach(k => localStorage.removeItem(k));

            showToast(data.message, 'success');
            setTimeout(() => window.location.href = '/index.php', 800);
        } else {
            showToast(data.message || 'Erro ao resetar.', 'error');
        }
    } catch (e) {
        showToast('Falha ao resetar laboratório.', 'error');
    }
}

// ================================================================
// TOGGLE ACORDEÃO (Dicas & Mitigação nos cards)
// ================================================================
function toggleAccordion(id) {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('hidden');
}
