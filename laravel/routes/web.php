<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\BalitaController;
use App\Http\Controllers\IbuHamilController;
use App\Http\Controllers\LansiaController;
use App\Http\Controllers\LaporanController;

Route::get('/', [DashboardController::class, 'index']);

Route::get('/warga', [WargaController::class, 'index']);
Route::get('/warga/tambah', [WargaController::class, 'create']);
Route::post('/warga/tambah', [WargaController::class, 'store']);
Route::get('/warga/{id}', [WargaController::class, 'show']);
Route::get('/warga/{id}/edit', [WargaController::class, 'edit']);
Route::post('/warga/{id}/edit', [WargaController::class, 'update']);
Route::post('/warga/{id}/hapus', [WargaController::class, 'destroy']);

Route::get('/balita/periksa/{id}', [BalitaController::class, 'periksa']);
Route::post('/balita/periksa/{id}', [BalitaController::class, 'periksa']);

Route::get('/ibu_hamil/periksa/{id}', [IbuHamilController::class, 'periksa']);
Route::post('/ibu_hamil/periksa/{id}', [IbuHamilController::class, 'periksa']);

Route::get('/lansia/periksa/{id}', [LansiaController::class, 'periksa']);
Route::post('/lansia/periksa/{id}', [LansiaController::class, 'periksa']);

Route::get('/laporan', [LaporanController::class, 'index']);
Route::get('/laporan/balita', [LaporanController::class, 'balita']);
Route::get('/laporan/ibu_hamil', [LaporanController::class, 'ibuHamil']);
Route::get('/laporan/lansia', [LaporanController::class, 'lansia']);
