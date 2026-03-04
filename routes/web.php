<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KartuPembayaranController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// === SANTRI ROUTES ===
Route::middleware(['auth', 'role:santri'])->prefix('santri')->name('santri.')->group(function () {
    Route::get('/dashboard', [SantriController::class, 'dashboard'])->name('dashboard');
    Route::get('/buat-tagihan', [SantriController::class, 'formBuatTagihan'])->name('buat-tagihan');
    Route::post('/buat-tagihan', [SantriController::class, 'storeTagihan'])->name('store-tagihan');
    Route::get('/konfirmasi-tagihan/{id}', [SantriController::class, 'konfirmasiTagihan'])->name('konfirmasi-tagihan');
    Route::get('/tagihan/{id}', [SantriController::class, 'showTagihan'])->name('show-tagihan');
    Route::get('/upload-pembayaran/{tagihan}', [SantriController::class, 'uploadPembayaran'])->name('upload-pembayaran');
    Route::post('/upload-pembayaran/{tagihan}', [SantriController::class, 'storeUploadPembayaran'])->name('store-pembayaran');
    Route::get('/tagihan-pembayaran', [SantriController::class, 'tagihanPembayaran'])->name('tagihan-pembayaran');
    Route::get('/kartu-pembayaran', [SantriController::class, 'kartuPembayaran'])->name('kartu-pembayaran');
});

// === BENDAHARA ROUTES ===
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [BendaharaController::class, 'dashboard'])->name('dashboard');
    Route::get('/pembayaran-menunggu', [BendaharaController::class, 'pembayaranMenunggu'])->name('pembayaran-menunggu');
    Route::get('/pembayaran/{id}', [BendaharaController::class, 'showPembayaran'])->name('show-pembayaran');
    Route::post('/pembayaran/{id}/verifikasi', [BendaharaController::class, 'verifikasiPembayaran'])->name('verifikasi-pembayaran');
    Route::get('/riwayat-verifikasi', [BendaharaController::class, 'riwayatVerifikasi'])->name('riwayat-verifikasi');
    Route::get('/laporan-pembayaran', [BendaharaController::class, 'laporanPembayaran'])->name('laporan-pembayaran');
    Route::get('/data-santri', [BendaharaController::class, 'dataSantri'])->name('data-santri');
    Route::get('/data-santri/{id}', [BendaharaController::class, 'detailSantri'])->name('detail-santri');
});

