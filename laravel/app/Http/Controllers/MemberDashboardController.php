<?php

namespace App\Http\Controllers;

use App\Models\JadwalPosyandu;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use Illuminate\Support\Facades\Auth;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $warga = Auth::user()->warga;
        if (!$warga) {
            return view('dashboard.anggota', [
                'warga' => null,
                'jadwalBerikutnya' => collect(),
                'hasil' => collect(),
            ]);
        }

        $kategori = $warga->kategoriJadwal();
        $jadwalBerikutnya = JadwalPosyandu::scheduled()
            ->forMemberCategory($kategori)
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->limit(5)
            ->get();

        $hasil = collect();
        if ($warga->balita) {
            $hasil = PemeriksaanBalita::where('balita_id', $warga->balita->id)
                ->where('verification_status', 'verified')->latest('tanggal')->limit(10)->get();
        } elseif ($warga->ibu_hamil) {
            $hasil = PemeriksaanIbuHamil::where('ibu_hamil_id', $warga->ibu_hamil->id)
                ->where('verification_status', 'verified')->latest('tanggal')->limit(10)->get();
        } elseif ($warga->lansia) {
            $hasil = PemeriksaanLansia::where('lansia_id', $warga->lansia->id)
                ->where('verification_status', 'verified')->latest('tanggal')->limit(10)->get();
        }

        return view('dashboard.anggota', compact('warga', 'jadwalBerikutnya', 'hasil'));
    }
}
