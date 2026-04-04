<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\AdminVendorController;
use App\Http\Controllers\VendorKantinController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PdfGenerator;


Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/kantin', [PemesananController::class, 'index'])->name('kantin.order');
Route::get('/kantin/vendors/{vendor}/menus', [PemesananController::class, 'menusByVendor'])->name('kantin.vendors.menus');
Route::post('/kantin/checkout', [PemesananController::class, 'store'])->name('kantin.checkout');
Route::get('/kantin/payment/{pesanan:order_code}', [PemesananController::class, 'payment'])->name('kantin.payment.show');
Route::post('/kantin/payment/{pesanan:order_code}/confirm', [PemesananController::class, 'confirmPayment'])->name('kantin.payment.confirm');
Route::post('/kantin/payment/webhook/midtrans', [PemesananController::class, 'webhookMidtrans'])->withoutMiddleware(['web'])->name('kantin.webhook.midtrans');

Route::middleware(['auth', 'verified','check.session'])->group(function () {
    Route::get('/', [PageController::class, 'homePage'])->name('home');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::get('/kategori/create', [KategoriController::class, 'create'])->name('kategori.create');
    Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::get('/kategori/edit/{id}', [KategoriController::class, 'edit'])->name('kategori.edit');
    Route::put('/kategori/update/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/destroy/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    
    
    Route::get('/buku', [BukuController::class, 'index'])->name('buku.index');
    Route::get('/buku/create', [BukuController::class, 'create'])->name('buku.create');
    Route::post('/buku/store', [BukuController::class, 'store'])->name('buku.store');
    Route::get('/buku/edit/{id}', [BukuController::class, 'edit'])->name('buku.edit');
    Route::put('/buku/update/{id}', [BukuController::class, 'update'])->name('buku.update');
    Route::delete('/buku/destroy/{id}', [BukuController::class, 'destroy'])->name('buku.destroy');

   Route::get('/dokumen/pengumuman', [PdfGenerator::class, 'cetakLabel'])->name('pengumuman');

    Route::middleware('role:admin')->group(function () {
        // Barang & Cetak Label TnJ 108
        Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang/store', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/edit/{id}', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/update/{id}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/destroy/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
        Route::post('/barang/cetak', [BarangController::class, 'formCetak'])->name('barang.formCetak');
        Route::post('/barang/cetak/pdf', [BarangController::class, 'cetakPdf'])->name('barang.cetakPdf');
    });

    Route::get('/latihan/table', [PageController::class, 'latihanTable'])->name('latihan.table');
    Route::get('/latihan/datatables', [PageController::class, 'latihanDatatables'])->name('latihan.datatables');
    Route::get('/latihan/select', [PageController::class, 'latihanSelect'])->name('latihan.select');
    Route::get('/wilayah', [PageController::class, 'wilayah'])->name('wilayah');

    Route::middleware('role:admin')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/barang/{id}', [PosController::class, 'getBarang'])->name('pos.getBarang');
        Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');
    });
});

Route::middleware(['auth', 'check.session', 'role:admin'])
    ->prefix('admin/vendor')
    ->name('admin.vendor.')
    ->group(function () {
        Route::get('/', [AdminVendorController::class, 'index'])->name('index');
        Route::get('/create', [AdminVendorController::class, 'create'])->name('create');
        Route::post('/', [AdminVendorController::class, 'store'])->name('store');
        Route::get('/{vendor}/edit', [AdminVendorController::class, 'edit'])->name('edit');
        Route::put('/{vendor}', [AdminVendorController::class, 'update'])->name('update');
        Route::delete('/{vendor}', [AdminVendorController::class, 'destroy'])->name('destroy');

        Route::get('/{vendor}/kelola', [VendorKantinController::class, 'adminKantin'])->name('kelola');
        Route::post('/{vendor}/menus', [VendorKantinController::class, 'adminStoreMenu'])->name('menu.store');
        Route::put('/{vendor}/menus/{menu}', [VendorKantinController::class, 'adminUpdateMenu'])->name('menu.update');
        Route::delete('/{vendor}/menus/{menu}', [VendorKantinController::class, 'adminDestroyMenu'])->name('menu.destroy');
        Route::delete('/{vendor}/pesanan/{pesanan}', [VendorKantinController::class, 'adminDestroyOrder'])->name('pesanan.destroy');
    });

require __DIR__.'/auth.php';
