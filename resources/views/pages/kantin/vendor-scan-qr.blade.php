@extends('layouts.kantin')

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start;">
    <!-- Kolom Scanning -->
    <section style="border:1px solid rgba(148,163,184,0.2);background:rgba(15,23,42,0.72);border-radius:24px;padding:22px;">
        <div style="color:#38bdf8;text-transform:uppercase;letter-spacing:.12em;font-size:.78rem;">Pemindai QR Code</div>
        <h2 style="margin:10px 0 8px;">Scan QR Code Customer</h2>
        <p style="margin:0 0 18px;color:#94a3b8;">Arahkan kamera ke QR code pesanan customer untuk melihat detail menu yang dipesan.</p>

        <!-- Area Kamera -->
        <div id="videoContainer" style="position:relative;width:100%;height:480px;border-radius:12px;overflow:hidden;background:#000;margin-bottom:16px;border:2px solid rgba(56,189,248,.2);">
            <!-- html5-qrcode akan membuat video di sini -->
        </div>

        <!-- Status -->
        <div style="margin-bottom:16px;padding:12px;border-radius:12px;background:rgba(56,189,248,.08);border:1px solid rgba(56,189,248,.2);">
            <div style="font-size:.82rem;color:#94a3b8;">Status: <span id="scannerStatus" style="color:#38bdf8;font-weight:700;">Siap scan</span></div>
        </div>

        <!-- Kontrol Kamera -->
        <div style="margin-bottom:16px;">
            <label style="display:block;color:#94a3b8;font-size:.82rem;margin-bottom:6px;">Pilih Kamera</label>
            <select id="cameraSelect" style="width:100%;padding:10px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(30,41,59,.5);color:white;font-size:.95rem;"></select>
        </div>

        <!-- Input Manual -->
        <div style="display:grid;grid-template-columns:1fr auto;gap:8px;">
            <input id="manualIdPesanan" type="text" placeholder="atau masukkan ID Pesanan" 
                   style="padding:10px;border-radius:8px;border:1px solid rgba(148,163,184,.2);background:rgba(30,41,59,.5);color:white;font-size:.95rem;" />
            <button id="btnManualLookup" style="padding:10px 16px;border-radius:8px;background:#38bdf8;color:#000;border:none;font-weight:700;cursor:pointer;font-size:.95rem;">Cari</button>
        </div>

        <!-- Tombol Kontrol -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:16px;">
            <button id="btnReset" style="padding:10px 16px;border-radius:8px;background:rgba(56,189,248,.2);border:1px solid rgba(56,189,248,.3);color:#38bdf8;font-weight:700;cursor:pointer;font-size:.95rem;">Scan Ulang</button>
            <button id="btnClear" style="padding:10px 16px;border-radius:8px;background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.3);color:#ef4444;font-weight:700;cursor:pointer;font-size:.95rem;">Hapus Hasil</button>
        </div>
    </section>

    <!-- Kolom Hasil Scan -->
    <section style="border:1px solid rgba(148,163,184,0.2);background:rgba(15,23,42,0.72);border-radius:24px;padding:22px;">
        <div style="color:#38bdf8;text-transform:uppercase;letter-spacing:.12em;font-size:.78rem;">Hasil Scan</div>
        <h2 style="margin:10px 0 8px;">Detail Pesanan</h2>

        <div id="noResultBox" style="padding:24px;border-radius:16px;border:2px dashed rgba(148,163,184,.3);text-align:center;color:#94a3b8;">
            <p style="margin:0;font-size:.95rem;">Belum ada pesanan yang dipindai</p>
        </div>

        <div id="resultBox" style="display:none;">
            <!-- Info Pesanan -->
            <div style="padding:16px;border-radius:12px;background:rgba(255,255,255,.04);border:1px solid rgba(148,163,184,.12);margin-bottom:16px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                    <div>
                        <div style="color:#94a3b8;font-size:.82rem;">ID Pesanan</div>
                        <div style="font-weight:700;margin-top:4px;color:#38bdf8;"><span id="resultIdPesanan">-</span></div>
                    </div>
                    <div>
                        <div style="color:#94a3b8;font-size:.82rem;">Kode Pesanan</div>
                        <div style="font-weight:700;margin-top:4px;"><span id="resultOrderCode">-</span></div>
                    </div>
                </div>
                <div style="padding:10px;border-radius:8px;background:rgba(0,0,0,.3);margin-bottom:12px;">
                    <div style="color:#94a3b8;font-size:.82rem;">Customer</div>
                    <div style="font-weight:700;margin-top:4px;"><span id="resultCustomerName">-</span></div>
                </div>
                <div style="padding:10px;border-radius:8px;background:rgba(0,0,0,.3);">
                    <div style="color:#94a3b8;font-size:.82rem;">Vendor</div>
                    <div style="font-weight:700;margin-top:4px;"><span id="resultVendorName">-</span></div>
                </div>
            </div>

            <!-- Status Pembayaran -->
            <div id="statusPaymentBox" style="padding:14px;border-radius:12px;background:rgba(255,255,255,.04);border:1px solid rgba(148,163,184,.12);margin-bottom:16px;text-align:center;">
                <div style="color:#94a3b8;font-size:.82rem;margin-bottom:6px;">Status Pembayaran</div>
                <div id="resultStatusBayar" style="font-size:1.3rem;font-weight:700;color:#22c55e;">Lunas</div>
            </div>

            <!-- Menu yang Dipesan -->
            <div style="margin-bottom:16px;">
                <div style="color:#94a3b8;font-size:.82rem;margin-bottom:10px;font-weight:700;">Menu yang Dipesan:</div>
                <div id="resultMenus" style="display:grid;gap:8px;">
                    <!-- Items akan diisi via JavaScript -->
                </div>
            </div>

            <!-- Total -->
            <div style="padding:14px;border-radius:12px;background:linear-gradient(135deg,rgba(34,197,94,.14),rgba(56,189,248,.12));border:1px solid rgba(34,197,94,.25);text-align:center;">
                <div style="color:#94a3b8;font-size:.82rem;">Total Pesanan</div>
                <div id="resultTotal" style="font-size:1.5rem;font-weight:700;color:#22c55e;margin-top:6px;">Rp 0</div>
            </div>
        </div>
    </section>
