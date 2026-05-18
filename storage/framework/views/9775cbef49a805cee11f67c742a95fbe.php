

<?php $__env->startSection('content'); ?>
<section class="panel">
    <div class="panel-inner">
        <div class="eyebrow">Pendaftaran</div>
        <h1 class="title">Ambil nomor antrian.</h1>
        <p class="lead">Isi nama dan pilih layanan. Setelah itu klik "Ambil Nomor" untuk cetak nomor antrian.</p>

        <div class="content card">
            <div class="card-body">
                <form id="formAntrian">
                    <?php echo csrf_field(); ?>
                    <div style="display:grid;gap:16px;">
                        <div>
                            <label class="label">Nama</label>
                            <input type="text" name="name" class="field" style="color:#eff6ff" placeholder="Masukkan nama pasien" required>
                        </div>
                        <div>
                            <label class="label">Layanan</label>
                            <select name="layanan"  class="field" required>
                                <option style="color: black" value="">-- Pilih Layanan --</option>
                                <option style="color: black" value="loket_umum">Loket Umum</option>
                                <option style="color: black" value="loket_khusus">Loket Khusus</option>
                                <option style="color: black" value="informasi">Informasi</option>
                            </select>
                        </div>
                        <div style="display:flex;gap:12px;justify-content:flex-end;flex-wrap:wrap;">
                            <button type="submit" class="btn-soft primary">Ambil Nomor</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="antrianPopup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="border-0 shadow-lg modal-content" style="overflow:hidden; border-radius:24px;">
            <div class="border-0 modal-header" style="background:linear-gradient(180deg, #22408c 0%, #183371 100%); color:#fff;">
                <div>
                    <div class="small text-uppercase" style="letter-spacing:0.18em; opacity:0.75;">Informasi Nomor Antrian</div>
                    <h5 class="mb-0 modal-title fw-bold">Nomor berhasil diambil</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="p-4 modal-body p-lg-5" id="popupPrintArea">
                <div class="mb-4 text-center">
                    <div style="font-size:0.85rem; color:var(--muted); letter-spacing:0.2em; text-transform:uppercase;">Nomor Antrian</div>
                    <div id="popupNomor" style="font-size:clamp(4rem, 12vw, 7rem); font-weight:800; line-height:0.9; margin-top:10px; color:#183371;">---</div>
                    <div id="popupNama" style="font-size:clamp(1.35rem, 3vw, 2rem); font-weight:700; margin-top:14px; color:#0f172a;">-</div>
                    <div id="popupLayanan" style="margin-top:8px; color:#64748b; font-size:1rem;">-</div>
                </div>
                <div class="mb-0 border-0 alert alert-info" style="background:#eff6ff; color:#1d4ed8; border-radius:18px;">
                    Silakan simpan informasi nomor ini. Anda tetap berada di halaman pendaftaran.
                </div>
            </div>
            <div class="p-4 pt-0 border-0 modal-footer">
                <button type="button" class="btn btn-outline-secondary" id="btnTutupPopup" data-bs-dismiss="modal">Selesai</button>
                <button type="button" class="btn btn-primary" id="btnCetakPopup">Cetak</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @media print {
        body * {
            visibility: hidden !important;
        }

        #popupPrintArea, #popupPrintArea * {
            visibility: visible !important;
        }

        #popupPrintArea {
            position: fixed !important;
            inset: 0 !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        #antrianPopup .modal-header,
        #antrianPopup .modal-footer,
        .panel,
        .topbar,
        .shell > .alert {
            display: none !important;
        }

        #antrianPopup .modal-content {
            box-shadow: none !important;
            border: 0 !important;
        }

        #antrianPopup {
            position: static !important;
            display: block !important;
            overflow: visible !important;
            background: #fff !important;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let antrianPopupModal = null;
let currentPopupData = null;
let guestAntrianSource = null;
const usePolling = <?php echo json_encode($usePolling ?? false, 15, 512) ?>;
const initialQueueVersion = <?php echo json_encode($queueVersion ?? null, 15, 512) ?>;
const initialAntrians = <?php echo json_encode($antrians ?? [], 15, 512) ?>;
const initialCalled = <?php echo json_encode($called ?? null, 15, 512) ?>;
let lastQueueVersion = null;

