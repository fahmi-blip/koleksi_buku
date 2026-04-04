@extends('layouts.kantin')

@push('styles')
<style>
	.payment-shell {
		display: grid;
		grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
		gap: 20px;
		align-items: start;
	}

	.payment-card {
		border: 0;
		border-radius: 20px;
		overflow: hidden;
		background: rgba(15, 23, 42, 0.78);
		border: 1px solid rgba(148, 163, 184, 0.18);
		box-shadow: 0 24px 70px rgba(2, 6, 23, 0.35);
		backdrop-filter: blur(18px);
	}

	.payment-card .card-body {
		padding: 22px;
	}

	.payment-title {
		margin: 0 0 8px;
		font-size: 1.45rem;
		font-weight: 700;
	}

	.payment-subtitle {
		margin: 0;
		color: var(--muted);
		line-height: 1.7;
	}

	.form-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 12px;
		margin-top: 18px;
	}

	.field-full {
		grid-column: 1 / -1;
	}

	.field-label {
		display: block;
		margin-bottom: 8px;
		color: #e2e8f0;
		font-weight: 700;
		font-size: 0.92rem;
	}

	.field-control {
		width: 100%;
		padding: 13px 14px;
		border-radius: 12px;
		border: 1px solid rgba(148, 163, 184, 0.2);
		background: rgba(15, 23, 42, 0.9);
		color: #e2e8f0;
		outline: none;
	}

	.field-control:focus {
		border-color: rgba(56, 189, 248, 0.55);
		box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
	}

	.summary-grid {
		display: grid;
		grid-template-columns: repeat(3, minmax(0, 1fr));
		gap: 12px;
		margin-top: 18px;
	}

	.summary-card {
		position: relative;
		min-height: 128px;
	}

	.summary-card .card-body {
		position: relative;
		z-index: 1;
		padding: 16px;
	}

	.summary-card .card-img-absolute {
		position: absolute;
		right: 0;
		top: 0;
		height: 100%;
		opacity: 0.18;
		pointer-events: none;
	}

	.summary-label {
		font-size: 0.82rem;
		text-transform: uppercase;
		letter-spacing: 0.08em;
		opacity: 0.88;
	}

	.summary-value {
		margin-top: 10px;
		font-size: 1.15rem;
		font-weight: 700;
		line-height: 1.3;
	}

	.action-row {
		display: flex;
		justify-content: flex-end;
		margin-top: 16px;
	}

	.btn-primary-gradient {
		border: none;
		border-radius: 14px;
		padding: 13px 18px;
		font-weight: 700;
		color: #fff;
		background: linear-gradient(135deg, #38bdf8, #2563eb);
		box-shadow: 0 18px 36px rgba(37, 99, 235, 0.24);
	}

	.checkout-button {
		width: 100%;
		margin-top: 18px;
		border: none;
		border-radius: 14px;
		padding: 13px 18px;
		font-weight: 700;
		color: #fff;
		background: linear-gradient(135deg, #f59e0b, #f97316);
		box-shadow: 0 18px 36px rgba(249, 115, 22, 0.24);
	}

	.checkout-button:disabled {
		opacity: 0.45;
		cursor: not-allowed;
	}

	.section-divider {
		margin-top: 22px;
		padding-top: 18px;
		border-top: 1px solid rgba(148, 163, 184, 0.14);
	}

	.section-title {
		margin: 0 0 8px;
		font-size: 1.05rem;
		font-weight: 700;
	}

	.section-note {
		margin: 0;
		color: var(--muted);
		line-height: 1.7;
	}

	.cart-head {
		display: flex;
		justify-content: space-between;
		align-items: center;
		gap: 10px;
		flex-wrap: wrap;
		margin-bottom: 12px;
	}

	.mini-pill {
		padding: 8px 12px;
		border-radius: 999px;
		font-size: 0.82rem;
		font-weight: 600;
		border: 1px solid rgba(56, 189, 248, 0.22);
		background: rgba(56, 189, 248, 0.12);
		color: #bae6fd;
	}

	.table-wrap {
		overflow: auto;
	}

	.cart-table {
		width: 100%;
		min-width: 720px;
		border-collapse: collapse;
	}

	.cart-table thead th {
		padding: 12px 8px;
		text-align: left;
		color: #94a3b8;
		border-bottom: 1px solid rgba(148, 163, 184, 0.16);
		font-weight: 600;
	}

	.cart-table tbody tr {
		border-bottom: 1px solid rgba(148, 163, 184, 0.12);
	}

	.cart-table tbody td {
		padding: 12px 8px;
		vertical-align: top;
	}

	.qty-input {
		width: 84px;
		padding: 9px 10px;
		border-radius: 10px;
		background: rgba(11, 18, 32, 0.95);
		color: #e2e8f0;
		border: 1px solid rgba(148, 163, 184, 0.2);
	}

	.btn-danger-soft {
		padding: 9px 12px;
		border: none;
		border-radius: 10px;
		background: rgba(239, 68, 68, 0.16);
		color: #fca5a5;
		cursor: pointer;
	}

	.summary-list {
		display: grid;
		gap: 12px;
		margin-top: 8px;
	}

	.summary-item {
		display: flex;
		justify-content: space-between;
		gap: 12px;
		color: #cbd5e1;
	}

	.summary-item strong {
		color: #fff;
	}

	.info-list {
		margin: 0;
		padding-left: 18px;
		color: #cbd5e1;
		line-height: 1.75;
	}

	@media (max-width: 992px) {
		.payment-shell,
		.summary-grid,
		.form-grid {
			grid-template-columns: 1fr;
		}
	}
</style>
@endpush

@section('content')
<div class="payment-shell">
	<section class="payment-card">
		<div class="card-body">
			<div class="mb-3 badge-soft primary">Pemesanan Kantin</div>
			<h2 class="payment-title">Buat pesanan baru</h2>
			<p class="payment-subtitle">Pilih vendor, pilih menu, atur jumlah dan catatan, lalu lanjutkan ke pembayaran.</p>

			<div class="summary-grid">
				<div class="text-white card bg-gradient-info summary-card card-img-holder">
					<div class="card-body">
						<img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
						<div class="summary-label">Vendor Dipilih</div>
						<div class="summary-value" id="vendorNameDisplay">-</div>
					</div>
				</div>
				<div class="text-white card bg-gradient-success summary-card card-img-holder">
					<div class="card-body">
						<img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
						<div class="summary-label">Jumlah Item</div>
						<div class="summary-value" id="itemCountDisplay">0 item</div>
					</div>
				</div>
				<div class="text-white card bg-gradient-danger summary-card card-img-holder">
					<div class="card-body">
						<img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
						<div class="summary-label">Total Pembayaran</div>
						<div class="summary-value" id="totalDisplay">Rp 0</div>
					</div>
				</div>
			</div>

			<div class="form-grid">
				<div>
					<label class="field-label" for="vendorSelect">Vendor penyedia</label>
					<select id="vendorSelect" class="field-control">
						@foreach ($vendorsData as $vendor)
							<option value="{{ $vendor['idvendor'] }}">{{ $vendor['nama_vendor'] }}</option>
						@endforeach
					</select>
				</div>
				<div>
					<label class="field-label" for="paymentMethod">Metode pembayaran</label>
					<select id="paymentMethod" class="field-control">
						<option value="midtrans">Midtrans (Multi-metode)</option>
					</select>
				</div>
				<div>
					<label class="field-label" for="menuSelect">Menu</label>
					<select id="menuSelect" class="field-control"></select>
				</div>
				<div>
					<label class="field-label" for="jumlahInput">Jumlah</label>
					<input id="jumlahInput" type="number" min="1" value="1" class="field-control">
				</div>
				<div class="field-full">
					<label class="field-label" for="catatanInput">Catatan item</label>
					<input id="catatanInput" type="text" maxlength="255" placeholder="Contoh: tanpa es, pedas sedang" class="field-control">
				</div>
			</div>

			<div class="action-row">
				<button id="addItemButton" type="button" class="btn-primary-gradient">Tambah ke Keranjang</button>
			</div>

			<div class="section-divider">
				<div class="cart-head">
					<div>
						<h3 class="section-title">Keranjang pesanan</h3>
						<p id="selectedVendorLabel" class="section-note"></p>
					</div>
					<div id="cartSummary" class="mini-pill">0 menu dipilih</div>
				</div>

				<div class="table-wrap">
					<table class="cart-table">
						<thead>
							<tr>
								<th>Menu</th>
								<th>Harga</th>
								<th>Qty</th>
								<th>Subtotal</th>
								<th>Catatan</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody id="cartBody"></tbody>
					</table>
				</div>
			</div>
		</div>
	</section>

	<aside style="display:grid;gap:20px;">
		<section class="payment-card">
			<div class="card-body">
				<h3 class="section-title">Ringkasan checkout</h3>
				<div class="summary-list">
					<div class="summary-item"><span>Total pembayaran</span><strong id="totalDisplayAside">Rp 0</strong></div>
					<div class="summary-item"><span>Jumlah item</span><strong id="itemCountDisplayAside">0 item</strong></div>
					<div class="summary-item"><span>Vendor dipilih</span><strong id="vendorNameDisplayAside">-</strong></div>
				</div>
				<button id="checkoutButton" type="button" class="checkout-button" disabled>Checkout Sekarang</button>
			</div>
		</section>

		<section class="payment-card">
			<div class="card-body">
				<h3 class="section-title">Panduan singkat</h3>
				<ol class="info-list">
					<li>Pilih vendor yang menyediakan menu.</li>
					<li>Pilih menu dari vendor tersebut.</li>
					<li>Tentukan metode pembayaran Midtrans.</li>
					<li>Checkout lalu lanjut ke halaman pembayaran.</li>
				</ol>
			</div>
		</section>
	</aside>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
	document.addEventListener('DOMContentLoaded', () => {
		axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

		const vendors = @json($vendorsData);
		const vendorSelect = document.getElementById('vendorSelect');
		const menuSelect = document.getElementById('menuSelect');
		const jumlahInput = document.getElementById('jumlahInput');
		const catatanInput = document.getElementById('catatanInput');
		const paymentMethod = document.getElementById('paymentMethod');
		const addItemButton = document.getElementById('addItemButton');
		const checkoutButton = document.getElementById('checkoutButton');
		const cartBody = document.getElementById('cartBody');

		const totalDisplay = document.getElementById('totalDisplay');
		const itemCountDisplay = document.getElementById('itemCountDisplay');
		const vendorNameDisplay = document.getElementById('vendorNameDisplay');

		const totalDisplayAside = document.getElementById('totalDisplayAside');
		const itemCountDisplayAside = document.getElementById('itemCountDisplayAside');
		const vendorNameDisplayAside = document.getElementById('vendorNameDisplayAside');

		const cartSummary = document.getElementById('cartSummary');
		const selectedVendorLabel = document.getElementById('selectedVendorLabel');

		let cart = [];

		const money = (value) => new Intl.NumberFormat('id-ID', {
			style: 'currency',
			currency: 'IDR',
			maximumFractionDigits: 0,
		}).format(value);

		function getVendor() {
			return vendors.find((vendor) => String(vendor.idvendor) === String(vendorSelect.value));
		}

		function renderMenuOptions() {
			const vendor = getVendor();
			menuSelect.innerHTML = '';

			if (!vendor) {
				menuSelect.innerHTML = '<option value="">Tidak ada vendor</option>';
				return;
			}

			vendorNameDisplay.textContent = vendor.nama_vendor;
			vendorNameDisplayAside.textContent = vendor.nama_vendor;
			selectedVendorLabel.textContent = `Menu yang tampil hanya milik ${vendor.nama_vendor}.`;

			if (!vendor.menus.length) {
				menuSelect.innerHTML = '<option value="">Menu belum tersedia</option>';
				return;
			}

			vendor.menus.forEach((menu) => {
				const option = document.createElement('option');
				option.value = menu.idmenu;
				option.textContent = `${menu.nama_menu} - ${money(menu.harga)}`;
				menuSelect.appendChild(option);
			});
		}

		function updateSummary() {
			const total = cart.reduce((sum, item) => sum + item.subtotal, 0);
			const count = cart.reduce((sum, item) => sum + item.jumlah, 0);

			totalDisplay.textContent = money(total);
			totalDisplayAside.textContent = money(total);

			itemCountDisplay.textContent = `${count} item`;
			itemCountDisplayAside.textContent = `${count} item`;

			cartSummary.textContent = `${cart.length} menu dipilih`;

			checkoutButton.disabled = cart.length === 0;
		}

		function renderCart() {
			cartBody.innerHTML = '';

			if (!cart.length) {
				cartBody.innerHTML = '<tr><td colspan="6" style="padding:18px 8px;color:#94a3b8;">Keranjang masih kosong.</td></tr>';
				return;
			}

			cart.forEach((item, index) => {
				const row = document.createElement('tr');
				row.innerHTML = `
					<td>${item.nama_menu}</td>
					<td>${money(item.harga)}</td>
					<td>
						<input type="number" min="1" value="${item.jumlah}" data-index="${index}" class="qty-input">
					</td>
					<td>${money(item.subtotal)}</td>
					<td style="max-width:180px;">${item.catatan || '-'}</td>
					<td>
						<button type="button" data-index="${index}" class="btn-danger-soft">Hapus</button>
					</td>
				`;
				cartBody.appendChild(row);
			});

			document.querySelectorAll('.qty-input').forEach((input) => {
				input.addEventListener('input', function () {
					const idx = Number(this.dataset.index);
					const value = Math.max(1, Number(this.value || 1));
					cart[idx].jumlah = value;
					cart[idx].subtotal = cart[idx].harga * value;
					updateSummary();
					renderCart();
				});
			});

			document.querySelectorAll('.btn-danger-soft').forEach((button) => {
				button.addEventListener('click', function () {
					cart.splice(Number(this.dataset.index), 1);
					updateSummary();
					renderCart();
				});
			});
		}

		vendorSelect.addEventListener('change', () => {
			cart = [];
			renderMenuOptions();
			renderCart();
			updateSummary();
		});

		addItemButton.addEventListener('click', () => {
			const vendor = getVendor();
			const menu = vendor?.menus.find((menuItem) => String(menuItem.idmenu) === String(menuSelect.value));
			const jumlah = Math.max(1, Number(jumlahInput.value || 1));
			const catatan = catatanInput.value.trim();

			if (!vendor || !menu) {
				Swal.fire('Menu belum dipilih', 'Silakan pilih vendor dan menu terlebih dahulu.', 'warning');
				return;
			}

			const existingIndex = cart.findIndex((item) => String(item.idmenu) === String(menu.idmenu) && item.catatan === catatan);

			if (existingIndex >= 0) {
				cart[existingIndex].jumlah += jumlah;
				cart[existingIndex].subtotal = cart[existingIndex].jumlah * cart[existingIndex].harga;
			} else {
				cart.push({
					idmenu: menu.idmenu,
					nama_menu: menu.nama_menu,
					harga: Number(menu.harga),
					jumlah,
					subtotal: Number(menu.harga) * jumlah,
					catatan: catatan || null,
				});
			}

			jumlahInput.value = 1;
			catatanInput.value = '';
			renderCart();
			updateSummary();
		});

		checkoutButton.addEventListener('click', () => {
			const vendor = getVendor();

			if (!vendor || !cart.length) {
				Swal.fire('Keranjang kosong', 'Tambahkan menu terlebih dahulu.', 'warning');
				return;
			}

			checkoutButton.disabled = true;
			checkoutButton.textContent = 'Memproses...';

			axios.post(@json(route('kantin.checkout')), {
				vendor_id: vendor.idvendor,
				payment_method: paymentMethod.value,
				items: cart,
			})
				.then((response) => {
					window.location.href = response.data.payment_url;
				})
				.catch((error) => {
					const message = error.response?.data?.message || 'Pesanan gagal diproses.';
					Swal.fire('Gagal', message, 'error');
					checkoutButton.disabled = false;
					checkoutButton.textContent = 'Checkout Sekarang';
				});
		});

		renderMenuOptions();
		renderCart();
		updateSummary();
	});
</script>
@endsection
