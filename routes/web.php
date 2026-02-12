<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\KategoriController;


// Route::get('/', function () {
//     return view('pages.home');
// })->middleware(['auth', 'verified','check.session'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified','check.session'])->group(function () {
    Route::get('/', [PageController::class, 'homePage'])->name('home');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori');
});

require __DIR__.'/auth.php';
