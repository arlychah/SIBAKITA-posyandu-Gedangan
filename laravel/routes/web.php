<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IbuHamilController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\LansiaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MemberAccountController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'role:admin,kader,warga,petugas'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});
Route::get('/dashboard/anggota', [\App\Http\Controllers\MemberDashboardController::class, 'index'])
    ->middleware(['auth', 'role:warga'])->name('dashboard.anggota');
Route::get('/dashboard/kader', [\App\Http\Controllers\KaderDashboardController::class, 'index'])
    ->middleware(['auth', 'role:kader'])->name('dashboard.kader');
Route::get('/dashboard/petugas', [\App\Http\Controllers\PetugasDashboardController::class, 'index'])
    ->middleware(['auth', 'role:petugas'])->name('dashboard.petugas');

Route::middleware(['auth', 'role:kader,petugas'])->group(function () {
    Route::get('/warga', [WargaController::class, 'index'])->name('warga.index');
    Route::get('/warga/{id}', [WargaController::class, 'show'])->whereNumber('id')->name('warga.show');
});

Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/warga/tambah', [WargaController::class, 'create'])->name('warga.create');
    Route::post('/warga/tambah', [WargaController::class, 'store'])->name('warga.store');
    Route::get('/warga/{id}/edit', [WargaController::class, 'edit'])->whereNumber('id')->name('warga.edit');
    Route::post('/warga/{id}/edit', [WargaController::class, 'update'])->whereNumber('id')->name('warga.update');
    Route::post('/warga/{id}/hapus', [WargaController::class, 'destroy'])->whereNumber('id')->name('warga.destroy');
});

Route::middleware(['auth', 'role:kader'])->group(function () {
    Route::get('/balita/periksa/{id}', [BalitaController::class, 'periksa'])->whereNumber('id')->name('balita.periksa');
    Route::post('/balita/periksa/{id}', [BalitaController::class, 'periksa'])->whereNumber('id')->name('balita.periksa.store');
    Route::get('/balita/periksa/{id}/koreksi/{pemeriksaan}', [BalitaController::class, 'koreksi'])->whereNumber('id')->whereNumber('pemeriksaan')->name('balita.periksa.koreksi');
    Route::post('/balita/periksa/{id}/koreksi/{pemeriksaan}', [BalitaController::class, 'koreksi'])->whereNumber('id')->whereNumber('pemeriksaan')->name('balita.periksa.koreksi.update');

    Route::get('/ibu_hamil/periksa/{id}', [IbuHamilController::class, 'periksa'])->whereNumber('id')->name('ibu_hamil.periksa');
    Route::post('/ibu_hamil/periksa/{id}', [IbuHamilController::class, 'periksa'])->whereNumber('id')->name('ibu_hamil.periksa.store');
    Route::get('/ibu_hamil/periksa/{id}/koreksi/{pemeriksaan}', [IbuHamilController::class, 'koreksi'])->whereNumber('id')->whereNumber('pemeriksaan')->name('ibu_hamil.periksa.koreksi');
    Route::post('/ibu_hamil/periksa/{id}/koreksi/{pemeriksaan}', [IbuHamilController::class, 'koreksi'])->whereNumber('id')->whereNumber('pemeriksaan')->name('ibu_hamil.periksa.koreksi.update');

    Route::get('/lansia/periksa/{id}', [LansiaController::class, 'periksa'])->whereNumber('id')->name('lansia.periksa');
    Route::post('/lansia/periksa/{id}', [LansiaController::class, 'periksa'])->whereNumber('id')->name('lansia.periksa.store');
    Route::get('/lansia/periksa/{id}/koreksi/{pemeriksaan}', [LansiaController::class, 'koreksi'])->whereNumber('id')->whereNumber('pemeriksaan')->name('lansia.periksa.koreksi');
    Route::post('/lansia/periksa/{id}/koreksi/{pemeriksaan}', [LansiaController::class, 'koreksi'])->whereNumber('id')->whereNumber('pemeriksaan')->name('lansia.periksa.koreksi.update');
});

Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/verifikasi', [VerificationController::class, 'index'])->name('verifikasi.index');
    Route::get('/verifikasi/{type}/{id}', [VerificationController::class, 'show'])->whereNumber('id')->name('verifikasi.show');
    Route::post('/verifikasi/{type}/{id}/verifikasi', [VerificationController::class, 'approve'])->whereNumber('id')->name('verifikasi.approve');
    Route::post('/verifikasi/{type}/{id}/kembalikan', [VerificationController::class, 'returnForCorrection'])->whereNumber('id')->name('verifikasi.return');
    Route::get('/verifikasi/{type}/{id}/edit', [VerificationController::class, 'edit'])->whereNumber('id')->name('verifikasi.edit');
    Route::post('/verifikasi/{type}/{id}/edit', [VerificationController::class, 'update'])->whereNumber('id')->name('verifikasi.update');

    Route::post('/jadwal-posyandu', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::get('/jadwal-posyandu/tambah', [JadwalController::class, 'create'])->name('jadwal.create');
    Route::get('/jadwal-posyandu/{jadwal}/edit', [JadwalController::class, 'edit'])->name('jadwal.edit');
    Route::post('/jadwal-posyandu/{jadwal}/edit', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::post('/jadwal-posyandu/{jadwal}/batal', [JadwalController::class, 'cancel'])->name('jadwal.cancel');
});

Route::middleware(['auth', 'role:petugas,admin'])->group(function () {
    Route::get('/akun-anggota', [MemberAccountController::class, 'index'])->name('akun-anggota.index');
    Route::post('/akun-anggota', [MemberAccountController::class, 'store'])->name('akun-anggota.store');
    Route::post('/akun-anggota/{user}/tautkan', [MemberAccountController::class, 'link'])->whereNumber('user')->name('akun-anggota.link');
});

Route::middleware(['auth', 'role:warga,petugas'])->group(function () {
    Route::get('/jadwal-posyandu', [JadwalController::class, 'index'])->name('jadwal.index');
});

Route::middleware(['auth', 'role:petugas'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
    Route::get('/balita', [LaporanController::class, 'balita'])->name('balita');
    Route::get('/ibu_hamil', [LaporanController::class, 'ibuHamil'])->name('ibu-hamil');
    Route::get('/lansia', [LaporanController::class, 'lansia'])->name('lansia');
});
