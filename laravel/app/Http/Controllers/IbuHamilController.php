<?php

namespace App\Http\Controllers;

use App\Models\IbuHamil;
use App\Models\PemeriksaanIbuHamil;
use Illuminate\Http\Request;

class IbuHamilController extends Controller
{
    public function periksa(Request $r, $id)
    {
        $ibu = IbuHamil::with(['warga', 'riwayat'])->findOrFail($id);

        if ($r->isMethod('POST')) {
            $tanggal = $r->tanggal ? date('Y-m-d', strtotime($r->tanggal)) : date('Y-m-d');
            $ttdDiberikan = $r->has('ttd_diberikan') ? true : false;

            PemeriksaanIbuHamil::create([
                'ibu_hamil_id' => $id,
                'tanggal' => $tanggal,
                'kehamilan_ke' => $r->kehamilan_ke ? (int)$r->kehamilan_ke : null,
                'usia_kehamilan' => $r->usia_kehamilan ? (int)$r->usia_kehamilan : null,
                'berat_badan' => $r->berat_badan ? (float)$r->berat_badan : null,
                'tekanan_darah_sistolik' => $r->tekanan_darah_sistolik ? (int)$r->tekanan_darah_sistolik : null,
                'tekanan_darah_diastolik' => $r->tekanan_darah_diastolik ? (int)$r->tekanan_darah_diastolik : null,
                'lila' => $r->lila ? (float)$r->lila : null,
                'tinggi_fundus' => $r->tinggi_fundus ? (float)$r->tinggi_fundus : null,
                'detak_jantung_janin' => $r->detak_jantung_janin ? (int)$r->detak_jantung_janin : null,
                'ttd_diberikan' => $ttdDiberikan,
                'jumlah_ttd' => $r->jumlah_ttd ? (int)$r->jumlah_ttd : null,
                'imunisasi_tt' => $r->imunisasi_tt ?? '',
                'catatan' => $r->catatan ?? '',
            ]);

            return back()->with('success', 'Pemeriksaan ibu hamil berhasil dicatat!');
        }

        $riwayat = PemeriksaanIbuHamil::where('ibu_hamil_id', $id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('ibu_hamil.periksa', compact('ibu', 'riwayat'));
    }
}
