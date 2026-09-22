<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;

class PemeriksaanSeeder extends Seeder
{
    public function run()
    {
        $balitaPertama = Balita::first();
        if ($balitaPertama) {
            $tanggalAwal = now()->subMonths(6);
            for ($i = 0; $i < 6; $i++) {
                PemeriksaanBalita::factory()->create([
                    'balita_id' => $balitaPertama->id,
                    'tanggal' => $tanggalAwal->copy()->addMonths($i),
                ]);
            }
        }

        $ibuPertama = IbuHamil::first();
        if ($ibuPertama) {
            $tanggalAwal = now()->subMonths(8);
            for ($i = 0; $i < 8; $i++) {
                PemeriksaanIbuHamil::factory()->create([
                    'ibu_hamil_id' => $ibuPertama->id,
                    'tanggal' => $tanggalAwal->copy()->addWeeks($i * 4),
                ]);
            }
        }

        $lansiaPertama = Lansia::take(2)->get();
        foreach ($lansiaPertama as $lansia) {
            $tanggalAwal = now()->subYears(2);
            for ($i = 0; $i < 4; $i++) {
                PemeriksaanLansia::factory()->create([
                    'lansia_id' => $lansia->id,
                    'tanggal' => $tanggalAwal->copy()->addMonths($i * 6),
                ]);
            }
        }

        $sisaBalita = Balita::where('id', '!=', optional($balitaPertama)->id)->get();
        foreach ($sisaBalita as $balita) {
            $jumlah = rand(2, 8);
            for ($i = 0; $i < $jumlah; $i++) {
                PemeriksaanBalita::factory()->create([
                    'balita_id' => $balita->id,
                ]);
            }
        }

        $sisaIbu = IbuHamil::where('id', '!=', optional($ibuPertama)->id)->get();
        foreach ($sisaIbu as $ibu) {
            $jumlah = rand(3, 10);
            for ($i = 0; $i < $jumlah; $i++) {
                PemeriksaanIbuHamil::factory()->create([
                    'ibu_hamil_id' => $ibu->id,
                ]);
            }
        }

        $sisaLansia = Lansia::whereNotIn('id', $lansiaPertama->pluck('id'))->get();
        foreach ($sisaLansia as $lansia) {
            $jumlah = rand(2, 6);
            for ($i = 0; $i < $jumlah; $i++) {
                PemeriksaanLansia::factory()->create([
                    'lansia_id' => $lansia->id,
                ]);
            }
        }
    }
}
