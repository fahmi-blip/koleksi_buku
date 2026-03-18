@extends('layouts.main')

@section('content')
<div class="page-header">
    <h3 class="page-title">Point of Sales (Kasir)</h3>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Transaksi</h4>

                <div class="row">
                    <div class="mb-3 col-md-6 form-group">
                        <label for="kode_barang" class="fw-bold text-warning">Kode barang :</label>
                        <input type="text" id="kode_barang" class="form-control"
                               placeholder="Masukkan kode barang, tekan Enter">
                    </div>

                    <div class="mb-3 col-md-6 form-group">
                        <label for="nama_barang" class="fw-bold text-danger">Nama barang :</label>
                        <input type="text" id="nama_barang" class="form-control"
                               style="background-color: #ffe0e0;" readonly placeholder="Nama barang">
                    </div>

                    <div class="mb-3 col-md-6 form-group">
                        <label for="harga_barang" class="fw-bold text-warning">Harga barang :</label>
                        <input type="text" id="harga_barang" class="form-control"
                               style="background-color: #fff3e0;" readonly placeholder="Harga barang">
                    </div>

                    <div class="mb-3 col-md-6 form-group">
                        <label for="jumlah" class="fw-bold text-warning">Jumlah:</label>
                        <input type="number" id="jumlah" class="form-control"
                               value="1" min="1">
                    </div>
                </div>

                <div class="mb-4 text-end">
                    <button type="button" id="btnTambahkan" class="btn btn-gradient-success" disabled>
                        Tambahkan
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="tabelCart">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Subtotal</th>
                                <th width="80px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td id="totalHarga" class="fw-bold text-success">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-3 text-end">
                    <button type="button" id="btnBayar" class="btn btn-gradient-info" disabled>
                        Bayar
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    
    let cart = []; 
    let currentBarang = null; 


    const inputKode    = document.getElementById('kode_barang');
    const inputNama    = document.getElementById('nama_barang');
    const inputHarga   = document.getElementById('harga_barang');
    const inputJumlah  = document.getElementById('jumlah');
    const btnTambahkan = document.getElementById('btnTambahkan');
    const btnBayar     = document.getElementById('btnBayar');
    const cartBody     = document.getElementById('cartBody');
    const totalHargaEl = document.getElementById('totalHarga');

    axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

    function formatRupiah(angka) {
        return 'Rp ' + parseInt(angka).toLocaleString('id-ID');
    }

    function resetForm() {
        inputKode.value    = '';
        inputNama.value    = '';
        inputHarga.value   = '';
        inputJumlah.value  = 1;
        currentBarang      = null;
        btnTambahkan.disabled = true;
        inputKode.focus();
    }

    function hitungTotal() {
        const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
        totalHargaEl.textContent = formatRupiah(total);
        btnBayar.disabled = cart.length === 0;
    }

    function renderCart() {
        cartBody.innerHTML = '';
        cart.forEach(function (item, index) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.id_barang}</td>
                <td>${item.nama}</td>
                <td>${formatRupiah(item.harga)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm input-jumlah"
                           value="${item.jumlah}" min="1" data-index="${index}" style="width:80px;">
                </td>
                <td class="subtotal-cell">${formatRupiah(item.subtotal)}</td>
                <td>
                    <button class="btn btn-sm btn-gradient-danger btn-hapus" data-index="${index}">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            `;
            cartBody.appendChild(tr);
        });

        document.querySelectorAll('.input-jumlah').forEach(function (input) {
            input.addEventListener('input', function () {
                const idx = parseInt(this.dataset.index);
                const newJumlah = parseInt(this.value) || 1;
                if (newJumlah < 1) {
                    this.value = 1;
                    return;
                }
                cart[idx].jumlah   = newJumlah;
                cart[idx].subtotal = cart[idx].harga * newJumlah;
                const row = this.closest('tr');
                row.querySelector('.subtotal-cell').textContent = formatRupiah(cart[idx].subtotal);
                hitungTotal();
            });
        });

        document.querySelectorAll('.btn-hapus').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.dataset.index);
                cart.splice(idx, 1);
                renderCart();
                hitungTotal();
            });
        });

        hitungTotal();
    }

    inputKode.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();

        const kode = inputKode.value.trim();
        if (!kode) return;

        inputNama.value   = 'Mencari...';
        inputHarga.value  = '';
        currentBarang     = null;
        btnTambahkan.disabled = true;

        axios.get(`/pos/barang/${kode}`)
            .then(function (response) {
                currentBarang     = response.data;
                inputNama.value   = currentBarang.nama;
                inputHarga.value  = currentBarang.harga;
                inputJumlah.value = 1;
                updateTambahkanState();
                inputJumlah.focus();
            })
            .catch(function () {
                inputNama.value  = 'Barang tidak ditemukan';
                inputHarga.value = '';
                currentBarang    = null;
                btnTambahkan.disabled = true;
            });
    });

    function updateTambahkanState() {
        const jumlah = parseInt(inputJumlah.value) || 0;
        btnTambahkan.disabled = !(currentBarang && jumlah > 0);
    }

    inputJumlah.addEventListener('input', updateTambahkanState);

    btnTambahkan.addEventListener('click', function () {
        if (!currentBarang) return;

        const jumlah = parseInt(inputJumlah.value) || 1;
        if (jumlah <= 0) return;

        const kode = currentBarang.id_barang;
        const existing = cart.findIndex(item => item.id_barang === kode);

        if (existing >= 0) {
            cart[existing].jumlah  += jumlah;
            cart[existing].subtotal = cart[existing].harga * cart[existing].jumlah;
        } else {
            cart.push({
                id_barang : currentBarang.id_barang,
                nama      : currentBarang.nama,
                harga     : currentBarang.harga,
                jumlah    : jumlah,
                subtotal  : currentBarang.harga * jumlah,
            });
        }

        renderCart();
        resetForm();
    });

    btnBayar.addEventListener('click', function () {
        if (cart.length === 0) return;

        const total = cart.reduce((sum, item) => sum + item.subtotal, 0);

        btnBayar.disabled = true;
        btnBayar.textContent = 'Memproses...';

        axios.post('/pos/store', {
            total : total,
            items : cart,
        })
        .then(function (response) {
            Swal.fire({
                icon             : 'success',
                title            : 'Pembayaran Berhasil!',
                text             : response.data.message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#4CAF50',
            }).then(function () {
                cart = [];
                renderCart();
                resetForm();
                btnBayar.textContent = 'Bayar';
            });
        })
        .catch(function (error) {
            const msg = error.response?.data?.message ?? 'Gagal menyimpan transaksi.';
            Swal.fire({
                icon : 'error',
                title: 'Gagal!',
                text : msg,
            });
            btnBayar.disabled    = false;
            btnBayar.textContent = 'Bayar';
        });
    });

    hitungTotal();
});
</script>
@endsection
