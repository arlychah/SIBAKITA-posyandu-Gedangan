<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan');
    }

    public function balita(Request $r)
    {
        $start = $r->input('start', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end = $r->input('end', Carbon::now()->format('Y-m-d'));

        $query = PemeriksaanBalita::with(['balita.warga'])
            ->whereBetween('tanggal', [$start, $end]);

        $data = $query->orderBy('tanggal', 'desc')->get();
        $total = $data->count();

        $underweight = $data->filter(function ($d) {
            return str_contains($d->status_gizi_bbu ?? '', 'Kurang') || str_contains($d->status_gizi_bbu ?? '', 'Buruk');
        })->count();

        $stunted = $data->filter(function ($d) {
            return str_contains($d->status_gizi_tbu ?? '', 'Pendek')
                || str_contains($d->status_gizi_tbu ?? '', 'Buruk')
                || str_contains($d->status_gizi_tbu ?? '', 'Kurang');
        })->count();

        $wasted = $data->filter(function ($d) {
            return str_contains($d->status_gizi_bbtb ?? '', 'Kurang') || str_contains($d->status_gizi_bbtb ?? '', 'Buruk');
        })->count();

        return view('laporan.balita', compact('data', 'total', 'stunted', 'underweight', 'wasted', 'start', 'end'));
    }

    public function ibuHamil(Request $r)
    {
        $start = $r->input('start', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end = $r->input('end', Carbon::now()->format('Y-m-d'));

        $query = PemeriksaanIbuHamil::with(['ibu_hamil.warga'])
            ->whereBetween('tanggal', [$start, $end]);

        $data = $query->orderBy('tanggal', 'desc')->get();
        $total = $data->count();

        $risikoKek = $data->filter(function ($d) {
            return $d->lila && $d->lila < 23.5;
        })->count();

        $tdTinggi = $data->filter(function ($d) {
            return $d->tekanan_darah_sistolik && $d->tekanan_darah_sistolik >= 140;
        })->count();

        $ttdDiberikan = $data->filter(function ($d) {
            return $d->ttd_diberikan === true;
        })->count();

        return view('laporan.ibu_hamil', compact('data', 'total', 'risikoKek', 'tdTinggi', 'ttdDiberikan', 'start', 'end'));
    }

    public function lansia(Request $r)
    {
        $start = $r->input('start', Carbon::now()->subDays(30)->format('Y-m-d'));
        $end = $r->input('end', Carbon::now()->format('Y-m-d'));

        $query = PemeriksaanLansia::with(['lansia.warga'])
            ->whereBetween('tanggal', [$start, $end]);

        $data = $query->orderBy('tanggal', 'desc')->get();
        $total = $data->count();

        $hipertensi = $data->filter(function ($d) {
            return ($d->tekanan_darah_sistolik && $d->tekanan_darah_sistolik >= 140)
                || ($d->tekanan_darah_diastolik && $d->tekanan_darah_diastolik >= 90);
        })->count();

        $diabetes = $data->filter(function ($d) {
            return ($d->gula_darah_puasa && $d->gula_darah_puasa >= 126)
                || ($d->gula_darah_sewaktu && $d->gula_darah_sewaktu >= 200);
        })->count();

        return view('laporan.lansia', compact('data', 'total', 'hipertensi', 'diabetes', 'start', 'end'));
    }
}
