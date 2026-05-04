@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Daftar Barang</h3>
    <a href="{{ route('barang.create') }}" class="btn btn-gradient-primary">+ Tambah Barang</a>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tabel Data Barang</h4>

                <form id="formCetak" action="{{ route('barang.formCetak') }}" method="POST">
                    @csrf
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCheckAll">
                                <i class="mdi mdi-checkbox-multiple-marked-outline"></i> Pilih Semua
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnUncheckAll">
                                <i class="mdi mdi-checkbox-multiple-blank-outline"></i> Batal Semua
                            </button>
                        </div>
                        <div class="gap-2 d-flex align-items-center">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnScanBarcode">
                                <i class="mdi mdi-camera"></i> Scan Barcode
                            </button>
                            <button type="submit" class="btn btn-gradient-success">
                                <i class="mdi mdi-printer"></i> Cetak Label Terpilih
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="40px"><i class="mdi mdi-checkbox-blank-outline"></i></th>
                                    <th>No</th>
                                    <th>ID Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th width="18%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data_barang as $barang)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $barang->id_barang }}"
                                               class="form-check-input item-check" style="width:18px;height:18px;">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><label class="badge badge-outline-info">{{ $barang->id_barang }}</label></td>
                                    <td>{{ $barang->nama }}</td>
                                    <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('barang.edit', $barang->id_barang) }}"
                                           class="mb-1 btn btn-sm btn-warning">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <a href="{{ route('barcode.index', $barang->id_barang) }}"
                                           class="mb-1 btn btn-sm btn-info" target="_blank">
                                            <i class="mdi mdi-barcode"></i>
                                        </a>
                                        <button type="button" class="mb-1 btn btn-sm btn-danger"
                                                onclick="hapusBarang('{{ $barang->id_barang }}')">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data barang.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<form id="formHapus" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function hapusBarang(id) {
        if (!confirm('Yakin hapus barang ini?')) return;
        const form = document.getElementById('formHapus');
        form.action = '/barang/destroy/' + id;
        form.submit();
    }

    document.getElementById('btnCheckAll').addEventListener('click', function () {
        document.querySelectorAll('.item-check').forEach(cb => cb.checked = true);
    });
    document.getElementById('btnUncheckAll').addEventListener('click', function () {
        document.querySelectorAll('.item-check').forEach(cb => cb.checked = false);
    });

    document.getElementById('formCetak').addEventListener('submit', function (e) {
        const checked = document.querySelectorAll('.item-check:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu barang untuk dicetak.');
        }
    });
</script>
<!-- Modal Camera Scanner -->
<div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7">
                        <div id="videoContainer" style="position:relative;">
                            <video id="video" autoplay muted playsinline></video>
                            <canvas id="canvas" style="display:none;"></canvas>
                        </div>
                        <div class="mt-2 text-muted small">Pastikan browser punya izin kamera dan arahkan barcode ke kamera.</div>
                        <div class="mt-2">
                            <label class="form-label small">Pilih Kamera</label>
                            <select id="cameraSelect" class="form-select form-select-sm"></select>
                        </div>
                        <div class="mt-2">
                            <label class="form-label small">Atau unggah foto barcode</label>
                            <input id="fileInputScan" type="file" accept="image/*" class="form-control form-control-sm" />
                            <img id="filePreview" src="" style="max-width:100%;margin-top:8px;display:none;border-radius:6px;" />
                        </div>
                        <div class="mt-2 text-muted small">Status: <span id="scannerStatus">Siap</span></div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-2">
                            <strong>Hasil Scan</strong>
                        </div>
                        <div id="scanResultBox" class="p-3 border rounded">
                            <div><strong>ID:</strong> <span id="scannedId">-</span></div>
                            <div><strong>Nama:</strong> <span id="scannedNama">-</span></div>
                            <div><strong>Harga:</strong> <span id="scannedHarga">-</span></div>
                        </div>
                        <div class="mt-3">
                            <button id="btnCloseScanner" type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button id="btnResetScanner" type="button" class="btn btn-sm btn-outline-primary">Scan Lagi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Beep audio -->
