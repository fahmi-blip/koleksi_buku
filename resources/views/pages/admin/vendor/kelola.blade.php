@extends('layouts.main')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h3 class="page-title mb-1">Kelola Vendor: {{ $vendor->nama_vendor }}</h3>
    </div>
    <a href="{{ route('admin.vendor.index') }}" class="btn btn-light">Kembali ke Master Vendor</a>
</div>

<div class="row mb-3">
    <div class="col-md-3 grid-margin stretch-card">
        <div class="text-white card bg-gradient-info card-img-holder">
            <div class="card-body">
                <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                <h6 class="mb-3 font-weight-normal">Total Menu <i class="mdi mdi-bookmark-outline mdi-24px float-end"></i></h6>
                <h3>{{ $summary['total_menu'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 stretch-card grid-margin">
                <div class="text-white card bg-gradient-danger card-img-holder">
                  <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="mb-3 font-weight-normal">Pending <i class="mdi mdi-chart-line mdi-24px float-end"></i>
                    </h4>
                    <h2>{{ $summary['total_order_pending'] }}</h2>
                  </div>
                </div>
              </div>
    <div class="col-md-3 stretch-card grid-margin">
                <div class="text-white card bg-gradient-success card-img-holder">
                  <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="mb-3 font-weight-normal">Lunas<i class="mdi mdi-diamond mdi-24px float-end"></i>
                    </h4>
                    <h2>{{ $summary['total_order_lunas'] }}</h2>
                  </div>
                </div>
              </div>
    <div class="col-md-3 stretch-card grid-margin">
                <div class="text-white card bg-gradient-warning card-img-holder">
                  <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
                    <h4 class="mb-3 font-weight-normal">Pendapatan<i class="mdi mdi-diamond mdi-24px float-end"></i>
                    </h4>
                    <h2>Rp {{ number_format($summary['total_pendapatan'], 0, ',', '.') }}</h2>
                  </div>
                </div>
              </div>
</div>

<div class="row">
    <div class="col-lg-5 grid-margin stretch-card" id="menu-form">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $editMenu ? 'Edit Menu' : 'Tambah Menu' }}</h4>
                <form method="POST" action="{{ $editMenu ? route('admin.vendor.menu.update', [$vendor->idvendor, $editMenu->idmenu]) : route('admin.vendor.menu.store', $vendor->idvendor) }}">
                    @csrf
                    @if ($editMenu)
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Nama menu</label>
                        <input type="text" name="nama_menu" class="form-control" value="{{ old('nama_menu', $editMenu->nama_menu ?? '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="harga" class="form-control" min="0" value="{{ old('harga', $editMenu->harga ?? 0) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Path gambar</label>
                        <input type="text" name="path_gambar" class="form-control" value="{{ old('path_gambar', $editMenu->path_gambar ?? '') }}" placeholder="opsional">
                    </div>
                    <div class=" mb-3">
                        <input type="checkbox" class="form-check-input" id="is_available" name="is_available" value="1" {{ old('is_available', $editMenu->is_available ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label mr-2" for="is_available">Menu tersedia</label>
                    </div>
                    <button type="submit" class="btn btn-gradient-primary">{{ $editMenu ? 'Simpan Perubahan' : 'Simpan Menu' }}</button>
                    @if ($editMenu)
                        <a href="{{ route('admin.vendor.kelola', $vendor->idvendor) }}" class="btn btn-light ms-2">Batal</a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Master Menu</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th width="170">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menus as $menu)
                                <tr>
                                    <td>{{ $menu->nama_menu }}</td>
                                    <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $menu->is_available ? 'bg-success' : 'bg-secondary' }}">{{ $menu->is_available ? 'Tersedia' : 'Nonaktif' }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.vendor.kelola', ['vendor' => $vendor->idvendor, 'menu' => $menu->idmenu]) }}#menu-form" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('admin.vendor.menu.destroy', [$vendor->idvendor, $menu->idmenu]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">Belum ada menu.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Riwayat Pesanan Pending</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingOrders as $order)
                                <tr>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->nama }}</td>
                                    <td>{{ $order->details->map(fn ($detail) => ($detail->menu->nama_menu ?? '-') . ' x ' . $detail->jumlah)->implode(', ') }}</td>
                                    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td>{{ strtoupper(str_replace('_', ' ', $order->metode_bayar)) }}</td>
                                    <td>
                                        <span class="badge bg-warning">
                                            {{ strtoupper($order->midtrans_status ?? data_get($order->gateway_payload, 'midtrans_status') ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.vendor.pesanan.destroy', [$vendor->idvendor, $order->order_code]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Tidak ada pesanan pending.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Riwayat Pesanan Lunas</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Pelanggan</th>
                                <th>Item</th>
                                <th>Total</th>
                                <th>Waktu Bayar</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($paidOrders as $order)
                                <tr>
                                    <td>{{ $order->order_code }}</td>
                                    <td>{{ $order->nama }}</td>
                                    <td>{{ $order->details->map(fn ($detail) => ($detail->menu->nama_menu ?? '-') . ' x ' . $detail->jumlah)->implode(', ') }}</td>
                                    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td>{{ optional($order->paid_at)?->locale('id')->translatedFormat('d F Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ strtoupper($order->midtrans_status ?? data_get($order->gateway_payload, 'midtrans_status') ?? 'settlemant') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.vendor.pesanan.destroy', [$vendor->idvendor, $order->order_code]) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Tidak ada pesanan lunas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
