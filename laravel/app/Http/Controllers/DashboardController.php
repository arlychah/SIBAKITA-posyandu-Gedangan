<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'warga') {
            return redirect()->route('dashboard.anggota');
        }
        if ($user->role === 'kader') {
            return redirect()->route('dashboard.kader');
        }
        if ($user->role === 'petugas') {
            return redirect()->route('dashboard.petugas');
        }

        return view('dashboard.admin', [
            'totalWarga' => Warga::count(),
            'totalBalita' => Balita::count(),
            'totalIbuHamil' => IbuHamil::count(),
            'totalLansia' => Lansia::count(),
            'menungguVerifikasi' => PemeriksaanBalita::where('verification_status', 'pending')->count()
                + PemeriksaanIbuHamil::where('verification_status', 'pending')->count()
                + PemeriksaanLansia::where('verification_status', 'pending')->count(),
        ]);
    }
}
