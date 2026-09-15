<?php

namespace App\Http\Controllers;

use App\Models\Lansia;
use App\Models\PemeriksaanLansia;
use Illuminate\Http\Request;

class LansiaController extends Controller
{
    public function periksa(Request $r, $id)
    {
        $lansia = Lansia::with(['warga', 'riwayat'])->findOrFail($id);

        if ($r->isMethod('POST')) {
            $tanggal = $r->tanggal ? date('Y-m-d', strtotime($r->tanggal)) : date('Y-m-d');

            PemeriksaanLansia::create([
                'lansia_id' => $id,
                'tanggal' => $tanggal,
                'berat_badan' => $r->berat_badan ? (float)$r->berat_badan : null,
                'tinggi_badan' => $r->tinggi_badan ? (float)$r->tinggi_badan : null,
                'tekanan_darah_sistolik' => $r->tekanan_darah_sistolik ? (int)$r->tekanan_darah_sistolik : null,
                'tekanan_darah_diastolik' => $r->tekanan_darah_diastolik ? (int)$r->tekanan_darah_diastolik : null,
                'gula_darah_puasa' => $r->gula_darah_puasa ? (float)$r->gula_darah_puasa : null,
                'gula_darah_sewaktu' => $r->gula_darah_sewaktu ? (float)$r->gula_darah_sewaktu : null,
                'kolesterol' => $r->kolesterol ? (float)$r->kolesterol : null,
                'asam_urat' => $r->asam_urat ? (float)$r->asam_urat : null,
                'skrining_jiwa' => $r->skrining_jiwa ?? '',
                'penglihatan' => $r->penglihatan ?? '',
                'pendengaran' => $r->pendengaran ?? '',
                'catatan' => $r->catatan ?? '',
            ]);

            return back()->with('success', 'Pemeriksaan lansia berhasil dicatat!');
        }

        $riwayat = PemeriksaanLansia::where('lansia_id', $id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('lansia.periksa', compact('lansia', 'riwayat'));
    }
}
