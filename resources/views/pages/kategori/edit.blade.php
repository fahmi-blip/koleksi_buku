@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Edit Kategori </h3>
    <a href="{{ route('kategori.index') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Kategori: {{ $kategori->judul }}</h4>
                <form class="forms-sample" action="{{ route('kategori.update', $kategori->idkategori) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">Update kategori</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection