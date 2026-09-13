<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Warga;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/warga', function () {
    return Warga::with(['balita', 'ibu_hamil', 'lansia'])->latest()->paginate(20);
});

Route::get('/warga/{id}', function ($id) {
    return Warga::with(['balita.riwayat', 'ibu_hamil.riwayat', 'lansia.riwayat'])->findOrFail($id);
});

Route::get('/balita', function () {
    return Balita::with('warga')->latest()->paginate(20);
});

Route::get('/ibu-hamil', function () {
    return IbuHamil::with('warga')->latest()->paginate(20);
});

Route::get('/lansia', function () {
    return Lansia::with('warga')->latest()->paginate(20);
});

Route::get('/dashboard-stats', function () {
    return [
        'total_warga' => Warga::count(),
        'total_balita' => Warga::where('kategori', 'Balita')->count(),
        'total_ibu_hamil' => Warga::where('kategori', 'Ibu Hamil')->count(),
        'total_lansia' => Warga::where('kategori', 'Lansia')->count(),
        'pemeriksaan_balita_total' => PemeriksaanBalita::count(),
        'pemeriksaan_ibu_total' => PemeriksaanIbuHamil::count(),
        'pemeriksaan_lansia_total' => PemeriksaanLansia::count(),
    ];
});