// === ADMIN ROUTES ===
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'indexUsers'])->name('users.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users.show');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Tahun Ajaran Management
    Route::get('/tahun-ajaran', [AdminController::class, 'indexTahunAjaran'])->name('tahun-ajaran.index');
    Route::get('/tahun-ajaran/create', [AdminController::class, 'createTahunAjaran'])->name('tahun-ajaran.create');
    Route::post('/tahun-ajaran', [AdminController::class, 'storeTahunAjaran'])->name('tahun-ajaran.store');
    Route::get('/tahun-ajaran/{id}/edit', [AdminController::class, 'editTahunAjaran'])->name('tahun-ajaran.edit');
    Route::put('/tahun-ajaran/{id}', [AdminController::class, 'updateTahunAjaran'])->name('tahun-ajaran.update');
    Route::delete('/tahun-ajaran/{id}', [AdminController::class, 'destroyTahunAjaran'])->name('tahun-ajaran.destroy');
    
    // Kategori Tagihan Management
    Route::get('/kategori-tagihan', [AdminController::class, 'indexKategoriTagihan'])->name('kategori-tagihan.index');
    Route::get('/kategori-tagihan/create', [AdminController::class, 'createKategoriTagihan'])->name('kategori-tagihan.create');
    Route::post('/kategori-tagihan', [AdminController::class, 'storeKategoriTagihan'])->name('kategori-tagihan.store');
    Route::get('/kategori-tagihan/{id}/edit', [AdminController::class, 'editKategoriTagihan'])->name('kategori-tagihan.edit');
    Route::put('/kategori-tagihan/{id}', [AdminController::class, 'updateKategoriTagihan'])->name('kategori-tagihan.update');
    Route::delete('/kategori-tagihan/{id}', [AdminController::class, 'destroyKategoriTagihan'])->name('kategori-tagihan.destroy');
    
    // Jenis Tagihan Management
    Route::get('/jenis-tagihan', [AdminController::class, 'indexJenisTagihan'])->name('jenis-tagihan.index');
    Route::get('/jenis-tagihan/create', [AdminController::class, 'createJenisTagihan'])->name('jenis-tagihan.create');
    Route::post('/jenis-tagihan', [AdminController::class, 'storeJenisTagihan'])->name('jenis-tagihan.store');
    Route::get('/jenis-tagihan/{id}/edit', [AdminController::class, 'editJenisTagihan'])->name('jenis-tagihan.edit');
    Route::put('/jenis-tagihan/{id}', [AdminController::class, 'updateJenisTagihan'])->name('jenis-tagihan.update');
    Route::delete('/jenis-tagihan/{id}', [AdminController::class, 'destroyJenisTagihan'])->name('jenis-tagihan.destroy');
    
    // Tagihan Management
    Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
    Route::get('/tagihan/create', [TagihanController::class, 'create'])->name('tagihan.create');
    Route::post('/tagihan', [TagihanController::class, 'store'])->name('tagihan.store');
    Route::get('/tagihan/{id}', [TagihanController::class, 'show'])->name('tagihan.show');
    Route::get('/tagihan/{id}/edit', [TagihanController::class, 'edit'])->name('tagihan.edit');
    Route::put('/tagihan/{id}', [TagihanController::class, 'update'])->name('tagihan.update');
    Route::delete('/tagihan/{id}', [TagihanController::class, 'destroy'])->name('tagihan.destroy');
    Route::put('/tagihan/{tagihan}/detail/{detail}', [TagihanController::class, 'updateDetail'])->name('tagihan.update-detail');
    Route::delete('/tagihan/{tagihan}/detail/{detail}', [TagihanController::class, 'deleteDetail'])->name('tagihan.delete-detail');
    
    // Pembayaran Management
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create/{tagihan?}', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::get('/pembayaran/{id}/edit', [PembayaranController::class, 'edit'])->name('pembayaran.edit');
    Route::put('/pembayaran/{id}', [PembayaranController::class, 'update'])->name('pembayaran.update');
    Route::delete('/pembayaran/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');
    Route::post('/pembayaran/{id}/verifikasi', [PembayaranController::class, 'verifikasi'])->name('pembayaran.verifikasi');
    Route::get('/pembayaran/{id}/download-bukti', [PembayaranController::class, 'downloadBukti'])->name('pembayaran.download-bukti');
    
    // Kartu Pembayaran Management
    Route::get('/kartu-pembayaran', [KartuPembayaranController::class, 'index'])->name('kartu-pembayaran.index');
    Route::get('/kartu-pembayaran/create', [KartuPembayaranController::class, 'create'])->name('kartu-pembayaran.create');
    Route::post('/kartu-pembayaran', [KartuPembayaranController::class, 'store'])->name('kartu-pembayaran.store');
    Route::get('/kartu-pembayaran/{id}', [KartuPembayaranController::class, 'show'])->name('kartu-pembayaran.show');
    Route::get('/kartu-pembayaran/{id}/edit', [KartuPembayaranController::class, 'edit'])->name('kartu-pembayaran.edit');
    Route::put('/kartu-pembayaran/{id}', [KartuPembayaranController::class, 'update'])->name('kartu-pembayaran.update');
    Route::delete('/kartu-pembayaran/{id}', [KartuPembayaranController::class, 'destroy'])->name('kartu-pembayaran.destroy');
    Route::get('/kartu-pembayaran/generate/massal', [KartuPembayaranController::class, 'showGenerateMassal'])->name('kartu-pembayaran.generate-massal');
    Route::post('/kartu-pembayaran/generate/massal', [KartuPembayaranController::class, 'generateMassal'])->name('kartu-pembayaran.store-massal');
    Route::get('/kartu-pembayaran/{id}/cetak', [KartuPembayaranController::class, 'cetak'])->name('kartu-pembayaran.cetak');
});

require __DIR__.'/auth.php';
