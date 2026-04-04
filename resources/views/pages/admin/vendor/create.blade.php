@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Tambah Vendor</h3>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.vendor.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama vendor</label>
                        <input type="text" name="nama_vendor" class="form-control" value="{{ old('nama_vendor') }}" required>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary">Simpan</button>
                    <a href="{{ route('admin.vendor.index') }}" class="btn btn-light ms-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection