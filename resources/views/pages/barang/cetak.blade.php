@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Pengaturan Cetak Label</h3>
    <a href="{{ route('barang.index') }}" class="btn btn-light">Kembali</a>
</div>

<div class="row">
    <div class="col-lg-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Barang yang Akan Dicetak</h4>
                <p class="card-description">Atur jumlah label untuk setiap barang.</p>

                <form id="formPrint" action="{{ route('barang.cetakPdf') }}" method="POST" target="_blank">
                    @csrf

                    @foreach($ids as $id)
                        <input type="hidden" name="ids[]" value="{{ $id }}">
                    @endforeach

                    <div class="mb-4 table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th width="90px">Jumlah Label</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data_barang as $barang)
                                <tr>
                                    <td><span class="badge badge-outline-info">{{ $barang->id_barang }}</span></td>
                                    <td>{{ $barang->nama }}</td>
                                    <td>Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="number" name="quantity[{{ $barang->id_barang }}]"
                                               class="form-control form-control-sm qty-input"
                                               value="1" min="1" max="40" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-3">Posisi Awal Cetak pada Kertas Label</h5>
                    <p class="mb-3 text-muted small">
                        Tentukan posisi kolom (X) dan baris (Y) label pertama yang akan diisi.
                        Label sebelum posisi ini akan dibiarkan kosong sehingga Anda tetap dapat
                        menggunakan sisa kertas label yang sudah terpakai sebagian.
                    </p>
                    <div class="mb-4 row">
                        <div class="col-auto">
                            <label class="form-label fw-bold">Kolom X <small class="text-muted">(1 – 5)</small></label>
                            <input type="number" name="start_x" id="inputX"
                                   class="form-control" value="1" min="1" max="5"
                                   style="width:80px;" required>
                        </div>
                        <div class="col-auto">
                            <label class="form-label fw-bold">Baris Y <small class="text-muted">(1 – 8)</small></label>
                            <input type="number" name="start_y" id="inputY"
                                   class="form-control" value="1" min="1" max="8"
                                   style="width:80px;" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary">
                        <i class="mdi mdi-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Preview Posisi Kertas Label</h4>
                <p class="card-description">
                    Kertas TnJ No. 108 &mdash; 5 kolom &times; 8 baris = 40 label<br>
                    <span class="text-success fw-bold">■</span> = posisi mulai cetak &nbsp;
                    <span class="text-warning fw-bold">■</span> = label akan dicetak &nbsp;
                    <span class="text-muted">□</span> = kosong
                </p>

                <div id="labelGrid" style="display:grid;grid-template-columns:repeat(5,1fr);gap:4px;max-width:300px;margin:0 auto;">
                    @for($r = 1; $r <= 8; $r++)
                        @for($c = 1; $c <= 5; $c++)
                            <div class="label-cell"
                                 data-col="{{ $c }}" data-row="{{ $r }}"
                                 style="height:32px;border:1px solid #ccc;border-radius:3px;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:10px;background:#f5f5f5;">
                                {{ $c }},{{ $r }}
                            </div>
                        @endfor
                    @endfor
                </div>

                <p class="mt-3 text-muted small">
                    Kolom (X) berjalan dari kiri ke kanan (1–5).<br>
                    Baris (Y) berjalan dari atas ke bawah (1–8).
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .label-cell { transition: background 0.2s; }
</style>

<script>
(function () {
    const inputX    = document.getElementById('inputX');
    const inputY    = document.getElementById('inputY');
    const qtyInputs = document.querySelectorAll('.qty-input');

    function totalLabels() {
        let t = 0;
        qtyInputs.forEach(i => t += Math.max(1, parseInt(i.value) || 1));
        return t;
    }

    function updateGrid() {
        const x     = parseInt(inputX.value) || 1;
        const y     = parseInt(inputY.value) || 1;
        const start = (y - 1) * 5 + (x - 1);   // 0-indexed
        const total = totalLabels();
        const end   = start + total - 1;

        document.querySelectorAll('.label-cell').forEach(cell => {
            const col  = parseInt(cell.dataset.col);
            const row  = parseInt(cell.dataset.row);
            const idx  = (row - 1) * 5 + (col - 1);

            if (idx === start) {
                cell.style.background = '#28a745';
                cell.style.color      = '#fff';
                cell.style.fontWeight = 'bold';
            } else if (idx > start && idx <= end) {
                cell.style.background = '#ffc107';
                cell.style.color      = '#333';
                cell.style.fontWeight = 'normal';
            } else {
                cell.style.background = '#f5f5f5';
                cell.style.color      = '#999';
                cell.style.fontWeight = 'normal';
            }
        });
    }

    inputX.addEventListener('input', updateGrid);
    inputY.addEventListener('input', updateGrid);
    qtyInputs.forEach(i => i.addEventListener('input', updateGrid));

    updateGrid();
})();
</script>
@endsection
