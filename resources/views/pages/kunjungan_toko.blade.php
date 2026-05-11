@extends('layouts.main')

@section('content')
<div class="flex-wrap gap-2 page-header d-flex justify-content-between align-items-center">
    <h3 class="page-title">Kunjungan Toko</h3>
    <a href="{{ route('home') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="flex-wrap gap-2 mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 card-title">List Toko</h4>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-bordered">
                        <thead>
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Toko</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Accuracy</th>
                                <th>Cetak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tokos as $toko)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $toko->barcode }}</div>
                                    <div class="mt-2 overflow-auto">{!! (new \Picqer\Barcode\Renderers\HtmlRenderer())->render((new \Picqer\Barcode\Types\TypeCode128())->getBarcode($toko->barcode)) !!}</div>
                                </td>
                                <td>{{ $toko->name }}</td>
                                <td>{{ $toko->latitude }}</td>
                                <td>{{ $toko->longitude }}</td>
                                <td>{{ $toko->accuracy }}</td>
                                <td>
                                    <a href="{{ route('kunjungan.barcode', $toko) }}" target="_blank" class="btn btn-sm btn-primary">Cetak Barcode</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada data toko</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Input Titik Awal</h4>
                <p class="mb-3 text-muted small">Tombol Geoloc akan mengambil lokasi terbaik saat ini menggunakan geolocation.</p>

                <form method="POST" action="{{ route('kunjungan.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Toko</label>
                        <input type="text" name="name" class="form-control" placeholder="Masukkan nama toko" required>
                    </div>

                    <div class="row g-2">
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Latitude</label>
                            <input type="text" id="toko_lat" name="latitude" class="form-control" readonly required>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Longitude</label>
                            <input type="text" id="toko_lng" name="longitude" class="form-control" readonly required>
                        </div>
                        <div class="mb-3 col-md-4">
                            <label class="form-label">Accuracy</label>
                            <input type="text" id="toko_acc" name="accuracy" class="form-control" readonly required>
                        </div>
                    </div>

                    <div class="gap-2 d-flex justify-content-end">
                        <button type="button" id="btnPickupToko" class="btn btn-primary">Geoloc</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Titik Kunjungan</h4>
                <p class="mb-3 text-muted small">Scan barcode toko, tampilkan data dari DB, lalu ambil lokasi sales untuk cek diterima atau ditolak.</p>

                <div class="mb-3">
                    <label class="form-label">Barcode Scanner</label>
                    <div class="input-group">
                        <input type="text" id="visit_barcode" class="form-control" placeholder="Scan atau ketik barcode toko">
                        <button type="button" id="btnOpenScanner" class="btn btn-outline-primary">Scanner</button>
                    </div>
                </div>

                <div id="tokoDbBox" class="p-3 mb-3 border rounded-4 bg-primary-subtle border-primary">
                    <div class="mb-2 fw-bold">Data dari DB hasil scan barcode</div>
                    <div>Barcode: <span id="db_barcode">-</span></div>
                    <div>Nama toko: <span id="db_name">-</span></div>
                    <div>Latitude: <span id="db_latitude">-</span></div>
                    <div>Longitude: <span id="db_longitude">-</span></div>
                    <div>Accuracy: <span id="db_accuracy">-</span></div>
                </div>

                <div class="p-3 mb-3 border rounded-4 bg-info-subtle border-info">
                    <div class="mb-2 fw-bold">Data titik kunjungan</div>
                    <div>Status: <span id="visit_status">Menunggu proses</span></div>
                    <div>Latitude: <span id="visit_latitude">-</span></div>
                    <div>Longitude: <span id="visit_longitude">-</span></div>
                    <div>Accuracy: <span id="visit_accuracy">-</span></div>
                    <div>Jarak: <span id="visit_distance">-</span></div>
                    <div>Threshold efektif: <span id="visit_threshold">-</span></div>
                </div>

                <div class="gap-2 d-flex justify-content-end">
                    <button type="button" id="btnAmbilLokasi" class="btn btn-warning">Ambil Lokasi</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="scannerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Barcode Scanner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <video id="scannerVideo" width="100%" autoplay muted playsinline style="border-radius:8px;background:#000"></video>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded">
                            <div class="mb-2"><strong>Hasil Scan</strong></div>
                            <div><strong>ID/Barcode:</strong> <span id="scannerResultText">-</span></div>
                            <div class="mt-2 text-muted small">Status: <span id="scannerStatus">Siap</span></div>
                        </div>
                        <div class="mt-3">
                            <div class="mb-3 row g-2">
                                <div class="col-sm-6">
                                    <label class="mb-1 form-label small">Pilih Kamera</label>
                                    <select id="cameraSelect" class="form-select form-select-sm"></select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="mb-1 form-label small">Unggah Foto Barcode</label>
                                    <input id="fileInputScan" type="file" accept="image/*" class="form-control form-control-sm" />
                                </div>
                            </div>
                            <img id="filePreview" src="" style="width:100%;max-height:150px;object-fit:contain;margin-bottom:10px;display:none;border-radius:6px;border:1px solid #e3e8ef;padding:4px;background:#fff;" />
                        </div>
                        <div class="mt-3">
                            <button id="btnStopScanner" class="btn btn-secondary btn-sm">Stop</button>
                            <button id="btnResetScanner" class="btn btn-outline-primary btn-sm">Scan Lagi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js"></script>