<audio id="beepAudio" preload="auto">
    <source src="/assets/sounds/u_edtmwfwu7c-beep-329314.mp3" type="audio/mpeg">
</audio>

@push('scripts')
{{-- Ganti library: pakai html5-qrcode yang jauh lebih stabil untuk webcam laptop --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
 
    // ── Elemen DOM ─────────────────────────────────────────────────────────
    const btnScanBarcode = document.getElementById('btnScanBarcode');
    const scannerModalEl = document.getElementById('scannerModal');
    const video          = document.getElementById('video');
    const beepAudio      = document.getElementById('beepAudio');
    const cameraSelect   = document.getElementById('cameraSelect');
    const statusEl       = document.getElementById('scannerStatus');
    const manualInput    = document.getElementById('manualBarcode');
    const manualBtn      = document.getElementById('btnManualSubmit');
    const resetBtn       = document.getElementById('btnResetScanner');
    const fileInput      = document.getElementById('fileInputScan');
    const filePreview    = document.getElementById('filePreview');
 
    // ── State ──────────────────────────────────────────────────────────────
    let html5Scanner  = null;   // instance Html5Qrcode
    let isRunning     = false;
    let scanLocked    = false;  // kunci setelah berhasil scan (agar tidak dobel)
    let videoStream   = null;
 
    // ── Helpers ────────────────────────────────────────────────────────────
 
    function setStatus(msg) {
        if (statusEl) statusEl.textContent = msg;
        console.log('[Scanner]', msg);
    }
 
    function setResult(id, nama, harga) {
        document.getElementById('scannedId').textContent    = id    ?? '-';
        document.getElementById('scannedNama').textContent  = nama  ?? '-';
        document.getElementById('scannedHarga').textContent = harga ?? '-';
    }
 
    function playBeep() {
        // Coba audio element dulu (user interaction mungkin sudah mengizinkan)
        try {
            if (beepAudio) {
                beepAudio.currentTime = 0;
                beepAudio.play().catch(() => {
                    // Jika gagal autoplay, gunakan WebAudio sebagai fallback
                    playWebAudioBeep();
                });
                return;
            }
        } catch (e) {
            console.warn('beepAudio.play() failed', e);
        }
        playWebAudioBeep();
    }

    function playWebAudioBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.type = 'sine';
            o.frequency.value = 1100;
            g.gain.value = 0.05; // jangan terlalu kencang
            o.connect(g);
            g.connect(ctx.destination);
            o.start();
            setTimeout(() => { try { o.stop(); ctx.close(); } catch (_) {} }, 140);
        } catch (e) {
            // nothing
        }
    }

    // Native BarcodeDetector (when supported) — often lebih andal di mobile
    async function tryNativeBarcodeDetector(deviceId) {
        if (!('BarcodeDetector' in window)) return false;
        try {
            const formats = [
                'code_128','code_39','ean_13','ean_8','upc_a','upc_e','qr_code'
            ];
            const detector = new window.BarcodeDetector({ formats });

            const constraints = deviceId
                ? { video: { deviceId: { exact: deviceId }, width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false }
                : { video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }, audio: false };

            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            videoStream = stream;
            video.srcObject = stream;
            await video.play().catch(() => {});
            isRunning = true;
            setStatus('Mendeteksi barcode (native) — arahkan ke kamera…');

            async function frameLoop() {
                if (!isRunning || scanLocked) return;
                try {
                    const barcodes = await detector.detect(video);
                    if (barcodes && barcodes.length) {
                        const b = barcodes[0];
                        const code = b.rawValue || b.rawData || (b.rawStringValue && String(b.rawStringValue)) || '';
                        if (code) {
                            scanLocked = true;
                            await stopScanner();
                            await handleScannedCode(code);
                            return;
                        }
                    }
                } catch (e) {
                    // ignore per-frame errors
                }
                requestAnimationFrame(frameLoop);
            }

            requestAnimationFrame(frameLoop);
            return true;
        } catch (e) {
            console.warn('tryNativeBarcodeDetector:', e);
            try { if (videoStream) { videoStream.getTracks().forEach(t => t.stop()); videoStream = null; } } catch (_) {}
            return false;
        }
    }
 
    // ── Stop scanner ───────────────────────────────────────────────────────
 
    async function stopScanner() {
        isRunning  = false;
        scanLocked = false;
 
        if (html5Scanner) {
            try {
                const state = html5Scanner.getState();
                // state 2 = SCANNING, state 3 = PAUSED
                if (state === 2 || state === 3) {
                    await html5Scanner.stop();
                }
            } catch (e) {
                console.warn('html5Scanner.stop():', e);
            }
            try { await html5Scanner.clear(); } catch (e) {}
            html5Scanner = null;
        }
 
        // matikan stream kamera yang mungkin masih aktif
        if (videoStream) {
            try { videoStream.getTracks().forEach(t => t.stop()); } catch (e) {}
            videoStream = null;
        }
        if (video.srcObject) {
            try { video.srcObject.getTracks().forEach(t => t.stop()); } catch (e) {}
            video.srcObject = null;
        }

        // Hapus elemen reader jika ada
        try {
            const old = document.getElementById('html5qr-reader');
            if (old && old.parentNode) old.parentNode.removeChild(old);
        } catch (e) {}
    }
 
    // ── Populate dropdown kamera ───────────────────────────────────────────
 
    async function populateCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const cams    = devices.filter(d => d.kind === 'videoinput');
 
            cameraSelect.innerHTML = '';
 
            if (!cams.length) {
                cameraSelect.innerHTML = '<option value="">Tidak ada kamera terdeteksi</option>';
                return null;
            }
 
            cams.forEach((cam, i) => {
                const opt       = document.createElement('option');
                opt.value       = cam.deviceId;
                opt.textContent = cam.label || ('Kamera ' + (i + 1));
                cameraSelect.appendChild(opt);
            });
 
            // pilih kamera pertama (biasanya built-in webcam laptop)
            cameraSelect.value = cams[0].deviceId;
            return cams[0].deviceId;
 
        } catch (e) {
            console.error('enumerateDevices:', e);
            return null;
        }
    }
 
    // ── Start scanner ──────────────────────────────────────────────────────
  
    async function startScanner() {
        await stopScanner();
        scanLocked = false;
        setStatus('Meminta izin kamera…');
 
        // 1. Minta izin kamera terlebih dahulu
        try {
            const tempStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            videoStream = tempStream;
            // Langsung tampilkan ke <video> sebagai preview sementara
            video.srcObject = tempStream;
            try { await video.play(); } catch (_) {}
        } catch (err) {
            setStatus('Kamera ditolak: ' + (err.message || err.name));
            return;
        }
 
        // 2. Setelah izin diberikan, enumerate kamera agar dapat label
        const selectedId = await populateCameras();
 
        // 3. Buat container untuk Html5Qrcode reader di dalam videoContainer
        //    supaya html5-qrcode bisa menampilkan video/stream langsung (tidak tersembunyi)
        let readerId = 'html5qr-reader';
        let readerDiv = document.getElementById(readerId);
        const videoContainer = document.getElementById('videoContainer');
        if (!readerDiv) {
            readerDiv = document.createElement('div');
            readerDiv.id = readerId;
            readerDiv.style.cssText = 'margin-top:-150px;position:relative;width:100%;height:360px;max-height:60vh;border-radius:8px;overflow:hidden;background:#000;';
            if (videoContainer) videoContainer.appendChild(readerDiv); else document.body.appendChild(readerDiv);
        }
 
        // 4. Hentikan preview stream sementara agar Html5Qrcode bisa ambil kamera
        if (videoStream) {
            videoStream.getTracks().forEach(t => t.stop());
            videoStream = null;
            video.srcObject = null;
        }
 
        setStatus('Memulai scanner…');

        // Jika perangkat mendukung BarcodeDetector native, coba dulu (lebih andal di banyak ponsel)
        try {
            const usedNative = await tryNativeBarcodeDetector(selectedId);
            if (usedNative) return;
        } catch (e) {
            console.warn('tryNativeBarcodeDetector threw', e);
        }

        html5Scanner = new Html5Qrcode(readerId, {
            verbose: false,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        });
 
        // Hitung area pemindaian lebih besar agar bisa membaca layar HP
        function calcQrbox() {
            const w = Math.min(900, Math.max(300, Math.floor(window.innerWidth * 0.75)));
            return { width: w, height: Math.round(w * 0.45) };
        }

        const config = {
            fps: 15,
            qrbox: calcQrbox(),
            aspectRatio: 1.5,
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.QR_CODE,
            ],
            rememberLastUsedCamera: false,
            showTorchButtonIfSupported: true,
        };
 
        const cameraConfig = selectedId
            ? { deviceId: { exact: selectedId } }
            : { facingMode: 'environment' };
 
        try {
            await html5Scanner.start(
                cameraConfig,
                config,
                // ── onScanSuccess ──────────────────────────────────────────
                async function (decodedText, decodedResult) {
                    if (scanLocked) return;
                    scanLocked = true;
 
                    console.log('[Scanner] Barcode terbaca:', decodedText, decodedResult);
                    setStatus('Barcode terbaca: ' + decodedText);
 
                    await stopScanner();
                    await handleScannedCode(decodedText);
                },
                // ── onScanFailure ──────────────────────────────────────────
                function (errorMsg) {
                    }
            );
 
            isRunning = true;
            setStatus('Mendeteksi barcode — arahkan ke kamera…');
 
        } catch (err) {
            console.error('Html5Qrcode.start() gagal:', err);
            setStatus('Error memulai kamera: ' + (err.message || JSON.stringify(err)));
 
            // Fallback: tampilkan video biasa tanpa decode otomatis
            await fallbackManualStream(selectedId);
        }
    }
 
    // ── Fallback jika Html5Qrcode gagal ──────────────────────────────────
    // Tampilkan kamera via getUserMedia biasa + canvas snapshot decode setiap 400ms
 
    async function fallbackManualStream(deviceId) {
        setStatus('Mode fallback — gunakan input manual jika tidak terbaca');
 
        try {
            const constraints = {
                video: deviceId ? { deviceId: { exact: deviceId } } : { facingMode: 'environment' },
                audio: false,
            };
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            videoStream   = stream;
            video.srcObject = stream;
            await video.play().catch(() => {});
            isRunning = true;
 
            // Canvas snapshot decode loop
            const canvas  = document.createElement('canvas');
            const ctx     = canvas.getContext('2d', { willReadFrequently: true });
            const ZXing   = window.ZXing;   // ZXing mungkin sudah di-load sebelumnya
 
            // coba load ZXing jika belum ada
            if (!ZXing) {
                await loadScript('https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js');
            }
 
            async function snapshotLoop() {
                if (!isRunning || scanLocked) return;
                if (video.readyState < 2 || video.videoWidth === 0) {
                    setTimeout(snapshotLoop, 300);
                    return;
                }
                canvas.width  = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0);
 
                try {
                    const reader = new window.ZXing.BrowserMultiFormatReader();
                    const imgData = canvas.toDataURL('image/png');
                    const img = new Image();
                    img.src = imgData;
                    await img.decode();
                    const result = await reader.decodeFromImageElement(img);
                    if (result && !scanLocked) {
                        scanLocked = true;
                        await handleScannedCode(result.getText());
                        return;
                    }
                } catch (_) {}
 
                if (isRunning) setTimeout(snapshotLoop, 350);
            }
 
            setTimeout(snapshotLoop, 500);
 
        } catch (err) {
            setStatus('Tidak bisa mengakses kamera. Gunakan input manual.');
        }
    }
 
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            if (document.querySelector('script[src="' + src + '"]')) { resolve(); return; }
            const s   = document.createElement('script');
            s.src     = src;
            s.onload  = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }
 
    // ── Lookup ke server ───────────────────────────────────────────────────
 
    async function handleScannedCode(code) {
        playBeep();
        setResult(code, 'Mencari…', '');
 
        try {
            const res  = await fetch('{{ route('barcode-scanner.lookup') }}', {
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
                setStatus('✓ Barang ditemukan');
            } else {
                setResult(code, '⚠ Barang tidak ditemukan', '');
                setStatus('Barang tidak ditemukan untuk kode: ' + code);
            }
        } catch (e) {
            console.error('Fetch error:', e);
            setResult(code, '✗ Gagal menghubungi server', '');
            setStatus('Error jaringan');
        }
    }
 
    // ── Upload gambar barcode ──────────────────────────────────────────────
 
    fileInput.addEventListener('change', async function () {
        const f = this.files && this.files[0];
        if (!f) return;
 
        const url          = URL.createObjectURL(f);
        filePreview.src    = url;
        filePreview.style.display = 'block';
        setStatus('Membaca barcode dari gambar…');
 
        try {
            // Coba Html5Qrcode file scan dulu
            if (html5Scanner || true) {
                const tempScanner = new Html5Qrcode('html5qr-reader');
                try {
                    const result = await tempScanner.scanFile(f, false);
                    if (result) {
                        setStatus('Barcode terbaca dari gambar');
                        await handleScannedCode(result);
                        return;
                    }
                } catch (e) {
                    console.warn('Html5Qrcode scanFile gagal, coba ZXing:', e);
                }
            }
 
            // Fallback ZXing
            await loadScript('https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js');
            const reader = new window.ZXing.BrowserMultiFormatReader();
            const img    = new Image();
            img.src      = url;
            await img.decode();
 
            let result = null;
            try { result = await reader.decodeFromImageElement(img); } catch (_) {}
 
            if (!result) result = await decodeWithPreprocess(reader, img);
 
            if (result) {
                setStatus('Barcode terbaca dari gambar');
                await handleScannedCode(result.getText());
            } else {
                setStatus('Gagal membaca barcode dari gambar. Coba foto lebih jelas.');
            }
        } catch (err) {
            console.error('File decode error:', err);
            setStatus('Error membaca gambar');
        }
    });
 
    async function decodeWithPreprocess(reader, img) {
        const off   = document.createElement('canvas');
        const MAX   = 1600;
        const scale = Math.min(1, MAX / Math.max(img.naturalWidth || 100, img.naturalHeight || 100));
        off.width   = Math.round((img.naturalWidth  || 300) * scale);
        off.height  = Math.round((img.naturalHeight || 150) * scale);
        const ctx   = off.getContext('2d');
        ctx.drawImage(img, 0, 0, off.width, off.height);
 
        const imgData = ctx.getImageData(0, 0, off.width, off.height);
        const d = imgData.data;
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
        ctx.putImageData(imgData, 0, 0);
 
        const pImg = new Image();
        pImg.src   = off.toDataURL('image/png');
        await pImg.decode();
        try { return await reader.decodeFromImageElement(pImg); } catch (_) { return null; }
    }
 
    // ── Event: buka modal ─────────────────────────────────────────────────
 
    btnScanBarcode.addEventListener('click', function () {
        beepAudio.play().then(() => beepAudio.pause()).catch(() => {});
        new bootstrap.Modal(scannerModalEl).show();
    });
 
    // ── Event: modal fully shown → start kamera ───────────────────────────
 
    scannerModalEl.addEventListener('shown.bs.modal', async function () {
        setResult('-', '-', '-');
        filePreview.style.display = 'none';
        fileInput.value = '';
        await startScanner();
    });
 
    // ── Event: modal ditutup → stop kamera ───────────────────────────────
 
    scannerModalEl.addEventListener('hide.bs.modal', async function () {
        await stopScanner();
    });
 
    // ── Event: ganti kamera ───────────────────────────────────────────────
 
    cameraSelect.addEventListener('change', async function () {
        setResult('-', '-', '-');
        await startScanner();
    });
 
    // ── Event: Scan Lagi ──────────────────────────────────────────────────
 
    resetBtn.addEventListener('click', async function () {
        setResult('-', '-', '-');
        await startScanner();
    });
 
    // ── Event: input manual ───────────────────────────────────────────────
 
    manualBtn.addEventListener('click', async function () {
        const code = (manualInput.value || '').trim();
        if (!code) { alert('Masukkan kode barcode terlebih dahulu'); return; }
        stopScanner();
        await handleScannedCode(code);
    });
 
    manualInput.addEventListener('keydown', async function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const code = this.value.trim();
        if (!code) return;
        await stopScanner();
        await handleScannedCode(code);
    });
 
});
</script>
@endpush
@endsection
