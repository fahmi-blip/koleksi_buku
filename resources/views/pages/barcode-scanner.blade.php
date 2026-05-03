@extends('layouts.main')

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
    const video = document.getElementById('video');
    const beepAudio = document.getElementById('beepAudio');
    let codeReader = null;
    let selectedDeviceId = null;

    function playBeep() { try { beepAudio.currentTime = 0; beepAudio.play(); } catch(e){} }

    async function startScanner() {
        if (codeReader) return;
        // trigger permission prompt explicitly
        try {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                const tempStream = await navigator.mediaDevices.getUserMedia({ video: true });
                tempStream.getTracks().forEach(t => t.stop());
            }
        } catch (permErr) {
            console.warn('Camera permission denied or not available', permErr);
            alert('Tidak dapat mengakses kamera. Periksa izin browser.');
            return;
        }

        codeReader = new ZXing.BrowserMultiFormatReader();
        try {
            const devices = await codeReader.listVideoInputDevices();
            const select = document.getElementById('cameraSelect');
            if (select) {
                select.innerHTML = '';
                devices.forEach(d => { const opt = document.createElement('option'); opt.value = d.deviceId; opt.text = d.label || d.deviceId; select.appendChild(opt); });
                selectedDeviceId = select.value || devices[0]?.deviceId;
                select.onchange = function () { selectedDeviceId = select.value; stopScanner(); startScanner(); };
            } else {
                selectedDeviceId = devices[0]?.deviceId;
            }

            codeReader.decodeFromVideoDevice(selectedDeviceId, video, (result, err) => {
                if (result) {
                    stopScanner();
                    handleScanned(result.getText());
                }
            });
        } catch(e) { console.error(e); alert('Error saat memulai scanner: ' + (e.message || e)); }
    }

    function stopScanner() {
        if (codeReader) { try { codeReader.reset(); } catch(e){} codeReader = null; }
        try { if (video && video.srcObject) { video.srcObject.getTracks().forEach(t=>t.stop()); video.srcObject = null; } } catch(e){}
    }

    async function handleScanned(code) {
        playBeep();
        document.getElementById('scannedId').textContent = code;
        document.getElementById('scannedNama').textContent = 'Mencari...';
        document.getElementById('scannedHarga').textContent = '';
        try {
            const res = await fetch('{{ route('barcode-scanner.lookup') }}', {
                method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
        } catch(e) {
            console.error(e);
            document.getElementById('scannedNama').textContent = 'Terjadi kesalahan';
        }
    }

    document.getElementById('btnStop').addEventListener('click', stopScanner);
    document.getElementById('btnReset').addEventListener('click', function(){ document.getElementById('scannedId').textContent='-'; document.getElementById('scannedNama').textContent='-'; document.getElementById('scannedHarga').textContent='-'; startScanner(); });

    startScanner();

    // image upload fallback
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    fileInput.addEventListener('change', async function (e) {
        const f = e.target.files && e.target.files[0];
        if (!f) return;
        const url = URL.createObjectURL(f);
        filePreview.src = url; filePreview.style.display = 'block';
        try {
            const img = new Image(); img.src = url; await img.decode();
            const reader = new ZXing.BrowserMultiFormatReader();
            let result = null;
            try { result = await reader.decodeFromImageElement(img); } catch(e) { result = null; }

            if (!result) {
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
                    const r = data[i], g = data[i+1], b = data[i+2];
                    const lum = 0.299*r + 0.587*g + 0.114*b;
                    data[i]=data[i+1]=data[i+2]=lum; sum += lum;
                }
                const avg = sum / (data.length/4 || 1);
                for (let i = 0; i < data.length; i += 4) {
                    const v = data[i]; const v2 = v > avg ? 255 : 0; data[i]=data[i+1]=data[i+2]=v2;
                }
                ctx.putImageData(imageData, 0, 0);
                const dataUrl = off.toDataURL('image/png');
                const pImg = new Image(); pImg.src = dataUrl; await pImg.decode();
                try { result = await reader.decodeFromImageElement(pImg); } catch(e) { result = null; }
            }

            if (result) { handleScanned(result.getText()); } else { alert('Gagal membaca barcode dari gambar'); }
        } catch (err) {
            console.error('image decode error', err);
            alert('Gagal membaca barcode dari gambar');
        }
    });
});
</script>
@endpush
@endsection
