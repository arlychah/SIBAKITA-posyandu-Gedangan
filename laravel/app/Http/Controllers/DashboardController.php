<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalWarga = Warga::count();
        $totalBalita = Warga::where('kategori', 'Balita')->count();
        $pemeriksaanBalita = PemeriksaanBalita::count();
        $totalIbuHamil = Warga::where('kategori', 'Ibu Hamil')->count();
        $pemeriksaanIbuHamil = PemeriksaanIbuHamil::count();
        $totalLansia = Warga::where('kategori', 'Lansia')->count();
        $pemeriksaanLansia = PemeriksaanLansia::count();
        $latestWarga = Warga::with(['balita', 'ibu_hamil', 'lansia'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalWarga',
            'totalBalita',
            'pemeriksaanBalita',
            'totalIbuHamil',
            'pemeriksaanIbuHamil',
            'totalLansia',
            'pemeriksaanLansia',
            'latestWarga'
        ));
    }
}
