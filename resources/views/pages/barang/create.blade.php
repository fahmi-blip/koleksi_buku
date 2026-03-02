@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Tambah Barang</h3>
    <a href="{{ route('barang.index') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Barang Baru</h4>
                <p class="card-description">Masukkan detail barang dengan lengkap.</p>
                <form class="forms-sample" action="{{ route('barang.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>ID Barang <small class="text-muted">(maks. 8 karakter)</small></label>
                        <input type="text" name="id_barang"
                               class="form-control @error('id_barang') is-invalid @enderror"
                               placeholder="Contoh: BRG-001" value="{{ old('id_barang') }}" required maxlength="8">
                        @error('id_barang') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Nama barang" value="{{ old('nama') }}" required maxlength="50">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga"
                               class="form-control @error('harga') is-invalid @enderror"
                               placeholder="0" value="{{ old('harga') }}" required min="0">
                        @error('harga') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Simpan</button>
                    <button type="reset" class="btn btn-light">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
