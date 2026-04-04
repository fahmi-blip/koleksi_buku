@extends('layouts.main')

@section('content')
@push('styles')
<div class="page-header">
    <h3 class="page-title">Latihan Form + HTML Table</h3>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <form id="formBarangLatihan" novalidate>
                    <div class="form-group">
                        <label for="namaBarang">Nama barang :</label>
                        <input type="text" id="namaBarang" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="hargaBarang">Harga barang:</label>
                        <input type="number" id="hargaBarang" class="form-control" required min="0">
                    </div>
                </form>

                <div class="mt-2 mb-4 d-flex justify-content-end">
                    <button type="button" id="btnSubmitLatihan" class="px-4 btn btn-gradient-success"
                            onclick="submitLatihan()">Submit</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT / HAPUS --}}
<div class="modal fade" id="modalEditHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formModal" novalidate>
                    <div class="form-group">
                        <label>ID barang :</label>
                        <input type="text" id="modalId" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama barang :</label>
                        <input type="text" id="modalNama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga barang:</label>
                        <input type="number" id="modalHarga" class="form-control" required min="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" id="btnHapus" class="px-4 btn btn-danger" onclick="hapusBarang()">Hapus</button>
                <button type="button" id="btnUbah"  class="px-4 btn btn-success" onclick="ubahBarang()">Ubah</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let idCounter  = 1;
let barangData = {}; 
let activeId   = null;

function submitLatihan() {
    const form  = document.getElementById('formBarangLatihan');
    const nama  = document.getElementById('namaBarang');
    const harga = document.getElementById('hargaBarang');
    const btn   = document.getElementById('btnSubmitLatihan');

    if (!form.checkValidity()) { form.reportValidity(); return; }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';

    setTimeout(function () {
        const idBarang = 'BRG-' + String(idCounter).padStart(3, '0');
        idCounter++;

        barangData[idBarang] = { nama: nama.value, harga: Number(harga.value) };

        const tbody = document.getElementById('tableBody');
        const tr = document.createElement('tr');
        tr.style.cursor  = 'pointer';
        tr.dataset.id    = idBarang;
        tr.addEventListener('click', function () { openModal(this.dataset.id); });
        tr.innerHTML = '<td>' + escapeHtml(idBarang) + '</td>'
                     + '<td>' + escapeHtml(nama.value) + '</td>'
                     + '<td>Rp ' + Number(harga.value).toLocaleString('id-ID') + '</td>';
        tbody.appendChild(tr);

        nama.value  = '';
        harga.value = '';
        btn.disabled = false;
        btn.innerHTML = 'Submit';
    }, 50);
}

function openModal(id) {
    activeId = id;
    const d = barangData[id];
    document.getElementById('modalId').value    = id;
    document.getElementById('modalNama').value  = d.nama;
    document.getElementById('modalHarga').value = d.harga;
    new bootstrap.Modal(document.getElementById('modalEditHapus')).show();
}

function hapusBarang() {
    const btn = document.getElementById('btnHapus');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';

    setTimeout(function () {
        const tr = document.querySelector('#tableBody tr[data-id="' + activeId + '"]');
        if (tr) tr.remove();
        delete barangData[activeId];

        bootstrap.Modal.getInstance(document.getElementById('modalEditHapus')).hide();
        btn.disabled = false;
        btn.innerHTML = 'Hapus';
    }, 50);
}

function ubahBarang() {
    const form  = document.getElementById('formModal');
    const nama  = document.getElementById('modalNama');
    const harga = document.getElementById('modalHarga');
    const btn   = document.getElementById('btnUbah');

    if (!form.checkValidity()) { form.reportValidity(); return; }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';

    setTimeout(function () {
        barangData[activeId] = { nama: nama.value, harga: Number(harga.value) };

        const tr = document.querySelector('#tableBody tr[data-id="' + activeId + '"]');
        if (tr) {
            const cells = tr.querySelectorAll('td');
            cells[1].textContent = nama.value;
            cells[2].textContent = 'Rp ' + Number(harga.value).toLocaleString('id-ID');
        }

        bootstrap.Modal.getInstance(document.getElementById('modalEditHapus')).hide();
        btn.disabled = false;
        btn.innerHTML = 'Ubah';
    }, 50);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
</script>
@endpush
