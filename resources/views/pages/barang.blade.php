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
                        <button type="submit" class="btn btn-gradient-success">
                            <i class="mdi mdi-printer"></i> Cetak Label Terpilih
                        </button>
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
@endsection
