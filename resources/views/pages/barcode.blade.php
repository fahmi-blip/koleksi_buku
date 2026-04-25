@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Barcode Barang</h3>
    <a href="{{ route('barang.index') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="card-title mb-3">{{ $barang->nama }}</h4>
                <p class="mb-4">
                    ID Barang: <span class="badge badge-outline-info">{{ $barang->id_barang }}</span>
                </p>

                <div class="d-flex justify-content-center align-items-center overflow-auto">
                    {!! $barcodeHtml !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection