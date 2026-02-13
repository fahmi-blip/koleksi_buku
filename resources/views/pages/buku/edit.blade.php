@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Edit Buku </h3>
    <a href="{{ route('buku.index') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Buku: {{ $buku->judul }}</h4>
                <form class="forms-sample" action="{{ route('buku.update', $buku->idbuku) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Kode Buku</label>
                        <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror" value="{{ old('kode', $buku->kode) }}" required>
                         @error('kode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul" class="form-control" value="{{ old('judul', $buku->judul) }}" required>
                    </div>

                    <div class="form-group">
                        <label>Kategori</label>
                        <select class="form-control" name="idkategori" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->idkategori }}" {{ old('idkategori', $buku->idkategori) == $cat->idkategori ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Pengarang</label>
                        <input type="text" name="pengarang" class="form-control" value="{{ old('pengarang', $buku->pengarang) }}" required>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Update Buku</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection