@extends('layouts.main')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .select2-container .select2-selection--single {
        height: 38px;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h3 class="page-title">Latihan Select</h3>
</div>

<div class="row">

    {{-- CARD 1: Select biasa --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Select</h4>

                {{-- Form tambah kota --}}
                <form id="formKota1" novalidate>
                    <div class="form-group">
                        <label for="inputKota1">Kota:</label>
                        <input type="text" id="inputKota1" class="form-control" required>
                    </div>
                </form>
                <div class="mb-3 d-flex justify-content-end">
                    <button type="button" id="btnTambah1" class="px-4 btn btn-gradient-success"
                            onclick="tambahKota1()">Tambahkan</button>
                </div>

                <div class="form-group">
                    <label for="selectKota1">Select Kota:</label>
                    <select id="selectKota1" class="form-control" onchange="kotaTerpilih1()">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <p class="mt-3"><strong>Kota Terpilih:</strong> <span id="kotaTerpilihLabel1"></span></p>
            </div>
        </div>
    </div>

    {{-- CARD 2: Select2 --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">select 2</h4>

                {{-- Form tambah kota --}}
                <form id="formKota2" novalidate>
                    <div class="form-group">
                        <label for="inputKota2">Kota:</label>
                        <input type="text" id="inputKota2" class="form-control" required>
                    </div>
                </form>
                <div class="mb-3 d-flex justify-content-end">
                    <button type="button" id="btnTambah2" class="px-4 btn btn-gradient-success"
                            onclick="tambahKota2()">Tambahkan</button>
                </div>

                <div class="form-group">
                    <label for="selectKota2">Select Kota:</label>
                    <select id="selectKota2" class="form-control" style="width:100%">
                        <option value="">-- Pilih Kota --</option>
                    </select>
                </div>

                <p class="mt-3"><strong>Kota Terpilih:</strong> <span id="kotaTerpilihLabel2"></span></p>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function tambahKota1() {
    const form  = document.getElementById('formKota1');
    const input = document.getElementById('inputKota1');
    const btn   = document.getElementById('btnTambah1');

    if (!form.checkValidity()) { form.reportValidity(); return; }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';

    setTimeout(function () {
        const kota = input.value.trim();
        const select = document.getElementById('selectKota1');
        const opt = document.createElement('option');
        opt.value = kota;
        opt.textContent = kota;
        select.appendChild(opt);

        input.value = '';
        btn.disabled = false;
        btn.innerHTML = 'Tambahkan';
    }, 50);
}

function kotaTerpilih1() {
    const select = document.getElementById('selectKota1');
    const label  = document.getElementById('kotaTerpilihLabel1');
    label.textContent = select.value || '';
}

$(document).ready(function () {
    $('#selectKota2').select2({
        placeholder: '-- Pilih Kota --',
        allowClear: true
    });

    $('#selectKota2').on('change', function () {
        document.getElementById('kotaTerpilihLabel2').textContent = $(this).val() || '';
    });
});

function tambahKota2() {
    const form  = document.getElementById('formKota2');
    const input = document.getElementById('inputKota2');
    const btn   = document.getElementById('btnTambah2');

    if (!form.checkValidity()) { form.reportValidity(); return; }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';

    setTimeout(function () {
        const kota = input.value.trim();
        const newOption = new Option(kota, kota, false, false);
        $('#selectKota2').append(newOption).trigger('change');

        input.value = '';
        btn.disabled = false;
        btn.innerHTML = 'Tambahkan';
    }, 50);
}
</script>
@endpush
