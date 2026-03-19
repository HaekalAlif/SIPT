<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\BendaharaController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KartuPembayaranController;
use App\Http\Controllers\MasterTagihanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
    Route::get('/kartu-pembayaran/pdf', [SantriController::class, 'downloadKartuPembayaranPdf'])->name('kartu-pembayaran.pdf');
    Route::get('/profil', [SantriController::class, 'profil'])->name('profil');
    Route::post('/profil', [SantriController::class, 'updateProfil'])->name('profil.update');
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
    
    // Master Data
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    
    // Master Tahun Ajaran
    Route::get('/tahun-ajaran', [AdminController::class, 'tahunAjaran'])->name('tahun-ajaran');
    Route::post('/tahun-ajaran', [AdminController::class, 'storeTahunAjaran'])->name('tahun-ajaran.store');
    Route::put('/tahun-ajaran/{id}', [AdminController::class, 'updateTahunAjaran'])->name('tahun-ajaran.update');
    Route::delete('/tahun-ajaran/{id}', [AdminController::class, 'destroyTahunAjaran'])->name('tahun-ajaran.destroy');
    
    // Master Kategori Tagihan
    Route::get('/kategori-tagihan', [AdminController::class, 'kategoriTagihan'])->name('kategori-tagihan');
    Route::post('/kategori-tagihan', [AdminController::class, 'storeKategoriTagihan'])->name('kategori-tagihan.store');
    Route::put('/kategori-tagihan/{id}', [AdminController::class, 'updateKategoriTagihan'])->name('kategori-tagihan.update');
    Route::delete('/kategori-tagihan/{id}', [AdminController::class, 'destroyKategoriTagihan'])->name('kategori-tagihan.destroy');
    
    // Master Jenis Tagihan
    Route::get('/jenis-tagihan', [AdminController::class, 'jenisTagihan'])->name('jenis-tagihan');
    Route::post('/jenis-tagihan', [AdminController::class, 'storeJenisTagihan'])->name('jenis-tagihan.store');
    Route::put('/jenis-tagihan/{id}', [AdminController::class, 'updateJenisTagihan'])->name('jenis-tagihan.update');
    Route::delete('/jenis-tagihan/{id}', [AdminController::class, 'destroyJenisTagihan'])->name('jenis-tagihan.destroy');
    
    // Master Kartu Pembayaran
    Route::get('kartu-pembayaran/generate-massal', [KartuPembayaranController::class, 'showGenerateMassal'])->name('kartu-pembayaran.generate-massal');
    Route::post('kartu-pembayaran/generate-massal', [KartuPembayaranController::class, 'generateMassal'])->name('kartu-pembayaran.store-massal');
    Route::get('kartu-pembayaran/{id}/cetak', [KartuPembayaranController::class, 'cetak'])->name('kartu-pembayaran.cetak');
    Route::resource('kartu-pembayaran', KartuPembayaranController::class);
    
    // Verifikasi Pembayaran
    Route::get('/verifikasi-pembayaran', [AdminController::class, 'verifikasiPembayaran'])->name('verifikasi-pembayaran');
    Route::get('/verifikasi-pembayaran/{id}', [AdminController::class, 'showVerifikasi'])->name('verifikasi-pembayaran.show');
    Route::post('/verifikasi-pembayaran/{id}', [AdminController::class, 'prosesVerifikasi'])->name('verifikasi-pembayaran.process');
    
    // Laporan
    Route::get('/laporan/pemasukan', [AdminController::class, 'laporanPemasukan'])->name('laporan-pemasukan');
    Route::get('/laporan/tunggakan', [AdminController::class, 'laporanTunggakan'])->name('laporan-tunggakan');
    Route::get('/laporan/rekap', [AdminController::class, 'laporanRekap'])->name('laporan-rekap');
    Route::get('/laporan/rekap-kelas', [AdminController::class, 'laporanRekapKelas'])->name('laporan-rekap-kelas');
    Route::get('/laporan/rekap-kelas/pdf', [AdminController::class, 'downloadLaporanRekapKelasPdf'])->name('laporan-rekap-kelas.pdf');
    
    // Master Tagihan (per-tahun-ajaran config)
    Route::get('/master-tagihan', [MasterTagihanController::class, 'index'])->name('master-tagihan');
    Route::put('/master-tagihan', [MasterTagihanController::class, 'update'])->name('master-tagihan.update');

    // Tagihan Management
    Route::get('/bayar-manual', [TagihanController::class, 'manualPaymentIndex'])->name('manual-payment.index');
    Route::get('/bayar-manual/santri/{user}', [TagihanController::class, 'manualPaymentShow'])->name('manual-payment.show');
    Route::post('/bayar-manual/santri/{user}', [TagihanController::class, 'storeBulkManualPayment'])->name('manual-payment.store-bulk');
    Route::resource('tagihan', TagihanController::class);
    Route::put('/tagihan/{tagihan}/detail/{detail}', [TagihanController::class, 'updateDetail'])->name('tagihan.update-detail');
    Route::delete('/tagihan/{tagihan}/detail/{detail}', [TagihanController::class, 'deleteDetail'])->name('tagihan.delete-detail');
    Route::post('/tagihan/{tagihan}/manual-payment', [TagihanController::class, 'storeManualPayment'])->name('tagihan.manual-payment');

    // Data Santri
    Route::get('/data-santri', function() {
        return redirect()->route('admin.users', ['role' => 'santri']);
    })->name('data-santri');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::get('/settings/metode-pembayaran', [AdminController::class, 'metodePembayaran'])->name('settings.metode-pembayaran');
    Route::post('/settings/metode-pembayaran', [AdminController::class, 'storeMetodePembayaran'])->name('settings.metode-pembayaran.store');
    Route::put('/settings/metode-pembayaran/{id}', [AdminController::class, 'updateMetodePembayaran'])->name('settings.metode-pembayaran.update');
    Route::delete('/settings/metode-pembayaran/{id}', [AdminController::class, 'destroyMetodePembayaran'])->name('settings.metode-pembayaran.destroy');
});

require __DIR__.'/auth.php';
