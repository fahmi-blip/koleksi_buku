

<?php $__env->startSection('content'); ?>
<div class="py-4 container-fluid">
    <div class="flex-wrap gap-2 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-1 fw-bold">Admin - Manajemen Antrian</h3>
            <div class="text-muted">Dashboard antrian dengan tampilan ringan seperti halaman utama project</div>
        </div>
        <div id="alerts"></div>
    </div>

    <div class="mb-4 border-0 shadow-sm card">
        <div class="p-4 card-body p-lg-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="mb-2 text-uppercase text-muted small fw-semibold">Nomor sedang dipanggil</div>
                    <div id="currentCalledNumber" class="mb-2 display-3 fw-bold text-primary" style="line-height:0.95;">---</div>
                    <div id="currentCalledName" class="mb-2 h3 fw-semibold">Belum ada antrian dipanggil</div>
                    <div id="currentCalledService" class="text-muted mb-3">-</div>
                    <div class="d-flex gap-2">
                        <button id="btnCallCurrent" class="btn btn-sm btn-warning btn-call-current" disabled>Panggil Ulang</button>
                        <button id="btnFinishCurrent" class="btn btn-sm btn-success btn-finish-current" disabled>Selesai</button>
                        <button id="btnLateCurrent" class="btn btn-sm btn-danger btn-late-current" disabled>Terlambat</button>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="p-3 border p-lg-4 rounded-4 bg-light">
                        <div class="py-2 d-flex justify-content-between align-items-center border-bottom">
                            <span class="text-muted">Nomor terakhir dipanggil</span>
                            <strong id="currentCalledMeta">-</strong>
                        </div>
                        <div class="py-2 d-flex justify-content-between align-items-center border-bottom">
                            <span class="text-muted">Total antrian aktif</span>
                            <strong id="totalActiveQuick">0</strong>
                        </div>
                        <div class="py-2 d-flex justify-content-between align-items-center border-bottom">
                            <span class="text-muted">Pembaruan terakhir</span>
                            <strong id="lastRefreshLabel">-</strong>
                        </div>
                        <div class="py-2 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Countdown</span>
                            <strong id="countdownDisplay" style="color:#dc3545; font-size:1.2rem;">-</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-2 row g-2">
        <div class="col-md-3 stretch-card grid-margin">
          <div class="text-white card bg-gradient-info card-img-holder">
            <div class="card-body">
              <img src='../assets/images/dashboard/circle.svg'  class="card-img-absolute" alt="circle-image" />
              <div class="mall text-uppercase fw-semibold">Menunggu</div>
              <div id="countMenunggu" class="mb-1 display-6 fw-bold">0</div>
              <div class="small">Peserta yang belum dipanggil</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
          <div class="text-white card bg-gradient-warning card-img-holder">
            <div class="card-body">
              <img src='../assets/images/dashboard/circle.svg'  class="card-img-absolute" alt="circle-image" />
                <div class="small text-uppercase fw-semibold">Dipanggil</div>
                <div id="countDipanggil" class="mb-1 display-6 fw-bold ">0</div>
                <div class="small">Peserta sedang dilayani</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
          <div class="text-white card bg-gradient-success card-img-holder">
            <div class="card-body">
              <img src='../assets/images/dashboard/circle.svg'  class="card-img-absolute" alt="circle-image" />
                <div class="small text-uppercase fw-semibold">Selesai</div>
                <div id="countSelesai" class="mb-1 display-6 fw-bold">0</div>
                <div class="small">Peserta yang sudah selesai</div>
            </div>
          </div>
        </div>
        <div class="col-md-3 stretch-card grid-margin">
          <div class="text-white card bg-gradient-danger card-img-holder">
            <div class="card-body">
              <img src='../assets/images/dashboard/circle.svg'  class="card-img-absolute" alt="circle-image" />
                <div class="small text-uppercase fw-semibold">Terlambat</div>
                <div id="countTerlambat" class="mb-1 display-6 fw-bold">0</div>
                <div class="small">Peserta yang belum hadir</div>
            </div>
          </div>
        </div>
    </div>

    <div class="border-0 shadow-sm card">
        <div class="flex-wrap gap-2 py-3 bg-white card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Daftar Antrian</h5>
                <div class="text-muted small">Klik panggil, selesai, atau terlambat dari daftar di bawah</div>
            </div>
            </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle table-hover" id="tblAntrian">
                <thead class="table-light">
                    <tr><th>No</th><th>Nama</th><th>Layanan</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $antrians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr data-id="<?php echo e($a->id); ?>">
                        <td class="fw-semibold"><?php echo e($a->nomor_text ?? str_pad($a->nomor,3,'0',STR_PAD_LEFT)); ?></td>
                        <td><?php echo e($a->name); ?></td>
                        <td><?php echo e($a->layanan_text ?? $a->layanan); ?></td>
                        <td class="status">
                            <span class="text-black border badge text-bg-light"><?php echo e($a->status); ?></span>
                        </td>
                        <td class="actions">
                            <button class="btn btn-sm btn-primary btn-warning">Panggil</button>
                            <button class="btn btn-sm btn-success btn-finish">Selesai</button>
                            <button class="btn btn-sm btn-danger btn-late">Terlambat</button>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let currentCalledId = null;
