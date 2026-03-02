@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Edit Barang</h3>
    <a href="{{ route('barang.index') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Barang</h4>
                <form class="forms-sample" action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>ID Barang</label>
                        <input type="text" class="form-control" value="{{ $barang->id_barang }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $barang->nama) }}" required maxlength="50">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga"
                               class="form-control @error('harga') is-invalid @enderror"
                               value="{{ old('harga', $barang->harga) }}" required min="0">
                        @error('harga') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Simpan Perubahan</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