</div>

<!-- Beep Audio -->
<audio id="beepAudio" preload="auto">
    <source src="/assets/sounds/u_edtmwfwu7c-beep-329314.mp3" type="audio/mpeg">
</audio>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── Elemen DOM ─────────────────────────────────────────────────────────
    const videoContainer = document.getElementById('videoContainer');
    const beepAudio = document.getElementById('beepAudio');
    const cameraSelect = document.getElementById('cameraSelect');
    const statusEl = document.getElementById('scannerStatus');
    const manualInput = document.getElementById('manualIdPesanan');
    const btnManualLookup = document.getElementById('btnManualLookup');
    const btnReset = document.getElementById('btnReset');
    const btnClear = document.getElementById('btnClear');
    const noResultBox = document.getElementById('noResultBox');
    const resultBox = document.getElementById('resultBox');

    // ── State ──────────────────────────────────────────────────────────────
    let html5Scanner = null;
    let isRunning = false;
    let scanLocked = false;

    // ── Helpers ────────────────────────────────────────────────────────────

    function setStatus(msg) {
        if (statusEl) statusEl.textContent = msg;
        console.log('[QRScanner]', msg);
    }

    function playBeep() {
        try {
            if (beepAudio) {
                beepAudio.currentTime = 0;
                beepAudio.play().catch(() => playWebAudioBeep());
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
            g.gain.value = 0.05;
            o.connect(g);
            g.connect(ctx.destination);
            o.start();
            setTimeout(() => { try { o.stop(); ctx.close(); } catch (_) {} }, 140);
        } catch (e) {}
    }

    function showResult(data) {
        noResultBox.style.display = 'none';
        resultBox.style.display = 'block';

        document.getElementById('resultIdPesanan').textContent = data.idpesanan;
        document.getElementById('resultOrderCode').textContent = data.order_code;
        document.getElementById('resultCustomerName').textContent = data.customer_name;
        document.getElementById('resultVendorName').textContent = data.vendor_name;
        document.getElementById('resultStatusBayar').textContent = data.status_bayar;
        document.getElementById('resultStatusBayar').style.color = data.status_bayar_color;
        document.getElementById('resultTotal').textContent = 'Rp ' + Number(data.total).toLocaleString('id-ID');

        const menuHtml = data.items.map(item =>
            `<div style="padding:10px;border-radius:8px;background:rgba(0,0,0,.3);border-left:3px solid #38bdf8;">
                <strong>${item.nama_menu}</strong>
                <div style="color:#94a3b8;font-size:.85rem;margin-top:4px;">
                    ${item.jumlah}x @ Rp ${Number(item.harga).toLocaleString('id-ID')} = <strong>Rp ${Number(item.subtotal).toLocaleString('id-ID')}</strong>
                </div>
                ${item.catatan && item.catatan !== '-' ? `<div style="color:#cbd5e1;font-size:.85rem;margin-top:4px;font-style:italic;">Catatan: ${item.catatan}</div>` : ''}
            </div>`
        ).join('');
        document.getElementById('resultMenus').innerHTML = menuHtml;
    }

    function clearResult() {
        noResultBox.style.display = 'block';
        resultBox.style.display = 'none';
        manualInput.value = '';
    }

    // ── Stop Scanner ───────────────────────────────────────────────────────

    async function stopScanner() {
        isRunning = false;
        scanLocked = false;

        if (html5Scanner) {
            try {
                const state = html5Scanner.getState();
                if (state === 2 || state === 3) {
                    await html5Scanner.stop();
                }
            } catch (e) {
                console.warn('html5Scanner.stop():', e);
            }
            try { await html5Scanner.clear(); } catch (e) {}
            html5Scanner = null;
        }

        try {
            const old = document.getElementById('html5qr-reader');
            if (old && old.parentNode) old.parentNode.removeChild(old);
        } catch (e) {}
    }

    // ── Populate Cameras ───────────────────────────────────────────────────

    async function populateCameras() {
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const cams = devices.filter(d => d.kind === 'videoinput');

            cameraSelect.innerHTML = '';

            if (!cams.length) {
                cameraSelect.innerHTML = '<option value="">Tidak ada kamera terdeteksi</option>';
                return null;
            }

            cams.forEach((cam, i) => {
                const opt = document.createElement('option');
                opt.value = cam.deviceId;
                opt.textContent = cam.label || ('Kamera ' + (i + 1));
                cameraSelect.appendChild(opt);
            });

            cameraSelect.value = cams[0].deviceId;
            return cams[0].deviceId;
        } catch (e) {
            console.error('enumerateDevices:', e);
            return null;
        }
    }

    // ── Start Scanner ──────────────────────────────────────────────────────

    async function startScanner() {
        await stopScanner();
        scanLocked = false;
        setStatus('Meminta izin kamera…');

        // Populate kamera terlebih dahulu
        const selectedId = await populateCameras();
        if (!selectedId) {
            setStatus('Tidak ada kamera terdeteksi');
            return;
        }

        // Buat container untuk html5-qrcode
        let readerId = 'html5qr-reader';
        let readerDiv = document.getElementById(readerId);
        if (readerDiv) {
            readerDiv.remove();
        }
        
        readerDiv = document.createElement('div');
        readerDiv.id = readerId;
        readerDiv.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;border-radius:12px;overflow:hidden;';
        videoContainer.innerHTML = ''; // bersihkan container
        videoContainer.appendChild(readerDiv);

        setStatus('Memulai scanner…');

        html5Scanner = new Html5Qrcode(readerId, {
            verbose: false,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        });

        function calcQrbox() {
            const w = Math.min(900, Math.max(400, Math.floor(window.innerWidth * 0.5)));
            return { width: w, height: Math.round(w * 0.75) };
        }

        const config = {
            fps: 15,
            qrbox: calcQrbox(),
            aspectRatio: 1.5,
            formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
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
                async function (decodedText, decodedResult) {
                    if (scanLocked) return;
                    scanLocked = true;

                    console.log('[QRScanner] QR terbaca:', decodedText);
                    setStatus('QR code terbaca: ' + decodedText);

                    playBeep();
                    await stopScanner();
                    await handleScannedQR(decodedText);
                },
                function (errorMsg) {}
            );

            isRunning = true;
            setStatus('Mendeteksi QR code — arahkan ke kamera…');

        } catch (err) {
            console.error('Html5Qrcode.start() gagal:', err);
            setStatus('Error memulai kamera: ' + err.message);
        }
    }

    // ── Handle Scanned QR ──────────────────────────────────────────────────

    async function handleScannedQR(code) {
        try {
            const res = await fetch('{{ route('kantin.qrcode.lookup') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ idpesanan: parseInt(code, 10) }),
            });

            const json = await res.json();

            if (json.success) {
                showResult(json.data);
                setStatus('✓ Pesanan ditemukan');
            } else {
                setStatus('⚠ Pesanan tidak ditemukan');
            }
        } catch (e) {
            console.error('Fetch error:', e);
            setStatus('Error jaringan');
        }
    }

    // ── Events ─────────────────────────────────────────────────────────────

    btnManualLookup.addEventListener('click', async function () {
        const id = (manualInput.value || '').trim();
        if (!id) { alert('Masukkan ID Pesanan terlebih dahulu'); return; }
        playBeep();
        await stopScanner();
        await handleScannedQR(id);
    });

    manualInput.addEventListener('keydown', async function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const id = this.value.trim();
        if (!id) return;
        playBeep();
        await stopScanner();
        await handleScannedQR(id);
    });

    btnReset.addEventListener('click', async function () {
        clearResult();
        await startScanner();
    });

    btnClear.addEventListener('click', function () {
        clearResult();
    });

    cameraSelect.addEventListener('change', async function () {
        clearResult();
        await startScanner();
    });

    // ── Initial start ──────────────────────────────────────────────────────

    startScanner();
});
</script>

@endsection
