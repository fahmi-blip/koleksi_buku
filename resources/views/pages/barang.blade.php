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
                        <div class="d-flex gap-2 align-items-center">
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
                            <video id="video" width="100%" autoplay muted playsinline style="border-radius:8px;background:#000"></video>
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
                            <label class="form-label small">Masukkan barcode secara manual (fallback)</label>
                            <div class="input-group">
                                <input id="manualBarcode" type="text" class="form-control form-control-sm" placeholder="Masukkan kode jika scanner gagal" />
                                <button id="btnManualSubmit" class="btn btn-sm btn-primary">Gunakan</button>
                            </div>
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
    <source src="data:audio/wav;base64,UklGRkQAAABXQVZFZm10IBAAAAABAAEAQB8AAAB9AAACABAAZGF0YQAAAAA=" type="audio/wav">
</audio>

@push('scripts')
<script src="https://unpkg.com/@zxing/library@0.18.6/umd/index.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnScanBarcode = document.getElementById('btnScanBarcode');
    const scannerModal = new bootstrap.Modal(document.getElementById('scannerModal'));
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const beepAudio = document.getElementById('beepAudio');

    let codeReader = null;
    let selectedDeviceId = null;

    btnScanBarcode.addEventListener('click', async function () {
        // user gesture: show modal and unlock audio playback
        scannerModal.show();
        try { const p = beepAudio.play(); if (p && p.then) p.then(()=>beepAudio.pause()).catch(()=>{}); } catch(e){}
    });

    // populate camera list and start scanner when modal fully shown
    document.getElementById('scannerModal').addEventListener('shown.bs.modal', async function () {
        await populateCameraList();
        // small delay to ensure video element is visible
        setTimeout(() => startScanner(), 200);
    });

    document.getElementById('btnCloseScanner').addEventListener('click', function () {
        stopScanner();
    });
    document.getElementById('btnResetScanner').addEventListener('click', function () {
        // reset displayed values and continue scanning
        document.getElementById('scannedId').textContent = '-';
        document.getElementById('scannedNama').textContent = '-';
        document.getElementById('scannedHarga').textContent = '-';
        startScanner();
    });

    function playBeep() {
        try { beepAudio.currentTime = 0; beepAudio.play(); }
        catch(e) { console.log('beep', e); }
    }

    async function startScanner() {
        if (codeReader) {
            return;
        }
        // trigger permission prompt explicitly (helps Chrome show camera permission)
        try {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                const tempStream = await navigator.mediaDevices.getUserMedia({ video: true });
                // stop temporary tracks after permission granted
                tempStream.getTracks().forEach(t => t.stop());
            }
        } catch (permErr) {
            console.warn('Camera permission denied or not available', permErr);
            return;
        }

        document.getElementById('scannerStatus').textContent = 'Mencari perangkat kamera...';
        codeReader = new ZXing.BrowserMultiFormatReader();
        try {
            const devices = await codeReader.listVideoInputDevices();
            await populateCameraList(devices);
            const select = document.getElementById('cameraSelect');
            selectedDeviceId = select && select.value ? select.value : (devices && devices[0]?.deviceId);

            document.getElementById('scannerStatus').textContent = 'Memulai kamera...';

            codeReader.decodeFromVideoDevice(selectedDeviceId, video, (result, err) => {
                if (result) {
                    // stop scanning
                    stopScanner();
                    const code = result.getText();
                    handleScannedCode(code);
                }
            });
            document.getElementById('scannerStatus').textContent = 'Mendeteksi...';
        } catch (e) {
            console.error('Scanner error', e);
            document.getElementById('scannerStatus').textContent = 'Error: ' + (e.message || e.name || 'tidak dapat mengakses kamera');
        }
    }

    async function populateCameraList(devices) {
        try {
            const select = document.getElementById('cameraSelect');
            select.innerHTML = '';
            let list = devices;
            if (!list) {
                const tempReader = new ZXing.BrowserMultiFormatReader();
                list = await tempReader.listVideoInputDevices();
            }
            if (!list || list.length === 0) {
                const opt = document.createElement('option'); opt.value = ''; opt.text = 'Tidak ada kamera terdeteksi'; select.appendChild(opt); return;
            }
            list.forEach(d => {
                const opt = document.createElement('option'); opt.value = d.deviceId; opt.text = d.label || d.deviceId; select.appendChild(opt);
            });
            // select first
            select.value = selectedDeviceId || list[0].deviceId;

            select.onchange = function () {
                selectedDeviceId = select.value;
                stopScanner();
                startScanner();
            };
        } catch (e) {
            console.warn('populateCameraList', e);
        }
    }

    function stopScanner() {
        if (codeReader) {
            try { codeReader.reset(); } catch(e) {}
            codeReader = null;
        }
        try { if (video && video.srcObject) {
            video.srcObject.getTracks().forEach(t=>t.stop());
            video.srcObject = null;
        }} catch(e){}
    }

    async function handleScannedCode(code) {
        // play beep
        playBeep();

        // show loading state
        document.getElementById('scannedId').textContent = code;
        document.getElementById('scannedNama').textContent = 'Mencari...';
        document.getElementById('scannedHarga').textContent = '';

        try {
            const res = await fetch('{{ route('barcode-scanner.lookup') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ barcode: code })
            });
            const json = await res.json();
            if (json.success) {
                document.getElementById('scannedId').textContent = json.data.id_barang;
                document.getElementById('scannedNama').textContent = json.data.nama;
                document.getElementById('scannedHarga').textContent = 'Rp ' + json.data.harga.toLocaleString('id-ID');
            } else {
                document.getElementById('scannedNama').textContent = 'Barang tidak ditemukan';
            }
        } catch (e) {
            console.error(e);
            document.getElementById('scannedNama').textContent = 'Terjadi kesalahan';
            document.getElementById('scannerStatus').textContent = 'Error saat lookup';
        }
    }

    // Ensure scanner stops when modal hidden
    document.getElementById('scannerModal').addEventListener('hidden.bs.modal', function () {
        stopScanner();
    });

    // manual submit fallback
    document.getElementById('btnManualSubmit').addEventListener('click', function (e) {
        e.preventDefault();
        const code = document.getElementById('manualBarcode').value.trim();
        if (!code) return alert('Masukkan kode barcode');
        stopScanner();
        handleScannedCode(code);
    });

    // image upload fallback
    const fileInput = document.getElementById('fileInputScan');
    const filePreview = document.getElementById('filePreview');
    fileInput.addEventListener('change', async function (e) {
        const f = e.target.files && e.target.files[0];
        if (!f) return;
        const url = URL.createObjectURL(f);
        filePreview.src = url; filePreview.style.display = 'block';
        document.getElementById('scannerStatus').textContent = 'Mencoba decode dari gambar...';
        try {
            const img = new Image(); img.src = url; await img.decode();
            const reader = new ZXing.BrowserMultiFormatReader();
            let result = null;
            try {
                result = await reader.decodeFromImageElement(img);
            } catch (e) {
                result = null;
            }

            if (!result) {
                // preprocess: resize, grayscale, threshold
                const off = document.createElement('canvas');
                const maxW = 1200;
                const scale = Math.min(1, maxW / img.naturalWidth);
                off.width = Math.max(100, Math.round(img.naturalWidth * scale));
                off.height = Math.max(100, Math.round(img.naturalHeight * scale));
                const ctx = off.getContext('2d');
                ctx.drawImage(img, 0, 0, off.width, off.height);
                const imageData = ctx.getImageData(0, 0, off.width, off.height);
                const data = imageData.data;
                let sum = 0;
                for (let i = 0; i < data.length; i += 4) {
                    const r = data[i], g = data[i + 1], b = data[i + 2];
                    const lum = 0.299 * r + 0.587 * g + 0.114 * b;
                    data[i] = data[i + 1] = data[i + 2] = lum;
                    sum += lum;
                }
                const avg = sum / (data.length / 4 || 1);
                for (let i = 0; i < data.length; i += 4) {
                    const v = data[i];
                    const v2 = v > avg ? 255 : 0;
                    data[i] = data[i + 1] = data[i + 2] = v2;
                }
                ctx.putImageData(imageData, 0, 0);
                const dataUrl = off.toDataURL('image/png');
                const pImg = new Image(); pImg.src = dataUrl; await pImg.decode();
                try {
                    result = await reader.decodeFromImageElement(pImg);
                } catch (e) {
                    result = null;
                }
            }

            if (result) {
                handleScannedCode(result.getText());
            } else {
                document.getElementById('scannerStatus').textContent = 'Gagal membaca dari gambar';
            }
        } catch (err) {
            console.error('image decode error', err);
            document.getElementById('scannerStatus').textContent = 'Gagal membaca dari gambar';
        }
    });
});
</script>
@endpush
@endsection