<script src="https://unpkg.com/piexifjs@0.0.1/piexif.min.js"></script>
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
// Helper: Extract EXIF orientation from blob
async function getExifOrientation(blob) {
    try {
        const arrayBuffer = await blob.arrayBuffer();
        const exif = piexif.load(arrayBuffer);
        const orientation = exif['0th'][piexif.ImageIFD.Orientation]?.[0] || 1;
        return orientation;
    } catch (e) {
        console.warn('EXIF read error:', e);
        return 1; // default: no rotation
    }
}

// Helper: Apply EXIF rotation to canvas
function applyExifRotation(ctx, canvas, orientation) {
    const { width, height } = canvas;
    switch(orientation) {
        case 3: // 180°
            ctx.translate(width, height);
            ctx.rotate(Math.PI);
            break;
        case 6: // 90° CW
            ctx.translate(width, 0);
            ctx.rotate(Math.PI / 2);
            [canvas.width, canvas.height] = [height, width];
            break;
        case 8: // 90° CCW
            ctx.translate(0, height);
            ctx.rotate(-Math.PI / 2);
            [canvas.width, canvas.height] = [height, width];
            break;
        case 2: // flip horizontal
            ctx.translate(width, 0);
            ctx.scale(-1, 1);
            break;
        case 4: // flip vertical
            ctx.translate(0, height);
            ctx.scale(1, -1);
            break;
        case 5: // transpose
            ctx.translate(width, 0);
            ctx.scale(-1, 1);
            ctx.rotate(Math.PI / 2);
            [canvas.width, canvas.height] = [height, width];
            break;
        case 7: // transverse
            ctx.translate(0, height);
            ctx.scale(1, -1);
            ctx.rotate(Math.PI / 2);
            [canvas.width, canvas.height] = [height, width];
            break;
    }
}

function getAccuratePosition(targetAccuracy = 50, maxWait = 20000) {
    return new Promise((resolve, reject) => {
        let bestResult = null;
        const startTime = Date.now();
        const watchId = navigator.geolocation.watchPosition(
            (position) => {
                const acc = position.coords.accuracy;
                if (!bestResult || acc < bestResult.coords.accuracy) {
                    bestResult = position;
                }
                if (acc <= targetAccuracy) {
                    navigator.geolocation.clearWatch(watchId);
                    resolve(bestResult);
                    return;
                }
                if (Date.now() - startTime >= maxWait) {
                    navigator.geolocation.clearWatch(watchId);
                    if (bestResult) resolve(bestResult);
                    else reject(new Error('Timeout, tidak dapat posisi'));
                }
            },
            (error) => reject(error),
            { enableHighAccuracy: true, maximumAge: 0, timeout: maxWait }
        );
    });
}

