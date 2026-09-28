<?php

namespace App\Http\Controllers;

use App\Models\JadwalPosyandu;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;

class PetugasDashboardController extends Controller
{
    public function index()
    {
        $menungguVerifikasi = PemeriksaanBalita::whereIn('verification_status', ['pending', 'legacy_review'])->count()
            + PemeriksaanIbuHamil::whereIn('verification_status', ['pending', 'legacy_review'])->count()
            + PemeriksaanLansia::whereIn('verification_status', ['pending', 'legacy_review'])->count();

        $perluPerbaikan = PemeriksaanBalita::where('verification_status', 'needs_revision')->count()
            + PemeriksaanIbuHamil::where('verification_status', 'needs_revision')->count()
            + PemeriksaanLansia::where('verification_status', 'needs_revision')->count();

        $jadwalMendatang = JadwalPosyandu::scheduled()->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')->orderBy('waktu_mulai')->limit(5)->get();

        return view('dashboard.petugas', compact('menungguVerifikasi', 'perluPerbaikan', 'jadwalMendatang'));
    }
}
