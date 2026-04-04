@extends('layouts.main')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h3 class="page-title mb-1">Master Vendor</h3>
    </div>
    <a href="{{ route('admin.vendor.create') }}" class="btn btn-gradient-primary">Tambah Vendor</a>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Vendor</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Vendor</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vendors as $vendor)
                                <tr>
                                    <td>{{ $vendor->idvendor }}</td>
                                    <td>{{ $vendor->nama_vendor }}</td>
                                    <td>
                                        <a href="{{ route('admin.vendor.kelola', $vendor->idvendor) }}" class="btn btn-sm btn-info">Kelola</a>
                                        <a href="{{ route('admin.vendor.edit', $vendor->idvendor) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('admin.vendor.destroy', $vendor->idvendor) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus vendor ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada vendor terdaftar.</td>
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