function openPopup(data) {
    currentPopupData = data;
    document.getElementById('popupNomor').textContent = data.nomor_text || String(data.nomor).padStart(3, '0');
    document.getElementById('popupNama').textContent = data.name || '-';
    document.getElementById('popupLayanan').textContent = data.layanan_text || data.layanan || '-';

    if (!antrianPopupModal) {
        antrianPopupModal = new bootstrap.Modal(document.getElementById('antrianPopup'));
    }
    antrianPopupModal.show();
}

function closePopup(){
    if (antrianPopupModal) {
        antrianPopupModal.hide();
    }
}

document.getElementById('btnCetakPopup').addEventListener('click', function(){
    window.print();
});

window.addEventListener('afterprint', function(){
    closePopup();
});

function updateGuestRealtime(payload, force = false) {
    if (!force && payload && payload.version && payload.version === lastQueueVersion) {
        return;
    }

    const called = payload && payload.called ? payload.called : null;
    const antrians = payload && Array.isArray(payload.antrians) ? payload.antrians : [];
    const waitingCount = antrians.filter((item) => item.status === 'menunggu').length;

    const currentCalledEl = document.getElementById('guestCurrentCalled');
    const waitingCountEl = document.getElementById('guestWaitingCount');

    if (currentCalledEl) {
        if (called) {
            const nomor = called.nomor_text || String(called.nomor).padStart(3, '0');
            currentCalledEl.textContent = `${nomor} - ${called.name || '-'}`;
        } else {
            currentCalledEl.textContent = '-';
        }
    }

    if (waitingCountEl) {
        waitingCountEl.textContent = String(waitingCount);
    }

    if (payload && payload.version) {
        lastQueueVersion = payload.version;
    }
}

function connectGuestSSE() {
    if (guestAntrianSource) {
        guestAntrianSource.close();
    }

    guestAntrianSource = new EventSource("<?php echo e(route('antrian.sse')); ?>");
    guestAntrianSource.addEventListener('queue-update', function(event) {
        try {
            const payload = JSON.parse(event.data);
            updateGuestRealtime(payload);
        } catch (err) {
            console.warn('Parse SSE guest gagal', err);
        }
    });
    guestAntrianSource.onopen = function() { console.info('SSE guest: connection opened'); };
    guestAntrianSource.onerror = function(e) { console.warn('SSE guest: connection error', e); };
}

async function fetchQueueSnapshot() {
    try {
        const res = await fetch("<?php echo e(route('antrian.snapshot')); ?>", { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;

        const payload = await res.json();
        updateGuestRealtime(payload);
    } catch (err) {
        console.warn('Polling guest gagal', err);
    }
}

function connectPolling() {
    fetchQueueSnapshot();
    setInterval(fetchQueueSnapshot, 3000);
}

document.getElementById('formAntrian').addEventListener('submit', async function(e){
    e.preventDefault();
    const fd = new FormData(this);
    let token = fd.get('_token');
    if (!token) {
        const meta = document.querySelector('meta[name="csrf-token"]');
        token = meta ? meta.getAttribute('content') : null;
    }
    const layananValue = fd.get('layanan');
    if (!layananValue) {
        alert('Silakan pilih layanan');
        return;
    }
    const body = { name: fd.get('name'), layanan: [layananValue] };

    const res = await fetch("<?php echo e(route('antrian.store')); ?>", {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify(body)
    });
    const data = await res.json();
    if (data.success) {
        openPopup(data);
        this.reset();
    } else {
        alert(data.message || 'Gagal mengambil nomor');
    }
});

updateGuestRealtime({ antrians: initialAntrians, called: initialCalled, version: initialQueueVersion }, true);

if (usePolling) {
    connectPolling();
} else {
    connectGuestSSE();
}
window.addEventListener('beforeunload', function(){
    if (guestAntrianSource) {
        guestAntrianSource.close();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.antrian', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\koleksi-buku\resources\views\antrian\guest.blade.php ENDPATH**/ ?>