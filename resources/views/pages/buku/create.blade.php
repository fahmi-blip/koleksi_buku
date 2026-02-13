@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Tambah Buku </h3>
    <a href="{{ route('buku.index') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Buku Baru</h4>
                <p class="card-description"> Masukkan detail buku dengan lengkap. </p>
                <form class="forms-sample" action="{{ route('buku.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Kode Buku</label>
                        <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror" placeholder="Contoh: NV-001" value="{{ old('kode') }}" required>
                        @error('kode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul" class="form-control" placeholder="Judul lengkap" value="{{ old('judul') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="idkategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->idkategori }}" {{ old('idkategori') == $cat->idkategori ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pengarang</label>
                        <input type="text" name="pengarang" class="form-control" placeholder="Nama Pengarang" value="{{ old('pengarang') }}" required>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Simpan Buku</button>
                    <button type="reset" class="btn btn-light">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection