@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<style>
    #dtBarang tbody tr { cursor: pointer; }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title">Latihan Form + DataTables</h3>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                {{-- FORM --}}
                <form id="formBarangDT" novalidate>
                    <div class="form-group">
                        <label for="namaBarangDT">Nama barang :</label>
                        <input type="text" id="namaBarangDT" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="hargaBarangDT">Harga barang:</label>
                        <input type="number" id="hargaBarangDT" class="form-control" required min="0">
                    </div>
                </form>

                <div class="mt-2 mb-4 d-flex justify-content-end">
                    <button type="button" id="btnSubmitDT" class="px-4 btn btn-gradient-success"
                            onclick="submitDT()">Submit</button>
                </div>

                {{-- DATATABLE --}}
                <div class="table-responsive">
                    <table id="dtBarang" class="table table-bordered w-100">
                        <thead>
                            <tr>
                                <th>ID barang</th>
                                <th>Nama</th>
                                <th>Harga</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT / HAPUS --}}
<div class="modal fade" id="modalEditHapusDT" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formModalDT" novalidate>
                    <div class="form-group">
                        <label>ID barang :</label>
                        <input type="text" id="modalIdDT" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Nama barang :</label>
                        <input type="text" id="modalNamaDT" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Harga barang:</label>
                        <input type="number" id="modalHargaDT" class="form-control" required min="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" id="btnHapusDT" class="px-4 btn btn-danger" onclick="hapusBarangDT()">Hapus</button>
                <button type="button" id="btnUbahDT"  class="px-4 btn btn-success" onclick="ubahBarangDT()">Ubah</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script>
let dtTable;
let idCounterDT = 1;
let dtData      = {}; 
let activeRowDT = null;

$(document).ready(function () {
    dtTable = $('#dtBarang').DataTable({
        columns: [
            { title: 'ID barang' },
            { title: 'Nama' },
            { title: 'Harga' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json'
        }
    });
    $('#dtBarang tbody').on('click', 'tr', function () {
        const row = dtTable.row(this);
        if (!row.data()) return;
        activeRowDT = row;
        const id = row.data()[0];
        document.getElementById('modalIdDT').value    = id;
        document.getElementById('modalNamaDT').value  = dtData[id].nama;
        document.getElementById('modalHargaDT').value = dtData[id].harga;
        new bootstrap.Modal(document.getElementById('modalEditHapusDT')).show();
    });
});

function submitDT() {
    const form  = document.getElementById('formBarangDT');
    const nama  = document.getElementById('namaBarangDT');
    const harga = document.getElementById('hargaBarangDT');
    const btn   = document.getElementById('btnSubmitDT');

    if (!form.checkValidity()) { form.reportValidity(); return; }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';

    setTimeout(function () {
        const idBarang = 'BRG-' + String(idCounterDT).padStart(3, '0');
        idCounterDT++;

        dtData[idBarang] = { nama: nama.value, harga: Number(harga.value) };

        dtTable.row.add([
            idBarang,
            escapeHtml(nama.value),
            'Rp ' + Number(harga.value).toLocaleString('id-ID')
        ]).draw();

        nama.value  = '';
        harga.value = '';
        btn.disabled = false;
        btn.innerHTML = 'Submit';
    }, 50);
}

function hapusBarangDT() {
    const btn = document.getElementById('btnHapusDT');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';

    setTimeout(function () {
        const id = activeRowDT.data()[0];
        delete dtData[id];
        activeRowDT.remove().draw();

        bootstrap.Modal.getInstance(document.getElementById('modalEditHapusDT')).hide();
        btn.disabled = false;
        btn.innerHTML = 'Hapus';
    }, 50);
}

function ubahBarangDT() {
    const form  = document.getElementById('formModalDT');
    const nama  = document.getElementById('modalNamaDT');
    const harga = document.getElementById('modalHargaDT');
    const btn   = document.getElementById('btnUbahDT');

    if (!form.checkValidity()) { form.reportValidity(); return; }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';

    setTimeout(function () {
        const id = activeRowDT.data()[0];
        dtData[id] = { nama: nama.value, harga: Number(harga.value) };

        activeRowDT.data([
            id,
            escapeHtml(nama.value),
            'Rp ' + Number(harga.value).toLocaleString('id-ID')
        ]).draw();

        bootstrap.Modal.getInstance(document.getElementById('modalEditHapusDT')).hide();
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