let countdownTimer = null;
let countdownSeconds = 0;
let antrianSource = null;
const COUNTDOWN_DURATION = 120; // 2 minutes

const initialAntrians = <?php echo json_encode($antrians, 15, 512) ?>;
const initialCalled = <?php echo json_encode($called, 15, 512) ?>;

function formatCountdown(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${String(secs).padStart(2, '0')}`;
}

function startCountdown(antrianId) {
    // Jika countdown sudah berjalan untuk peserta ini, jangan restart
    if (currentCalledId === parseInt(antrianId) && countdownTimer) {
        console.log('Countdown sudah berjalan untuk peserta ini, tidak di-reset. Admin bisa klik Panggil lagi untuk ingatkan.');
        return;
    }
    
    // Clear existing countdown jika berbeda peserta
    if (countdownTimer) {
        clearInterval(countdownTimer);
    }
    
    currentCalledId = parseInt(antrianId);
    countdownSeconds = COUNTDOWN_DURATION;
    document.getElementById('countdownDisplay').textContent = formatCountdown(countdownSeconds);
    
    countdownTimer = setInterval(async function() {
        countdownSeconds--;
        document.getElementById('countdownDisplay').textContent = formatCountdown(countdownSeconds);
        
        // Ketika countdown habis
        if (countdownSeconds <= 0) {
            clearInterval(countdownTimer);
            countdownTimer = null;
            document.getElementById('countdownDisplay').textContent = '-';
            
            // Auto-late
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            try {
                await fetch('/antrian/' + currentCalledId + '/late', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token }
                });
                console.log('Status otomatis diubah ke terlambat');
            } catch (err) {
                console.warn('Auto-late gagal', err);
            }
        }
    }, 1000);
}

function stopCountdown() {
    if (countdownTimer) {
        clearInterval(countdownTimer);
        countdownTimer = null;
    }
    currentCalledId = null;
    countdownSeconds = 0;
    document.getElementById('countdownDisplay').textContent = '-';
}

function updateDashboard(payload){
    const data = payload.antrians || [];
    const counts = data.reduce((acc, item) => {
        acc[item.status] = (acc[item.status] || 0) + 1;
        return acc;
    }, {});

    const called = payload.called || null;
    document.getElementById('countMenunggu').textContent = counts.menunggu || 0;
    document.getElementById('countDipanggil').textContent = counts.dipanggil || 0;
    document.getElementById('countSelesai').textContent = counts.selesai || 0;
    document.getElementById('countTerlambat').textContent = counts.terlambat || 0;
    document.getElementById('totalActiveQuick').textContent = data.length;
    document.getElementById('lastRefreshLabel').textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

    if (called) {
        document.getElementById('currentCalledNumber').textContent = called.nomor_text || String(called.nomor).padStart(3, '0');
        document.getElementById('currentCalledName').textContent = called.name || '-';
        document.getElementById('currentCalledService').textContent = called.layanan_text || called.layanan || '-';
        document.getElementById('currentCalledMeta').textContent = 'ID ' + called.id;
        // Enable buttons
        document.getElementById('btnCallCurrent').disabled = false;
        document.getElementById('btnFinishCurrent').disabled = false;
        document.getElementById('btnLateCurrent').disabled = false;
        startCountdown(called.id);
    } else {
        document.getElementById('currentCalledNumber').textContent = '---';
        document.getElementById('currentCalledName').textContent = 'Belum ada antrian dipanggil';
        document.getElementById('currentCalledService').textContent = '-';
        document.getElementById('currentCalledMeta').textContent = '-';
        // Disable buttons
        document.getElementById('btnCallCurrent').disabled = true;
        document.getElementById('btnFinishCurrent').disabled = true;
        document.getElementById('btnLateCurrent').disabled = true;
        stopCountdown();
    }
}

function applyQueueState(payload) {
    updateDashboard(payload);
    updateTable(payload.antrians || []);
}

function updateTable(data){
    const tbody = document.querySelector('#tblAntrian tbody');
    tbody.innerHTML = '';
    data.forEach(a => {
        const tr = document.createElement('tr'); tr.dataset.id = a.id;
        tr.innerHTML = `<td class="fw-semibold">${a.nomor_text || String(a.nomor).padStart(3,'0')}</td><td>${a.name}</td><td>${a.layanan_text || a.layanan || ''}</td><td class="status"><span class="border badge text-bg-light text-secondary">${a.status}</span></td><td class="actions"><button class="btn btn-sm btn-primary btn-warning">Panggil</button> <button class="btn btn-sm btn-success btn-finish">Selesai</button> <button class="btn btn-sm btn-danger btn-late">Terlambat</button></td>`;
        tbody.appendChild(tr);
    });
}

function connectSSE() {
    if (antrianSource) {
        antrianSource.close();
    }

    antrianSource = new EventSource("<?php echo e(route('antrian.sse')); ?>");

    antrianSource.addEventListener('queue-update', function(event) {
        try {
            const payload = JSON.parse(event.data);
            applyQueueState(payload);
        } catch (err) {
            console.warn('Gagal parse event SSE admin', err);
        }
    });

    antrianSource.onerror = function() {
        console.warn('Koneksi SSE admin terganggu, browser akan mencoba menyambung ulang otomatis.');
    };
}

document.addEventListener('click', async function(e){
    // Button Panggil Ulang di card informasi
    if (e.target.matches('#btnCallCurrent')){
        if (!currentCalledId) return;
        if (!confirm('Panggil ulang nomor ini?')) return;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch('/antrian/'+currentCalledId+'/call', { method:'POST', headers:{'X-CSRF-TOKEN':token, 'Accept':'application/json'}});
        const data = await res.json();
        if (!res.ok){ alert(data.message || 'Gagal'); return; }
        startCountdown(currentCalledId);
    }
    // Button Selesai di card informasi
    if (e.target.matches('#btnFinishCurrent')){
        if (!currentCalledId) return;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/antrian/'+currentCalledId+'/finish',{method:'POST',headers:{'X-CSRF-TOKEN':token}});
        stopCountdown();
    }
    // Button Terlambat di card informasi
    if (e.target.matches('#btnLateCurrent')){
        if (!currentCalledId) return;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/antrian/'+currentCalledId+'/late',{method:'POST',headers:{'X-CSRF-TOKEN':token}});
        stopCountdown();
    }
    if (e.target.matches('.btn-warning')){
        const tr = e.target.closest('tr'); const id = tr.dataset.id;
        if (!confirm('Panggil nomor ini?')) return;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch('/antrian/'+id+'/call', { method:'POST', headers:{'X-CSRF-TOKEN':token, 'Accept':'application/json'}});
        const data = await res.json();
        if (!res.ok){ alert(data.message || 'Gagal'); return; }
        startCountdown(id); // Start countdown setelah panggil
    }
    if (e.target.matches('.btn-finish')){
        const tr = e.target.closest('tr'); const id = tr.dataset.id;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/antrian/'+id+'/finish',{method:'POST',headers:{'X-CSRF-TOKEN':token}});
        if (currentCalledId === parseInt(id)) {
            stopCountdown(); // Stop countdown ketika selesai
        }
    }
    if (e.target.matches('.btn-late')){
        const tr = e.target.closest('tr'); const id = tr.dataset.id;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        await fetch('/antrian/'+id+'/late',{method:'POST',headers:{'X-CSRF-TOKEN':token}});
        if (currentCalledId === parseInt(id)) {
            stopCountdown(); // Stop countdown ketika terlambat
        }
    }
});

connectSSE();
applyQueueState({ antrians: initialAntrians, called: initialCalled });
window.addEventListener('beforeunload', function() {
    if (antrianSource) {
        antrianSource.close();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.main', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\koleksi-buku\resources\views/antrian/admin.blade.php ENDPATH**/ ?>