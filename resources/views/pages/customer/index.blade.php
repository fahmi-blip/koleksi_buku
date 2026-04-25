@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Data Customer</h3>
    <div class="btn-group gap-2" role="group" aria-label="Mode tambah customer">
        <a href="{{ route('customer.index', ['mode' => 'blob']) }}" class="btn btn-sm {{ $mode === 'blob' ? 'btn-info' : 'btn-outline-info' }}">Tambah Customer 1</a>
        <a href="{{ route('customer.index', ['mode' => 'file']) }}" class="btn btn-sm {{ $mode === 'file' ? 'btn-info' : 'btn-outline-info' }}">Tambah Customer 2</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Tambah Customer</h4>
                <p class="card-description mb-4">
                    @if($mode === 'blob')
                        Mode aktif: <strong>Tambah Customer 1</strong> (foto disimpan sebagai blob di database).
                    @else
                        Mode aktif: <strong>Tambah Customer 2</strong> (foto disimpan sebagai file, path disimpan di database).
                    @endif
                </p>

                <form method="POST" action="{{ $mode === 'blob' ? route('customer.storeBlob') : route('customer.storeFile') }}" id="customerForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}" required>
                            </div>

                            <div class="form-group">
                                <label>Provinsi</label>
                                <select id="provinsi" name="provinsi" class="form-control" required>
                                    <option value="">Memuat data provinsi...</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Kabupaten / Kota</label>
                                <select id="kota" name="kota" class="form-control" disabled required>
                                    <option value="">Pilih Kabupaten/Kota</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Kecamatan</label>
                                <select id="kecamatan" name="kecamatan" class="form-control" disabled required>
                                    <option value="">Pilih Kecamatan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Kodepos - Kelurahan</label>
                                <select id="kodepos_kelurahan" name="kodepos_kelurahan" class="form-control" disabled required>
                                    <option value="">Pilih Kelurahan</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <input type="hidden" name="photo_data" id="photo_data" required>

                            <div class="mb-3 d-flex align-items-end gap-3">
                                <div style="width:180px;height:180px;border:1px solid #98c47f;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                    <img id="previewImage" src="" alt="Foto" style="width:100%;height:100%;object-fit:cover;display:none;">
                                    <span id="previewPlaceholder">Foto</span>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2"> 
                            <button type="button" id="btnOpenCamera" class="btn btn-primary">Ambil Foto</button>
                                <button type="submit" class="btn btn-success">
                                    {{ $mode === 'blob' ? 'Simpan Data (Blob)' : 'Simpan Data (File Path)' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tabel Customer</h4>
                <p class="card-description">Data customer dari tabel <strong>customer</strong>.</p>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Provinsi</th>
                                <th>Kota</th>
                                <th>Kecamatan</th>
                                <th>Kodepos - Kelurahan</th>
                                <th>Metode Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($customer->foto_link)
                                        <img src="{{ $customer->foto_link }}" alt="Foto {{ $customer->nama }}" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                                    @elseif($customer->metode_foto === 'blob' && $customer->foto_blob)
                                        <img src="data:{{ $customer->foto_blob_mime ?? 'image/jpeg' }};base64,{{ base64_encode($customer->foto_blob) }}" alt="Foto {{ $customer->nama }}" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                                    @elseif($customer->metode_foto === 'file' && $customer->foto_path)
                                        <img src="{{ asset('storage/' . $customer->foto_path) }}" alt="Foto {{ $customer->nama }}" style="width:64px;height:64px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <span class="badge badge-outline-secondary">-</span>
                                    @endif
                                </td>
                                <td>{{ $customer->nama }}</td>
                                <td>{{ $customer->alamat }}</td>
                                <td>{{ $customer->provinsi }}</td>
                                <td>{{ $customer->kota }}</td>
                                <td>{{ $customer->kecamatan }}</td>
                                <td>{{ $customer->kodepos_kelurahan }}</td>
                                <td>
                                    <span class="badge {{ $customer->metode_foto === 'blob' ? 'badge-gradient-info' : 'badge-gradient-primary' }}">
                                        {{ strtoupper($customer->metode_foto) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data customer.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="cameraModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1050;">
    <div style="width:720px;max-width:calc(100% - 24px);margin:60px auto;background:#fff;border-radius:8px;overflow:hidden;">
        <div style="padding:12px 16px;border-bottom:1px solid #ddd;font-weight:600;">Modal ambil Foto</div>
        <div style="padding:16px;">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div style="border:1px solid #98c47f;height:220px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <video id="cameraStream" autoplay playsinline style="width:100%;height:100%;object-fit:cover;"></video>
                    </div>
                    <button type="button" id="btnStartCamera" class="mt-2 btn btn-info w-100">Pilihan kamera</button>
                </div>
                <div class="col-md-6 mb-3">
                    <div style="border:1px solid #98c47f;height:220px;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        <canvas id="snapshotCanvas" width="320" height="220" style="max-width:100%;height:auto;"></canvas>
                    </div>
                    <button type="button" id="btnTakePhoto" class="mt-2 btn btn-primary w-100">Ambil Foto</button>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" id="btnCloseCamera" class="btn btn-light">Tutup</button>
                <button type="button" id="btnSavePhoto" class="btn btn-success">Simpan Foto</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const PLACEHOLDER = '';
    const csvBase = 'https://raw.githubusercontent.com/guzfirdaus/Wilayah-Administrasi-Indonesia/master/csv';
    const cache = { provinces: null, regencies: null, districts: null, villages: null };

    const provinsi = document.getElementById('provinsi');
    const kota = document.getElementById('kota');
    const kecamatan = document.getElementById('kecamatan');
    const kelurahan = document.getElementById('kodepos_kelurahan');

    const modal = document.getElementById('cameraModal');
    const btnOpenCamera = document.getElementById('btnOpenCamera');
    const btnCloseCamera = document.getElementById('btnCloseCamera');
    const btnStartCamera = document.getElementById('btnStartCamera');
    const btnTakePhoto = document.getElementById('btnTakePhoto');
    const btnSavePhoto = document.getElementById('btnSavePhoto');
    const video = document.getElementById('cameraStream');
    const canvas = document.getElementById('snapshotCanvas');
    const ctx = canvas.getContext('2d');
    const previewImage = document.getElementById('previewImage');
    const previewPlaceholder = document.getElementById('previewPlaceholder');
    const photoInput = document.getElementById('photo_data');
    const customerForm = document.getElementById('customerForm');

    let mediaStream = null;
    let capturedDataUrl = null;

    function sanitizeValue(value) {
        return (value || '').replace(/\s+/g, ' ').trim();
    }

    function parseSemicolonCsv(text) {
        const rows = [];
        let row = [];
        let field = '';
        let inQuotes = false;

        for (let i = 0; i < text.length; i += 1) {
            const ch = text[i];

            if (ch === '"') {
                const next = text[i + 1];
                if (inQuotes && next === '"') {
                    field += '"';
                    i += 1;
                } else {
                    inQuotes = !inQuotes;
                }
                continue;
            }

            if (ch === ';' && !inQuotes) {
                row.push(sanitizeValue(field));
                field = '';
                continue;
            }

            if (ch === '\n' && !inQuotes) {
                row.push(sanitizeValue(field));
                rows.push(row);
                row = [];
                field = '';
                continue;
            }

            if (ch !== '\r') {
                field += ch;
            }
        }

        if (field.length > 0 || row.length > 0) {
            row.push(sanitizeValue(field));
            rows.push(row);
        }

        const headers = rows[0] || [];
        return rows.slice(1)
            .filter(item => item.length >= headers.length && item[0] !== '')
            .map(item => {
                const obj = {};
                headers.forEach((header, index) => {
                    obj[header] = sanitizeValue(item[index] || '');
                });
                return obj;
            });
    }

    async function fetchCsv(fileName) {
        const response = await axios.get(csvBase + '/' + fileName + '.csv', { responseType: 'text' });
        return parseSemicolonCsv(response.data);
    }

    async function getDataset(name) {
        if (cache[name]) {
            return cache[name];
        }
        cache[name] = fetchCsv(name);
        return cache[name];
    }

    function setSelectOptions(selectEl, items, placeholder, valueKey = 'id', labelFn = null) {
        selectEl.innerHTML = '';

        const emptyOption = document.createElement('option');
        emptyOption.value = PLACEHOLDER;
        emptyOption.textContent = placeholder;
        selectEl.appendChild(emptyOption);

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item[valueKey];
            option.textContent = labelFn ? labelFn(item) : item.name;
            selectEl.appendChild(option);
        });

        selectEl.value = PLACEHOLDER;
        selectEl.disabled = items.length === 0;
    }

    function resetChain(level) {
        if (level === 'provinsi') {
            setSelectOptions(kota, [], 'Pilih Kabupaten/Kota');
            setSelectOptions(kecamatan, [], 'Pilih Kecamatan');
            setSelectOptions(kelurahan, [], 'Pilih Kelurahan');
        }
        if (level === 'kota') {
            setSelectOptions(kecamatan, [], 'Pilih Kecamatan');
            setSelectOptions(kelurahan, [], 'Pilih Kelurahan');
        }
        if (level === 'kecamatan') {
            setSelectOptions(kelurahan, [], 'Pilih Kelurahan');
        }
    }

    async function loadProvinsi() {
        const provinces = await getDataset('provinces');
        const items = provinces.map(item => ({ id: item.id, name: item.name }));
        setSelectOptions(provinsi, items, 'Pilih Provinsi');
    }

    provinsi.addEventListener('change', async function () {
        resetChain('provinsi');
        if (provinsi.value === PLACEHOLDER) {
            return;
        }

        const regencies = await getDataset('regencies');
        const items = regencies
            .filter(item => item.province_id === provinsi.value)
            .map(item => ({ id: item.id, name: item.name }));
        setSelectOptions(kota, items, 'Pilih Kabupaten/Kota');
    });

    kota.addEventListener('change', async function () {
        resetChain('kota');
        if (kota.value === PLACEHOLDER) {
            return;
        }

        const districts = await getDataset('districts');
        const items = districts
            .filter(item => item.regency_id === kota.value)
            .map(item => ({ id: item.id, name: item.name }));
        setSelectOptions(kecamatan, items, 'Pilih Kecamatan');
    });

    kecamatan.addEventListener('change', async function () {
        resetChain('kecamatan');
        if (kecamatan.value === PLACEHOLDER) {
            return;
        }

        const villages = await getDataset('villages');
        const items = villages
            .filter(item => item.district_id === kecamatan.value)
            .map(item => ({
                id: item.id,
                name: item.name,
                postal_code: item.postal_code || item.kode_pos || item.kodepos || '-',
            }));

        setSelectOptions(
            kelurahan,
            items,
            'Pilih Kelurahan',
            'id',
            item => item.postal_code + ' - ' + item.name
        );
    });

    function stopCamera() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(track => track.stop());
            mediaStream = null;
        }
        video.srcObject = null;
    }

    async function startCamera() {
        try {
            stopCamera();
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' },
                audio: false,
            });
            video.srcObject = mediaStream;
        } catch (error) {
            alert('Kamera tidak dapat diakses. Pastikan izin kamera sudah diberikan.');
        }
    }

    btnOpenCamera.addEventListener('click', function () {
        modal.style.display = 'block';
        startCamera();
    });

    btnCloseCamera.addEventListener('click', function () {
        modal.style.display = 'none';
        stopCamera();
    });

    btnStartCamera.addEventListener('click', startCamera);

    btnTakePhoto.addEventListener('click', function () {
        if (!video.srcObject) {
            alert('Kamera belum aktif. Klik Pilihan kamera terlebih dahulu.');
            return;
        }

        const width = video.videoWidth || 320;
        const height = video.videoHeight || 220;
        canvas.width = width;
        canvas.height = height;
        ctx.drawImage(video, 0, 0, width, height);
        capturedDataUrl = canvas.toDataURL('image/jpeg', 0.9);
    });

    btnSavePhoto.addEventListener('click', function () {
        if (!capturedDataUrl) {
            alert('Ambil foto terlebih dahulu.');
            return;
        }

        previewImage.src = capturedDataUrl;
        previewImage.style.display = 'block';
        previewPlaceholder.style.display = 'none';
        photoInput.value = capturedDataUrl;

        modal.style.display = 'none';
        stopCamera();
    });

    customerForm.addEventListener('submit', function (event) {
        if (!photoInput.value) {
            event.preventDefault();
            alert('Silakan ambil dan simpan foto customer terlebih dahulu.');
            return;
        }

        if (!provinsi.value || !kota.value || !kecamatan.value || !kelurahan.value) {
            event.preventDefault();
            alert('Silakan lengkapi pilihan wilayah dari provinsi hingga kelurahan.');
            return;
        }

        const provText = provinsi.options[provinsi.selectedIndex]?.text || '';
        const kotaText = kota.options[kota.selectedIndex]?.text || '';
        const kecText = kecamatan.options[kecamatan.selectedIndex]?.text || '';
        const kelText = kelurahan.options[kelurahan.selectedIndex]?.text || '';

        provinsi.innerHTML = '<option selected value="' + provText + '">' + provText + '</option>';
        kota.innerHTML = '<option selected value="' + kotaText + '">' + kotaText + '</option>';
        kecamatan.innerHTML = '<option selected value="' + kecText + '">' + kecText + '</option>';
        kelurahan.innerHTML = '<option selected value="' + kelText + '">' + kelText + '</option>';

        provinsi.disabled = false;
        kota.disabled = false;
        kecamatan.disabled = false;
        kelurahan.disabled = false;
    });

    loadProvinsi().catch(function () {
        provinsi.innerHTML = '<option value="">Gagal memuat data provinsi</option>';
    });
});
</script>
@endsection