document.addEventListener('DOMContentLoaded', function () {
    console.log('═══════════════════════════════════════════');
    console.log('🎫 Kunjungan Toko Scanner - Initialized');
    console.log('═══════════════════════════════════════════');
    
    const pickupBtn = document.getElementById('btnPickupToko');
    const ambilLokasiBtn = document.getElementById('btnAmbilLokasi');
    const openScannerBtn = document.getElementById('btnOpenScanner');
    const scannerModalEl = document.getElementById('scannerModal');
    const scannerVideo = document.getElementById('scannerVideo');
    const scannerResultText = document.getElementById('scannerResultText');
    const scannerStatus = document.getElementById('scannerStatus');
    const visitBarcode = document.getElementById('visit_barcode');

    const dbBox = {
        barcode: document.getElementById('db_barcode'),
        name: document.getElementById('db_name'),
        latitude: document.getElementById('db_latitude'),
        longitude: document.getElementById('db_longitude'),
        accuracy: document.getElementById('db_accuracy'),
    };

    const visitBox = {
        status: document.getElementById('visit_status'),
        latitude: document.getElementById('visit_latitude'),
        longitude: document.getElementById('visit_longitude'),
        accuracy: document.getElementById('visit_accuracy'),
        distance: document.getElementById('visit_distance'),
        threshold: document.getElementById('visit_threshold'),
    };

    let scanner = null;
    let currentVisit = null;

    function setDbInfo(data) {
        dbBox.barcode.textContent = data?.barcode ?? '-';
        dbBox.name.textContent = data?.name ?? '-';
        dbBox.latitude.textContent = data?.latitude ?? '-';
        dbBox.longitude.textContent = data?.longitude ?? '-';
        dbBox.accuracy.textContent = data?.accuracy ?? '-';
    }

    function setVisitInfo(data) {
        visitBox.status.textContent = data?.status_text ?? 'Menunggu proses';
        visitBox.latitude.textContent = data?.latitude ?? '-';
        visitBox.longitude.textContent = data?.longitude ?? '-';
        visitBox.accuracy.textContent = data?.accuracy ?? '-';
        visitBox.distance.textContent = data?.distance_text ?? '-';
        visitBox.threshold.textContent = data?.effective_threshold_text ?? '-';
    }

    async function fetchTokoByBarcode(barcode) {
        try {
            console.log('📡 Sending request to lookup API...');
            
            // Get CSRF token - with fallback methods
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            // Fallback: Try to get from form if meta tag not found
            if (!csrfToken) {
                csrfToken = document.querySelector('input[name="_token"]')?.value;
            }
            
            if (!csrfToken) {
                throw new Error('CSRF token tidak ditemukan - halaman mungkin belum fully loaded');
            }
            
            console.log('✓ CSRF token acquired');
            
            const response = await fetch("{{ route('kunjungan.lookup') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ barcode: barcode.trim() })
            });

            console.log('📬 Response status:', response.status);
            const data = await response.json();
            console.log('📦 Response data:', data);
            
            if (!response.ok || !data.success) {
                const msg = data.message || `HTTP ${response.status}: Toko tidak ditemukan`;
                throw new Error(msg);
            }

            setDbInfo(data.data);
            return data.data;
        } catch (err) {
            console.error('🔴 Fetch error:', err.message);
            throw err;
        }
    }

    async function evaluateVisit(position) {
        const barcode = visitBarcode.value.trim();
        if (!barcode) {
            alert('Scan barcode toko terlebih dahulu');
            return;
        }

        // Get CSRF token - with fallback methods
        let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            csrfToken = document.querySelector('input[name="_token"]')?.value;
        }
        if (!csrfToken) {
            throw new Error('CSRF token tidak ditemukan');
        }

        const response = await fetch("{{ route('kunjungan.check') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                barcode,
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
                threshold: 300,
            })
        });

        const data = await response.json();
        if (!response.ok || !data.ok) {
            throw new Error(data.message || 'Gagal memproses kunjungan');
        }

        currentVisit = {
            status_text: data.status === 'accepted' ? 'DITERIMA' : 'DITOLAK',
            latitude: data.visit.latitude,
            longitude: data.visit.longitude,
            accuracy: data.visit.accuracy,
            distance_text: data.distance_text + ' m',
            effective_threshold_text: data.effective_threshold_text + ' m',
        };

        setDbInfo(data.toko);
        setVisitInfo(currentVisit);
    }

    async function openScanner() {
        const modal = new bootstrap.Modal(scannerModalEl);
        modal.show();
    }

    function setStatus(msg) {
        if (scannerStatus) scannerStatus.textContent = msg;
    }

    let codeReader = null;
    let isScanning = false;
    let lastDeviceId = null;
    let html5Scanner = null;
    let canvasDecodeIntervalId = null;

    async function populateCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const videoDevices = devices.filter(d => d.kind === 'videoinput');
            const cameraSelect = document.getElementById('cameraSelect');
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
            const backCam = videoDevices.find(d => /back|rear|environment/i.test(d.label));
            cameraSelect.value = backCam ? backCam.deviceId : videoDevices[0].deviceId;
            lastDeviceId = cameraSelect.value;
        } catch (e) { console.warn('enumerateDevices gagal:', e); }
    }

    async function decodeLoop() {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        const hints = new Map();
        hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, [ZXing.BarcodeFormat.CODE_128]);
        const reader = new ZXing.BrowserMultiFormatReader(hints);

        const decodeFrame = async () => {
            if (!isScanning || !scannerVideo) return;

            try {
                if (scannerVideo.readyState >= 2) {
                    canvas.width = scannerVideo.videoWidth;
                    canvas.height = scannerVideo.videoHeight;
                    
                    if (canvas.width > 0 && canvas.height > 0) {
                        ctx.drawImage(scannerVideo, 0, 0);
                        
                        for (let scale = 1; scale <= 2 && isScanning; scale++) {
                            try {
                                let decodeCanvas = canvas;
                                
                                if (scale > 1) {
                                    decodeCanvas = document.createElement('canvas');
                                    decodeCanvas.width = canvas.width * scale;
                                    decodeCanvas.height = canvas.height * scale;
                                    const dctx = decodeCanvas.getContext('2d');
                                    dctx.drawImage(canvas, 0, 0, decodeCanvas.width, decodeCanvas.height);
                                }
                                
                                const imageData = decodeCanvas.getContext('2d').getImageData(0, 0, decodeCanvas.width, decodeCanvas.height);
                                const d = imageData.data;
                                let sum = 0;
                                const lums = [];
                                
                                for (let i = 0; i < d.length; i += 4) {
                                    const lum = 0.299 * d[i] + 0.587 * d[i+1] + 0.114 * d[i+2];
                                    lums.push(lum);
                                    sum += lum;
                                }
                                
                                const avg = sum / lums.length;
                                const variance = lums.reduce((s, l) => s + Math.pow(l - avg, 2), 0) / lums.length;
                                const stdDev = Math.sqrt(variance);
                                
                                for (let i = 0; i < d.length; i += 4) {
                                    const lum = lums[i / 4];
                                    const normalized = (lum - avg) / (stdDev || 1) * 50 + 128;
                                    const clamped = Math.max(0, Math.min(255, normalized));
                                    const v = clamped > 128 ? 255 : 0;
                                    d[i] = d[i+1] = d[i+2] = v;
                                }
                                
                                decodeCanvas.getContext('2d').putImageData(imageData, 0, 0);
                                const img = new Image();
                                img.src = decodeCanvas.toDataURL('image/png');
                                
                                await new Promise((resolve) => {
                                    img.onload = () => resolve();
                                    img.onerror = () => resolve();
                                    setTimeout(resolve, 100);
                                });

                                const result = await reader.decodeFromImageElement(img);
                                if (result && isScanning) {
                                    const text = result.getText();
                                    console.log('✓ Barcode detected (canvas):', text);
                                    scannerResultText.textContent = text;
                                    setStatus('Barcode terbaca!');
                                    isScanning = false;
                                    stopScanner();
                                    await handleScannedCode(text);
                                    return;
                                }
                            } catch (e) {}
                        }
                    }
                }
            } catch (err) {}

            if (isScanning) {
                canvasDecodeIntervalId = setTimeout(decodeFrame, 150);
            }
        };

        canvasDecodeIntervalId = setTimeout(decodeFrame, 300);
    }

    async function handleScannedCode(code) {
        if (!code) {
            console.warn('⚠ Empty barcode code');
            return;
        }
        console.log('🔍 Looking up barcode:', code);
        setStatus('Mencari…');
        try {
            visitBarcode.value = code;
            const tokoData = await fetchTokoByBarcode(code);
            console.log('✓ Toko found:', tokoData);
            setStatus('Toko ditemukan ✓');
            // Tutup modal otomatis setelah data berhasil di-fetch
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(scannerModalEl);
                if (modal) {
                    modal.hide();
                } else {
                    scannerModalEl.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
                
                // Scroll ke tokoDbBox untuk menampilkan data hasil scan
                setTimeout(() => {
                    const tokoBox = document.getElementById('tokoDbBox');
                    if (tokoBox) {
                        tokoBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 300);
            }, 500);
        } catch (e) {
            console.error('❌ Error:', e);
            setStatus('Toko tidak ditemukan');
            alert(e.message || 'Toko tidak ditemukan');
        }
    }

    async function startScanner() {
        if (isScanning) return;
        stopScanner();
        setStatus('Meminta izin kamera…');
        let stream;
        try {
            await populateCameras();
            const cameraSelect = document.getElementById('cameraSelect');
            const deviceId = cameraSelect.value;
            const constraints = { 
                video: deviceId 
                    ? { 
                        deviceId: { exact: deviceId },
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                    : { 
                        facingMode: 'environment',
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    },
                audio: false 
            };
            stream = await navigator.mediaDevices.getUserMedia(constraints);
        } catch (err) {
            console.error('Camera error:', err);
            setStatus('Kamera ditolak: ' + (err.message || err.name));
            return;
        }
        
        scannerVideo.srcObject = stream;
        
        try {
            await scannerVideo.play();
        } catch (e) {
            console.warn('Video play error:', e);
        }
        
        if (scannerVideo.readyState !== scannerVideo.HAVE_ENOUGH_DATA) {
            await new Promise(resolve => {
                const handler = () => {
                    if (scannerVideo.readyState === scannerVideo.HAVE_ENOUGH_DATA) {
                        scannerVideo.removeEventListener('loadedmetadata', handler);
                        resolve();
                    }
                };
                scannerVideo.addEventListener('loadedmetadata', handler);
                setTimeout(resolve, 2000);
            });
        }
        
        isScanning = true;
        setStatus('Mendeteksi barcode… Arahkan ke barcode');
        console.log('✓ Scanner started (canvas snapshot mode)');
        decodeLoop();
    }

    function stopScanner() {
        isScanning = false;
        
        if (canvasDecodeIntervalId) {
            clearTimeout(canvasDecodeIntervalId);
            canvasDecodeIntervalId = null;
        }
        
        if (codeReader) {
            try { codeReader.reset(); } catch (e) {}
            codeReader = null;
        }
        
        if (html5Scanner) {
            try {
                const state = html5Scanner.getState();
                if (state === 2 || state === 3) {
                    html5Scanner.stop();
                }
            } catch (e) {}
            try { html5Scanner.clear(); } catch (e) {}
            html5Scanner = null;
        }
        
        if (scannerVideo && scannerVideo.srcObject) {
            try {
                scannerVideo.srcObject.getTracks().forEach(track => {
                    try { track.stop(); } catch (e) {}
                });
            } catch (e) {}
            scannerVideo.srcObject = null;
        }
        
        try {
            const old = document.getElementById('html5qr-reader');
            if (old && old.parentNode) old.parentNode.removeChild(old);
        } catch (e) {}
    }

    // ─── event: upload gambar ─────────────────────────────────────────────────
    const fileInput = document.getElementById('fileInputScan');
    const filePreview = document.getElementById('filePreview');
    if (fileInput) {
        fileInput.addEventListener('change', async function () {
            const f = this.files && this.files[0];
            if (!f) return;

            const url = URL.createObjectURL(f);
            filePreview.src = url;
            filePreview.style.display = 'block';
            scannerStatus.textContent = 'Membaca barcode dari gambar…';
            console.log('📸 Processing file:', f.name, f.size, 'bytes');

            const hints = new Map();
            hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, [ZXing.BarcodeFormat.CODE_128]);
            const reader = new ZXing.BrowserMultiFormatReader(hints);

            try {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.src = url;
                
                // Wait for image to load with timeout
                await Promise.race([
                    new Promise((resolve, reject) => {
                        img.onload = () => resolve();
                        img.onerror = () => reject(new Error('Failed to load image'));
                    }),
                    new Promise((_, reject) => setTimeout(() => reject(new Error('Image load timeout')), 5000))
                ]);
                
                console.log('✓ Image loaded:', img.naturalWidth, 'x', img.naturalHeight);

                let result = null;

                // Get EXIF orientation
                const orientation = await getExifOrientation(f);
                console.log('📷 EXIF Orientation:', orientation);

                // Coba decode langsung
                try { 
                    console.log('→ Attempt 1: Direct decode...');
                    result = await reader.decodeFromImageElement(img); 
                } catch (err) {
                    console.warn('Direct decode failed:', err.message);
                }

                // Jika gagal, coba dengan preprocessing perbaikan
                if (!result) {
                    console.log('→ Attempt 2: Decode with EXIF correction + preprocessing...');
                    result = await decodeWithExifAndPreprocess(reader, f, img, orientation);
                }

                // Fallback: coba dengan inverted preprocessing
                if (!result) {
                    console.log('→ Attempt 3: Decode with inverted contrast...');
                    result = await decodeWithInvertedPreprocess(reader, f, img, orientation);
                }

                // Fallback: coba dengan adaptive preprocessing
                if (!result) {
                    console.log('→ Attempt 4: Decode with adaptive preprocessing...');
                    result = await decodeWithAdaptivePreprocess(reader, f, img, orientation);
                }

                if (result) {
                    const barcode = result.getText();
                    console.log('✓ Barcode detected from file:', barcode);
                    stopScanner();
                    await handleScannedCode(barcode);
                } else {
                    console.warn('⚠ No barcode found in image');
                    scannerStatus.textContent = 'Gagal membaca barcode - coba foto lebih jelas atau gunakan pencahayaan lebih baik';
                }
            } catch (err) {
                console.error('❌ File processing error:', err);
                scannerStatus.textContent = 'Error: ' + (err.message || 'Gagal membaca gambar');
            }
        });
    }

    async function decodeWithExifAndPreprocess(reader, blob, img, orientation) {
        try {
            const canvas = document.createElement('canvas');
            const originalWidth = img.naturalWidth;
            const originalHeight = img.naturalHeight;
            
            // Determine canvas size based on orientation
            let width = originalWidth;
            let height = originalHeight;
            if ([5, 6, 7, 8].includes(orientation)) {
                [width, height] = [height, width];
            }
            
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            
            // Apply EXIF rotation
            applyExifRotation(ctx, canvas, orientation);
            ctx.drawImage(img, 0, 0, originalWidth, originalHeight);
            
            // Scale down if too large
            let processCanvas = canvas;
            if (canvas.width > 2000 || canvas.height > 2000) {
                const scale = Math.max(canvas.width, canvas.height) / 1920;
                processCanvas = document.createElement('canvas');
                processCanvas.width = Math.round(canvas.width / scale);
                processCanvas.height = Math.round(canvas.height / scale);
                const pCtx = processCanvas.getContext('2d');
                pCtx.drawImage(canvas, 0, 0, processCanvas.width, processCanvas.height);
            }
            
            // Grayscale + Contrast Enhancement
            const imageData = processCanvas.getContext('2d').getImageData(0, 0, processCanvas.width, processCanvas.height);
            const d = imageData.data;
            let sum = 0;
            const lums = [];
            
            for (let i = 0; i < d.length; i += 4) {
                const lum = 0.299 * d[i] + 0.587 * d[i+1] + 0.114 * d[i+2];
                lums.push(lum);
                sum += lum;
            }
            
            const avg = sum / lums.length;
            const variance = lums.reduce((s, l) => s + Math.pow(l - avg, 2), 0) / lums.length;
            const stdDev = Math.sqrt(variance);
            
            // Adaptive threshold with contrast stretching
            for (let i = 0; i < d.length; i += 4) {
                const lum = lums[i / 4];
                const normalized = (lum - avg) / (stdDev || 1) * 50 + 128;
                const clamped = Math.max(0, Math.min(255, normalized));
                const v = clamped > 128 ? 255 : 0;
                d[i] = d[i+1] = d[i+2] = v;
            }
            
            processCanvas.getContext('2d').putImageData(imageData, 0, 0);
            
            const pImg = new Image();
            pImg.src = processCanvas.toDataURL('image/png');
            pImg.crossOrigin = 'anonymous';
            
            await Promise.race([
                new Promise((resolve, reject) => {
                    pImg.onload = () => resolve();
                    pImg.onerror = () => reject(new Error('Processed image load failed'));
                }),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Image load timeout')), 3000))
            ]);

            return await reader.decodeFromImageElement(pImg);
        } catch (err) {
            console.warn('EXIF+Preprocess decode failed:', err.message);
            return null;
        }
    }

    async function decodeWithInvertedPreprocess(reader, blob, img, orientation) {
        try {
            const canvas = document.createElement('canvas');
            const originalWidth = img.naturalWidth;
            const originalHeight = img.naturalHeight;
            
            let width = originalWidth;
            let height = originalHeight;
            if ([5, 6, 7, 8].includes(orientation)) {
                [width, height] = [height, width];
            }
            
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            
            applyExifRotation(ctx, canvas, orientation);
            ctx.drawImage(img, 0, 0, originalWidth, originalHeight);
            
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const d = imageData.data;
            let sum = 0;
            
            for (let i = 0; i < d.length; i += 4) {
                const lum = 0.299 * d[i] + 0.587 * d[i+1] + 0.114 * d[i+2];
                d[i] = d[i+1] = d[i+2] = lum;
                sum += lum;
            }
            
            const avg = sum / (d.length / 4);
            // Inverted threshold
            for (let i = 0; i < d.length; i += 4) {
                const v = d[i] > avg ? 0 : 255;
                d[i] = d[i+1] = d[i+2] = v;
            }
            
            ctx.putImageData(imageData, 0, 0);
            
            const pImg = new Image();
            pImg.src = canvas.toDataURL('image/png');
            pImg.crossOrigin = 'anonymous';
            
            await Promise.race([
                new Promise((resolve, reject) => {
                    pImg.onload = () => resolve();
                    pImg.onerror = () => reject(new Error('Image load failed'));
                }),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Image load timeout')), 3000))
            ]);

            return await reader.decodeFromImageElement(pImg);
        } catch (err) {
            console.warn('Inverted preprocess decode failed:', err.message);
            return null;
        }
    }

    async function decodeWithAdaptivePreprocess(reader, blob, img, orientation) {
        try {
            const canvas = document.createElement('canvas');
            const originalWidth = img.naturalWidth;
            const originalHeight = img.naturalHeight;
            
            let width = originalWidth;
            let height = originalHeight;
            if ([5, 6, 7, 8].includes(orientation)) {
                [width, height] = [height, width];
            }
            
            canvas.width = Math.min(2000, width);
            canvas.height = Math.min(2000, height);
            const ctx = canvas.getContext('2d');
            
            applyExifRotation(ctx, canvas, orientation);
            ctx.drawImage(img, 0, 0, originalWidth, originalHeight);
            
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const d = imageData.data;
            
            // Simple Otsu threshold algorithm
            const histogram = new Array(256).fill(0);
            for (let i = 0; i < d.length; i += 4) {
                const lum = 0.299 * d[i] + 0.587 * d[i+1] + 0.114 * d[i+2];
                histogram[Math.floor(lum)]++;
            }
            
            let maxVariance = 0;
            let threshold = 0;
            const total = d.length / 4;
            
            let sumB = 0;
            let wB = 0;
            let sum = 0;
            for (let i = 0; i < 256; i++) {
                sum += i * histogram[i];
            }
            
            for (let i = 0; i < 256; i++) {
                wB += histogram[i];
                if (wB === 0) continue;
                
                const wF = total - wB;
                if (wF === 0) break;
                
                sumB += i * histogram[i];
                const mB = sumB / wB;
                const mF = (sum - sumB) / wF;
                const variance = wB * wF * Math.pow(mB - mF, 2);
                
                if (variance > maxVariance) {
                    maxVariance = variance;
                    threshold = i;
                }
            }
            
            for (let i = 0; i < d.length; i += 4) {
                const lum = 0.299 * d[i] + 0.587 * d[i+1] + 0.114 * d[i+2];
                const v = lum > threshold ? 255 : 0;
                d[i] = d[i+1] = d[i+2] = v;
            }
            
            ctx.putImageData(imageData, 0, 0);
            
            const pImg = new Image();
            pImg.src = canvas.toDataURL('image/png');
            pImg.crossOrigin = 'anonymous';
            
            await Promise.race([
                new Promise((resolve, reject) => {
                    pImg.onload = () => resolve();
                    pImg.onerror = () => reject(new Error('Image load failed'));
                }),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Image load timeout')), 3000))
            ]);

            return await reader.decodeFromImageElement(pImg);
        } catch (err) {
            console.warn('Adaptive preprocess decode failed:', err.message);
            return null;
        }
    }

    pickupBtn.addEventListener('click', async function () {
        try {
            const pos = await getAccuratePosition(50, 20000);
            document.getElementById('toko_lat').value = pos.coords.latitude;
            document.getElementById('toko_lng').value = pos.coords.longitude;
            document.getElementById('toko_acc').value = pos.coords.accuracy;
        } catch (error) {
            alert('Gagal ambil lokasi: ' + error.message);
        }
    });

    ambilLokasiBtn.addEventListener('click', async function () {
        try {
            const pos = await getAccuratePosition(50, 20000);
            await evaluateVisit(pos);
        } catch (error) {
            alert('Gagal ambil lokasi: ' + error.message);
        }
    });

    // camera select change handler
    const cameraSelect = document.getElementById('cameraSelect');
    if (cameraSelect) {
        cameraSelect.addEventListener('change', async function () {
            lastDeviceId = this.value;
            scannerResultText.textContent = '-';
            setStatus('-');
            await startScanner();
        });
    }

    // stop/reset buttons
    const btnStopScanner = document.getElementById('btnStopScanner');
    const btnResetScanner = document.getElementById('btnResetScanner');
    if (btnStopScanner) btnStopScanner.addEventListener('click', stopScanner);
    if (btnResetScanner) btnResetScanner.addEventListener('click', async function () {
        scannerResultText.textContent = '-';
        setStatus('-');
        await startScanner();
    });

    openScannerBtn.addEventListener('click', openScanner);

    scannerModalEl.addEventListener('shown.bs.modal', async function () {
        scannerResultText.textContent = '-';
        setStatus('Memulai kamera…');
        setTimeout(startScanner, 150);
    });
    scannerModalEl.addEventListener('hidden.bs.modal', function () {
        stopScanner();
        setStatus('Siap');
    });
    
    console.log('✓ Event listeners attached');
    console.log('═══════════════════════════════════════════');
});
</script>
@endpush
