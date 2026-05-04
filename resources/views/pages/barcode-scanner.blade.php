{{-- @extends('layouts.main')

@section('content')
<div class="container px-4 py-6">
    <h3 class="mb-4">Barcode Scanner (Camera)</h3>
    <div class="row">
        <div class="col-md-8">
            <video id="video" width="100%" autoplay muted playsinline style="border-radius:8px;background:#000"></video>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded">
                <div><strong>ID:</strong> <span id="scannedId">-</span></div>
                <div><strong>Nama:</strong> <span id="scannedNama">-</span></div>
                <div><strong>Harga:</strong> <span id="scannedHarga">-</span></div>
            </div>
            <div class="mt-3">
                <label class="form-label small">Unggah foto barcode (jika tidak bisa scan dari layar)</label>
                <input id="fileInput" type="file" accept="image/*" class="form-control form-control-sm" />
                <img id="filePreview" src="" style="max-width:100%;margin-top:8px;display:none;border-radius:6px;" />
            </div>
            <div class="mt-3">
                <button id="btnStop" class="btn btn-secondary btn-sm">Stop</button>
                <button id="btnReset" class="btn btn-outline-primary btn-sm">Scan Lagi</button>
            </div>
        </div>
    </div>
</div>

<audio id="beepAudio" preload="auto">
    <source src="data:audio/wav;base64,UklGRkQAAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQAAAAA=" type="audio/wav">
</audio>

@push('scripts')
<script src="https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnScanBarcode  = document.getElementById('btnScanBarcode');
    const scannerModalEl  = document.getElementById('scannerModal');
    const video           = document.getElementById('video');
    const beepAudio       = document.getElementById('beepAudio');
    const cameraSelect    = document.getElementById('cameraSelect');
    const manualInput     = document.getElementById('manualBarcode');
    const manualBtn       = document.getElementById('btnManualSubmit');
    const resetBtn        = document.getElementById('btnResetScanner');
    const statusEl        = document.getElementById('scannerStatus');
    const fileInput       = document.getElementById('fileInputScan');
    const filePreview     = document.getElementById('filePreview');
 
    let codeReader    = null;   // instance ZXing aktif
    let isScanning    = false;  // flag agar tidak double-start
    let lastDeviceId  = null;
 
    // ─── helpers ────────────────────────────────────────────────────────────
 
    function setStatus(msg) {
        if (statusEl) statusEl.textContent = msg;
    }
 
    function setResult(id, nama, harga) {
        document.getElementById('scannedId').textContent    = id    || '-';
        document.getElementById('scannedNama').textContent  = nama  || '-';
        document.getElementById('scannedHarga').textContent = harga || '-';
    }
 
    function playBeep() {
        try {
            beepAudio.currentTime = 0;
            beepAudio.play().catch(() => {});
        } catch (e) {}
    }
 
    // ─── stop scanner ────────────────────────────────────────────────────────
 
    function stopScanner() {
        isScanning = false;
        if (codeReader) {
            try { codeReader.reset(); } catch (e) {}
            codeReader = null;
        }
        // matikan semua track kamera
        if (video && video.srcObject) {
            try { video.srcObject.getTracks().forEach(t => t.stop()); } catch (e) {}
            video.srcObject = null;
        }
    }
 
    // ─── populate kamera dropdown ─────────────────────────────────────────────
 
    async function populateCameras() {
        try {
            // pakai enumerateDevices agar tidak perlu instance ZXing dulu
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(d => d.kind === 'videoinput');
 
            cameraSelect.innerHTML = '';
            if (!videoDevices.length) {
                cameraSelect.innerHTML = '<option value="">Tidak ada kamera</option>';
                return;
            }
 
            videoDevices.forEach((d, i) => {
                const opt = document.createElement('option');
                opt.value = d.deviceId;
                opt.textContent = d.label || ('Kamera ' + (i + 1));
                cameraSelect.appendChild(opt);
            });
 
            // gunakan kamera belakang jika ada, jika tidak pilih pertama
            const backCam = videoDevices.find(d =>
                /back|rear|environment/i.test(d.label)
            );
            cameraSelect.value = backCam ? backCam.deviceId : videoDevices[0].deviceId;
            lastDeviceId = cameraSelect.value;
 
        } catch (e) {
            console.warn('enumerateDevices gagal:', e);
        }
    }
 
    // ─── start scanner ────────────────────────────────────────────────────────
 
    async function startScanner() {
        if (isScanning) return;   // jangan double-start
        stopScanner();            // bersihkan state lama
 
        setStatus('Meminta izin kamera…');
 
        // 1. Minta izin kamera eksplisit terlebih dahulu
        let stream;
        try {
            const deviceId = cameraSelect.value || lastDeviceId;
            const constraints = {
                video: deviceId
                    ? { deviceId: { exact: deviceId } }
                    : { facingMode: 'environment' },
                audio: false,
            };
            stream = await navigator.mediaDevices.getUserMedia(constraints);
        } catch (err) {
            setStatus('Kamera ditolak: ' + (err.message || err.name));
            return;
        }
 
        // 2. Pasang stream langsung ke <video> agar langsung tampil
        video.srcObject = stream;
        try { await video.play(); } catch (e) {}
 
        // 3. Populate dropdown kamera (label baru tersedia setelah izin diberikan)
        await populateCameras();
 
        // 4. Mulai decode dengan ZXing — gunakan decodeFromStream agar kita
        //    bisa memberikan stream yang sudah kita buat sendiri
        setStatus('Mendeteksi barcode…');
        isScanning  = true;
        codeReader  = new ZXing.BrowserMultiFormatReader();
 
        // decodeFromStream tidak ada di semua versi; gunakan decodeFromVideoDevice
        // tapi kita sudah set video.srcObject di atas, jadi ZXing tinggal decode
        try {
            // Pasang decode loop manual agar lebih reliable
            decodeLoop();
        } catch (e) {
            setStatus('Error scanner: ' + e.message);
            isScanning = false;
        }
    }
 
    // ─── decode loop (manual, lebih reliable dari decodeFromVideoDevice) ──────
 
    async function decodeLoop() {
        while (isScanning && codeReader) {
            try {
                const result = await codeReader.decodeFromVideoElement(video);
                if (result && isScanning) {
                    stopScanner();
                    setStatus('Barcode terbaca!');
                    await handleScannedCode(result.getText());
                    return;
                }
            } catch (err) {
                // NotFoundException = normal (belum ada barcode di frame)
                // abaikan dan lanjut loop
                if (err && err.name === 'NotFoundException') {
                    // tunggu sebentar sebelum frame berikutnya
                    await sleep(120);
                    continue;
                }
                // Error lain (ChecksumException, FormatException) → abaikan juga
                await sleep(120);
            }
        }
    }
 
    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
 
    // ─── lookup ke server ─────────────────────────────────────────────────────
 
    async function handleScannedCode(code) {
        playBeep();
        setResult(code, 'Mencari…', '');
 
        try {
            const res = await fetch('{{ route('barcode-scanner.lookup') }}', {
                method:  'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ barcode: code }),
            });
 
            const json = await res.json();
 
            if (json.success) {
                setResult(
                    json.data.id_barang,
                    json.data.nama,
                    'Rp ' + Number(json.data.harga).toLocaleString('id-ID')
                );
                setStatus('Barang ditemukan ✓');
            } else {
                setResult(code, 'Barang tidak ditemukan', '');
                setStatus('Barang tidak ditemukan');
            }
        } catch (e) {
            console.error(e);
            setResult(code, 'Gagal menghubungi server', '');
            setStatus('Error jaringan');
        }
    }
 
    // ─── event: buka modal ───────────────────────────────────────────────────
 
    btnScanBarcode.addEventListener('click', function () {
        // unlock audio (harus di dalam user gesture)
        beepAudio.play().then(() => beepAudio.pause()).catch(() => {});
        const modal = new bootstrap.Modal(scannerModalEl);
        modal.show();
    });
 
    // ─── event: modal terbuka penuh → baru start kamera ─────────────────────
 
    scannerModalEl.addEventListener('shown.bs.modal', async function () {
        setResult('-', '-', '-');
        setStatus('Memulai kamera…');
        filePreview.style.display = 'none';
        fileInput.value = '';
        await startScanner();
    });
 
    // ─── event: modal ditutup → stop kamera ─────────────────────────────────
 
    scannerModalEl.addEventListener('hidden.bs.modal', function () {
        stopScanner();
        setStatus('Siap');
    });
 
    // ─── event: ganti kamera ────────────────────────────────────────────────
 
    cameraSelect.addEventListener('change', async function () {
        lastDeviceId = this.value;
        setResult('-', '-', '-');
        await startScanner();
    });
 
    // ─── event: tombol Scan Lagi ─────────────────────────────────────────────
 
    resetBtn.addEventListener('click', async function () {
        setResult('-', '-', '-');
        await startScanner();
    });
 
    // ─── event: input manual ─────────────────────────────────────────────────
 
    manualBtn.addEventListener('click', async function () {
        const code = (manualInput.value || '').trim();
        if (!code) { alert('Masukkan kode barcode'); return; }
        stopScanner();
        await handleScannedCode(code);
    });
 
    manualInput.addEventListener('keydown', async function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const code = this.value.trim();
        if (!code) return;
        stopScanner();
        await handleScannedCode(code);
    });
 
    // ─── event: upload gambar ─────────────────────────────────────────────────
 
    fileInput.addEventListener('change', async function () {
        const f = this.files && this.files[0];
        if (!f) return;
 
        const url = URL.createObjectURL(f);
        filePreview.src = url;
        filePreview.style.display = 'block';
        setStatus('Membaca barcode dari gambar…');
 
        const reader = new ZXing.BrowserMultiFormatReader();
 
        try {
            const img = new Image();
            img.src = url;
            await img.decode();
 
            let result = null;
 
            // coba decode langsung
            try { result = await reader.decodeFromImageElement(img); } catch (_) {}
 
            // jika gagal, coba preprocessing (grayscale + threshold)
            if (!result) {
                result = await decodeWithPreprocess(reader, img);
            }
 
            if (result) {
                stopScanner();
                await handleScannedCode(result.getText());
            } else {
                setStatus('Gagal membaca barcode dari gambar');
            }
        } catch (err) {
            console.error(err);
            setStatus('Error membaca gambar');
        }
    });
 
    async function decodeWithPreprocess(reader, img) {
        const off = document.createElement('canvas');
        const MAX = 1400;
        const scale = Math.min(1, MAX / Math.max(img.naturalWidth, img.naturalHeight));
        off.width  = Math.max(50, Math.round(img.naturalWidth  * scale));
        off.height = Math.max(50, Math.round(img.naturalHeight * scale));
        const ctx = off.getContext('2d');
        ctx.drawImage(img, 0, 0, off.width, off.height);
 
        const imageData = ctx.getImageData(0, 0, off.width, off.height);
        const d = imageData.data;
        let sum = 0;
        for (let i = 0; i < d.length; i += 4) {
            const lum = 0.299 * d[i] + 0.587 * d[i+1] + 0.114 * d[i+2];
            d[i] = d[i+1] = d[i+2] = lum;
            sum += lum;
        }
        const avg = sum / (d.length / 4);
        for (let i = 0; i < d.length; i += 4) {
            const v = d[i] > avg ? 255 : 0;
            d[i] = d[i+1] = d[i+2] = v;
        }
        ctx.putImageData(imageData, 0, 0);
 
        const pImg = new Image();
        pImg.src = off.toDataURL('image/png');
        await pImg.decode();
 
        try { return await reader.decodeFromImageElement(pImg); } catch (_) { return null; }
    }
});
</script>
@endpush
@endsection --}}
