@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">QR Scanner Pesanan</h3>
    <a href="{{ route('home') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-lg-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Scan Kamera</h4>
                <p class="card-description">Arahkan kamera ke QR code yang berisi ID pesanan.</p>

                <div id="reader" style="width:100%;max-width:520px;margin:0 auto;"></div>

                <div class="mt-4">
                    <label class="form-label fw-bold">Atau masukkan payload secara manual</label>
                    <div class="input-group">
                        <input type="text" id="manualPayload" class="form-control" placeholder="Contoh: 12">
                        <button type="button" id="btnLookup" class="btn btn-primary">Cek QR</button>
                    </div>
                    <small class="text-muted">QR code saat ini berisi angka <strong>idpesanan</strong>.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Hasil Scan</h4>

                <div id="scanState" class="alert alert-secondary mb-3">
                    Menunggu scan QR code...
                </div>

                <div id="scanResult" style="display:none;">
                    <div class="mb-3 p-3 border rounded">
                        <div class="text-muted small">ID Pesanan</div>
                        <div class="fw-bold fs-4" id="resultId"></div>
                    </div>

                    <div class="mb-3 p-3 border rounded">
                        <div class="text-muted small">Order Code</div>
                        <div id="resultOrderCode"></div>
                    </div>

                    <div class="mb-3 p-3 border rounded">
                        <div class="text-muted small">Customer / Vendor</div>
                        <div id="resultCustomer"></div>
                        <div id="resultVendor" class="text-muted small"></div>
                    </div>

                    <div class="mb-3 p-3 border rounded">
                        <div class="text-muted small">Status</div>
                        <div id="resultStatus" class="fw-bold"></div>
                        <div id="resultPaidAt" class="text-muted small"></div>
                    </div>

                    <div class="mb-3 p-3 border rounded">
                        <div class="text-muted small">Total</div>
                        <div id="resultTotal" class="fw-bold text-success"></div>
                    </div>

                    <div class="p-3 border rounded">
                        <div class="text-muted small mb-2">Detail Item</div>
                        <div id="resultItems"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
(function () {
    const scanState = document.getElementById('scanState');
    const scanResult = document.getElementById('scanResult');
    const manualPayload = document.getElementById('manualPayload');
    const btnLookup = document.getElementById('btnLookup');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let lastPayload = null;
    let html5QrCode = null;

    function setState(message, type = 'secondary') {
        scanState.className = 'alert alert-' + type + ' mb-3';
        scanState.textContent = message;
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0,
        }).format(value);
    }

    function renderResult(data) {
        document.getElementById('resultId').textContent = data.idpesanan;
        document.getElementById('resultOrderCode').textContent = data.order_code;
        document.getElementById('resultCustomer').textContent = data.nama;
        document.getElementById('resultVendor').textContent = data.vendor ? 'Vendor: ' + data.vendor : '';
        document.getElementById('resultStatus').textContent = data.status_bayar === 1 ? 'Lunas' : 'Belum Lunas';
        document.getElementById('resultStatus').style.color = data.status_bayar === 1 ? '#16a34a' : '#f59e0b';
        document.getElementById('resultPaidAt').textContent = data.paid_at ? 'Dibayar pada ' + data.paid_at : 'Belum ada waktu pembayaran';
        document.getElementById('resultTotal').textContent = formatCurrency(data.total);

        const itemsEl = document.getElementById('resultItems');
        itemsEl.innerHTML = data.items.map(function (item) {
            return '<div class="d-flex justify-content-between border-bottom py-2"><span>' + item.nama_menu + ' x ' + item.jumlah + '</span><strong>' + formatCurrency(item.subtotal) + '</strong></div>';
        }).join('');

        scanResult.style.display = 'block';
    }

    function lookupPayload(payload) {
        const trimmed = String(payload || '').trim();

        if (!trimmed) {
            setState('Payload kosong.', 'danger');
            return;
        }

        if (trimmed === lastPayload) {
            return;
        }

        lastPayload = trimmed;
        setState('Membaca QR code: ' + trimmed + ' ...', 'info');

        fetch('{{ route('qr-scanner.lookup') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ payload: trimmed }),
        })
        .then(function (response) {
            return response.json().then(function (data) {
                if (!response.ok) {
                    throw data;
                }
                return data;
            });
        })
        .then(function (response) {
            setState(response.message, 'success');
            renderResult(response.data);
        })
        .catch(function (error) {
            setState(error.message || 'QR code tidak dapat dibaca.', 'danger');
        });
    }

    btnLookup.addEventListener('click', function () {
        lookupPayload(manualPayload.value);
    });

    manualPayload.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            lookupPayload(manualPayload.value);
        }
    });

    html5QrCode = new Html5Qrcode('reader');
    Html5Qrcode.getCameras().then(function (devices) {
        if (!devices || !devices.length) {
            setState('Kamera tidak ditemukan. Gunakan input manual.', 'warning');
            return;
        }

        const cameraId = devices[0].id;

        html5QrCode.start(
            cameraId,
            {
                fps: 10,
                qrbox: { width: 240, height: 240 },
            },
            function (decodedText) {
                lookupPayload(decodedText);
            },
            function () {}
        ).catch(function (error) {
            setState('Gagal membuka kamera: ' + error, 'danger');
        });
    }).catch(function (error) {
        setState('Tidak bisa mengakses kamera: ' + error, 'danger');
    });
})();
</script>
@endsection