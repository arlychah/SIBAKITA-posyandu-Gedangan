<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class LaporanController extends Controller
{
    private const PAGE_SIZE = 100;

    public function index()
    {
        return view('laporan.index');
    }

    public function balita(Request $request)
    {
        [$start, $end] = $this->dateRange($request);
        $query = PemeriksaanBalita::with(['balita.warga'])
            ->where('verification_status', 'verified')
            ->whereBetween('tanggal', [$start, $end]);

        $total = (clone $query)->count();
        $underweight = (clone $query)->where(function ($query) {
            $query->where('status_gizi_bbu', 'LIKE', '%Kurang%')
                ->orWhere('status_gizi_bbu', 'LIKE', '%Buruk%');
        })->count();
        $stunted = (clone $query)->where(function ($query) {
            $query->where('status_gizi_tbu', 'LIKE', '%Pendek%')
                ->orWhere('status_gizi_tbu', 'LIKE', '%Buruk%')
                ->orWhere('status_gizi_tbu', 'LIKE', '%Kurang%');
        })->count();
        $wasted = (clone $query)->where(function ($query) {
            $query->where('status_gizi_bbtb', 'LIKE', '%Kurang%')
                ->orWhere('status_gizi_bbtb', 'LIKE', '%Buruk%');
        })->count();
        $data = (clone $query)->orderByDesc('tanggal')->paginate(self::PAGE_SIZE)->withQueryString();

        return view('laporan.balita', compact('data', 'total', 'stunted', 'underweight', 'wasted', 'start', 'end'));
    }

    public function ibuHamil(Request $request)
    {
        [$start, $end] = $this->dateRange($request);
        $query = PemeriksaanIbuHamil::with(['ibu_hamil.warga'])
            ->where('verification_status', 'verified')
            ->whereBetween('tanggal', [$start, $end]);

        $total = (clone $query)->count();
        $risikoKek = (clone $query)->whereNotNull('lila')->where('lila', '<', 23.5)->count();
        $tdTinggi = (clone $query)->where('tekanan_darah_sistolik', '>=', 140)->count();
        $ttdDiberikan = (clone $query)->where('ttd_diberikan', true)->count();
        $data = (clone $query)->orderByDesc('tanggal')->paginate(self::PAGE_SIZE)->withQueryString();

        return view('laporan.ibu_hamil', compact(
            'data', 'total', 'risikoKek', 'tdTinggi', 'ttdDiberikan', 'start', 'end'
        ));
    }

    public function lansia(Request $request)
    {
        [$start, $end] = $this->dateRange($request);
        $query = PemeriksaanLansia::with(['lansia.warga'])
            ->where('verification_status', 'verified')
            ->whereBetween('tanggal', [$start, $end]);

        $total = (clone $query)->count();
        $hipertensi = (clone $query)->where(function ($query) {
            $query->where('tekanan_darah_sistolik', '>=', 140)
                ->orWhere('tekanan_darah_diastolik', '>=', 90);
        })->count();
        $diabetes = (clone $query)->where(function ($query) {
            $query->where('gula_darah_puasa', '>=', 126)
                ->orWhere('gula_darah_sewaktu', '>=', 200);
        })->count();
        $data = (clone $query)->orderByDesc('tanggal')->paginate(self::PAGE_SIZE)->withQueryString();

        return view('laporan.lansia', compact('data', 'total', 'hipertensi', 'diabetes', 'start', 'end'));
    }

    private function dateRange(Request $request): array
    {
        $filters = $request->validate([
            'start' => ['nullable', 'date_format:Y-m-d'],
            'end' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $start = $filters['start'] ?? Carbon::today()->subDays(30)->toDateString();
        $end = $filters['end'] ?? Carbon::today()->toDateString();

        if ($end < $start) {
            throw ValidationException::withMessages([
                'end' => 'Tanggal akhir harus sama dengan atau setelah tanggal awal.',
            ]);
        }

        return [$start, $end];
    }
}
