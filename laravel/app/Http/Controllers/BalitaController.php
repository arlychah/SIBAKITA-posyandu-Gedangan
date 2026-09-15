<?php

namespace App\Http\Controllers;

use App\Helpers\StatusGizi;
use App\Models\Balita;
use App\Models\PemeriksaanBalita;
use Illuminate\Http\Request;

class BalitaController extends Controller
{
    public function periksa(Request $r, $id)
    {
        $balita = Balita::with(['warga', 'riwayat'])->findOrFail($id);
        $warga = $balita->warga;
        $umurTahun = $warga->umur_tahun;
        $umurBulan = $warga->umur_bulan;

        if ($r->isMethod('POST')) {
            $r->validate([
                'berat_badan' => 'required|numeric',
                'tinggi_badan' => 'required|numeric',
            ]);

            $tanggal = $r->tanggal ? date('Y-m-d', strtotime($r->tanggal)) : date('Y-m-d');
            $beratBadan = (float)$r->berat_badan;
            $tinggiBadan = (float)$r->tinggi_badan;
            $jk = $warga->jenis_kelamin;
            $totalBulan = $umurBulan;

            $statusBBU = StatusGizi::bbu($beratBadan, $totalBulan, $jk);
            $statusTBU = StatusGizi::tbu($tinggiBadan, $totalBulan, $jk);
            $statusBBTB = StatusGizi::bbtb($beratBadan, $tinggiBadan, $totalBulan, $jk);

            PemeriksaanBalita::create([
                'balita_id' => $id,
                'tanggal' => $tanggal,
                'berat_badan' => $beratBadan,
                'tinggi_badan' => $tinggiBadan,
                'lingkar_kepala' => $r->lingkar_kepala ? (float)$r->lingkar_kepala : null,
                'status_gizi_bbu' => $statusBBU,
                'status_gizi_tbu' => $statusTBU,
                'status_gizi_bbtb' => $statusBBTB,
                'asi_eksklusif' => $r->asi_eksklusif ?? '',
                'imunisasi_bcg' => $r->has('imunisasi_bcg'),
                'imunisasi_dpt1' => $r->has('imunisasi_dpt1'),
                'imunisasi_dpt2' => $r->has('imunisasi_dpt2'),
                'imunisasi_dpt3' => $r->has('imunisasi_dpt3'),
                'imunisasi_polio1' => $r->has('imunisasi_polio1'),
                'imunisasi_polio2' => $r->has('imunisasi_polio2'),
                'imunisasi_polio3' => $r->has('imunisasi_polio3'),
                'imunisasi_campak' => $r->has('imunisasi_campak'),
                'vitamin_a_bulan_ke' => $r->vitamin_a_bulan_ke ? (int)$r->vitamin_a_bulan_ke : null,
                'pmt_diterima' => $r->pmt_diterima ?? '',
                'catatan' => $r->catatan ?? '',
            ]);

            return back()->with('success', "Pemeriksaan balita berhasil dicatat! Status gizi: {$statusBBTB}");
        }

        $riwayat = PemeriksaanBalita::where('balita_id', $id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('balita.periksa', compact('balita', 'umurTahun', 'umurBulan', 'riwayat'));
    }
}
