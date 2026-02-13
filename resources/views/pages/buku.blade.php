@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title"> Daftar Koleksi Buku </h3>
    <a href="{{ route('buku.create') }}" class="btn btn-gradient-primary">+ Tambah Buku Baru</a>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tabel Data Buku</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Judul Buku</th>
                                <th>Kategori</th>
                                <th>Pengarang</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data_buku as $buku)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><label class="badge badge-outline-info">{{ $buku->kode }}</label></td>
                                <td>{{ $buku->judul }}</td>
                                <td>{{ $buku->kategori->nama_kategori ?? "-"}}</td>
                                <td>{{ $buku->pengarang }}</td>
                                <td>
                                     <a href="{{ route('buku.edit', $buku->idbuku) }}" class="mb-1 btn btn-sm btn-warning ">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <form action="{{ route('buku.destroy', $buku->idbuku) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus buku ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="mb-1 btn btn-sm btn-danger ">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </form> 
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data buku.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection