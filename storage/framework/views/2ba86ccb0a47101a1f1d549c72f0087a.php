

<?php $__env->startSection('content'); ?>
<section class="panel" style="min-height:auto; padding:32px 24px;">
    <div class="panel-inner">
        <div style="min-height:55vh; display:flex; align-items:center; justify-content:center; text-align:center; margin-bottom:40px;">
            <div style="width:100%;">
                <div class="eyebrow">Papan antrian publik</div>
                <div style="font-size:1rem; color:var(--muted); letter-spacing:0.18em; text-transform:uppercase;">Nomor dipanggil</div>
                <div id="nomor" style="font-size:clamp(5rem, 16vw, 10rem); font-weight:800; line-height:0.9; margin-top:12px; color:#ffd86b; text-shadow:0 0 30px rgba(255, 216, 107, 0.2);">---</div>
                <div id="nama" style="font-size:clamp(1.4rem, 4vw, 2.5rem); font-weight:700; margin-top:14px;">-</div>
                <div id="layanan" style="margin-top:8px; color:var(--muted); font-size:1rem;">-</div>
                <audio id="notifAudio" preload="auto">
                    <source src="<?php echo e(asset('assets/sounds/dragon-studio-doorbell-ding-dong-482879 (mp3cut.net).mp3')); ?>" type="audio/mpeg">
                </audio>
            </div>
        </div>

        <div class="card" style="border:1px solid var(--border); background:rgba(31,41,55,0.4); border-radius:20px;">
            <div class="p-4 border-0 card-header" style="background:rgba(51,65,85,0.5); border-radius:20px 20px 0 0;">
                <div style="font-size:0.85rem; color:var(--muted); letter-spacing:0.15em; text-transform:uppercase; margin-bottom:4px;">Antrian</div>
                <h5 class="mb-0" style="color:var(--text); font-weight:700;">Pasien Menunggu</h5>
            </div>
            <div class="p-0 card-body">
                <div id="waitingList" style="max-height:400px; overflow-y:auto;">
                    <div style="padding:24px; text-align:center; color:var(--muted); font-size:0.95rem;">Tidak ada antrian menunggu</div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let lastCalledId = null;
let lastCalledAt = null;
let lastAnnouncement = '';
let antrianSource = null;
const notifAudio = document.getElementById('notifAudio');
let preferredVoice = null;
const initialAntrians = <?php echo json_encode($antrians, 15, 512) ?>;
const initialCalled = <?php echo json_encode($called, 15, 512) ?>;
const initialQueueVersion = <?php echo json_encode($queueVersion ?? null, 15, 512) ?>;
const usePolling = <?php echo json_encode($usePolling ?? false, 15, 512) ?>;
let pollingTimer = null;
let lastQueueVersion = null;


function pickFemaleVoice() {
    if (!('speechSynthesis' in window)) return null;

    const voices = window.speechSynthesis.getVoices() || [];
    if (!voices.length) return null;

    const lower = (value) => String(value || '').toLowerCase();

    const gadisVoice = voices.find((voice) => {
        const name = lower(voice.name);
        const lang = lower(voice.lang);
        return lang.startsWith('id') && name.includes('gadis');
    });
    if (gadisVoice) return gadisVoice;

    const indonesianFemale = voices.find((voice) => {
        const name = lower(voice.name);
        const lang = lower(voice.lang);
        return lang.startsWith('id') && (
            name.includes('female') ||
            name.includes('wanita') ||
            name.includes('perempuan')
        );
    });
    if (indonesianFemale) return indonesianFemale;

    const indonesianVoice = voices.find((voice) => lower(voice.lang).startsWith('id'));
    if (indonesianVoice) return indonesianVoice;

    return voices[0] || null;
}

function loadPreferredVoice() {
    preferredVoice = pickFemaleVoice();
}

if ('speechSynthesis' in window) {
    loadPreferredVoice();
    window.speechSynthesis.addEventListener('voiceschanged', loadPreferredVoice);
}

function speakText(text) {
    if (!('speechSynthesis' in window) || !text) return;

    try {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        if (preferredVoice) {
            utterance.voice = preferredVoice;
            utterance.lang = preferredVoice.lang || 'id-ID';
        } else {
            utterance.lang = 'id-ID';
        }

        utterance.rate = 0.85;
        utterance.pitch = 1.0;
        window.speechSynthesis.speak(utterance);
    } catch (err) {
        console.warn('TTS gagal', err);
    }
}

function playNotificationSound() {
    return new Promise((resolve) => {
        if (!notifAudio) {
            resolve(false);
            return;
        }

        try {
            notifAudio.pause();
            notifAudio.currentTime = 0;
            if (typeof notifAudio.load === 'function') {
                notifAudio.load();
            }
        } catch (err) {}

        let settled = false;

        const finish = (played) => {
            if (settled) return;
            settled = true;
            notifAudio.onended = null;
            notifAudio.onerror = null;
            resolve(played);
        };

        notifAudio.onended = function() {
            finish(true);
        };

        notifAudio.onerror = function() {
            finish(false);
        };

        const playResult = notifAudio.play();
        if (playResult && typeof playResult.catch === 'function') {
            playResult.then(() => {}).catch(() => finish(false));
        }
    });
}

async function announceText(text) {
    const message = text || '';
    if (!message) return;

    if (!notifAudio) {
        speakText(message);
        return;
    }

    const played = await playNotificationSound();
    if (!played) {
        speakText(message);
        return;
    }

    speakText(message);
}

function renderWaitingList(antrians) {
    const waiting = (antrians || []).filter((a) => a.status === 'menunggu');
    const container = document.getElementById('waitingList');
    if (!container) return;

    if (!waiting.length) {
        container.innerHTML = '<div style="padding:24px; text-align:center; color:var(--muted); font-size:0.95rem;">Tidak ada antrian menunggu</div>';
        return;
    }

    let html = '<table style="width:100%; border-collapse:collapse;"><tbody>';
    waiting.forEach((item, idx) => {
        const borderBottom = idx < waiting.length - 1 ? '1px solid var(--border)' : 'none';
        html += `
            <tr style="border-bottom:${borderBottom};">
                <td style="padding:16px 24px; text-align:left; width:70px;">
                    <div style="font-size:1.8rem; font-weight:800; color:#4cc9f0; line-height:1;">${item.nomor_text || String(item.nomor).padStart(3, '0')}</div>
                </td>
                <td style="padding:16px 12px; text-align:left;">
                    <div style="font-weight:600; color:var(--text); font-size:1rem; margin-bottom:4px;">${item.name || '-'}</div>
                    <div style="font-size:0.85rem; color:var(--muted);">${item.layanan_text || item.layanan || '-'}</div>
                </td>
            </tr>
        `;
    });
    html += '</tbody></table>';
    container.innerHTML = html;
}

function updatePapanUI(payload, force = false) {
    if (!payload) return;

    if (!force && payload.version && payload.version === lastQueueVersion) {
        return;
    }

    const called = payload.called || null;
    const nomorEl = document.getElementById('nomor');
    const namaEl = document.getElementById('nama');
    const layananEl = document.getElementById('layanan');

    if (called) {
        const nextId = called.id;
        const nextCalledAt = called.called_at || '';

        if (nomorEl) nomorEl.textContent = called.nomor_text || String(called.nomor).padStart(3, '0');
        if (namaEl) namaEl.textContent = called.name || '-';
        if (layananEl) layananEl.textContent = called.layanan_text || called.layanan || '-';

        if (lastCalledId !== nextId || lastCalledAt !== nextCalledAt || lastAnnouncement !== called.announcement_text) {
            const announcement = called.announcement_text || `Antrian nomor ${called.nomor_text || called.nomor} atas nama ${called.name} silahkan menuju ke layanan ${called.layanan_text || called.layanan || '-'}`;
            announceText(announcement);
            lastCalledId = nextId;
            lastCalledAt = nextCalledAt;
            lastAnnouncement = called.announcement_text || '';
        }
    } else {
        if (nomorEl) nomorEl.textContent = '---';
        if (namaEl) namaEl.textContent = '-';
        if (layananEl) layananEl.textContent = '-';
        lastCalledId = null;
        lastCalledAt = null;
        lastAnnouncement = '';
    }

    renderWaitingList(payload.antrians || []);

    if (payload.version) {
        lastQueueVersion = payload.version;
    }
}

function connectSSE() {
    if (antrianSource) {
        antrianSource.close();
    }

    antrianSource = new EventSource("<?php echo e(route('antrian.sse')); ?>");
    antrianSource.addEventListener('queue-update', function(event) {
        try {
            const payload = JSON.parse(event.data);
            updatePapanUI(payload);
        } catch (err) {
            console.warn('Parse SSE papan gagal', err);
        }
    });

    antrianSource.onopen = function() {
        console.info('SSE papan: connection opened');
    };

    antrianSource.onerror = function(e) {
        console.warn('Koneksi SSE papan terganggu, browser akan reconnect otomatis.', e);
    };
}

async function fetchQueueSnapshot() {
    try {
        const res = await fetch("<?php echo e(route('antrian.snapshot')); ?>", { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;

        const payload = await res.json();
        updatePapanUI(payload);
    } catch (err) {
        console.warn('Polling papan gagal', err);
    }
}

function connectPolling() {
    if (pollingTimer) {
        clearInterval(pollingTimer);
    }

    fetchQueueSnapshot();
    pollingTimer = setInterval(fetchQueueSnapshot, 1000);
}

if (usePolling) {
    connectPolling();
} else {
    connectSSE();
}
updatePapanUI({ antrians: initialAntrians, called: initialCalled, version: initialQueueVersion }, true);
window.addEventListener('beforeunload', function() {
    if (antrianSource) {
        antrianSource.close();
    }
    if (pollingTimer) {
        clearInterval(pollingTimer);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.antrian', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\koleksi-buku\resources\views/antrian/papan.blade.php ENDPATH**/ ?>