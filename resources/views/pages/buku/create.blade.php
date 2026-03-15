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
                <form class="forms-sample" id="formBuku" action="{{ route('buku.store') }}" method="POST">
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
                </form>

                <button type="button" id="btnSimpanBuku" class="btn btn-gradient-primary me-2" onclick="submitBuku()">Simpan Buku</button>
                <button type="reset" form="formBuku" class="btn btn-light">Reset</button>

                <script>
                function submitBuku() {
                    const form = document.getElementById('formBuku');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    const btn = document.getElementById('btnSimpanBuku');
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
                    form.submit();
                }
                </script>
            </div>
        </div>
    </div>
</div>
@endsection