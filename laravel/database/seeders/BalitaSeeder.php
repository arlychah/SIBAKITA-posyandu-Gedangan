<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Balita;
use App\Models\Warga;

class BalitaSeeder extends Seeder
{
    public function run()
    {
        $wargaBalita = Warga::where('nik', '3515120101010001')->first();
        if ($wargaBalita) {
            Balita::create([
                'warga_id' => $wargaBalita->id,
                'nama_ibu' => 'Ibu Ananda',
                'nama_ayah' => 'Ayah Ananda',
            ]);
        }

        $wargaBalitaRandom = Warga::where('kategori', 'balita')
            ->where('nik', '!=', '3515120101010001')
            ->limit(14)
            ->get();

        foreach ($wargaBalitaRandom as $warga) {
            if (!Balita::where('warga_id', $warga->id)->exists()) {
                Balita::factory()->create(['warga_id' => $warga->id]);
            }
        }

        $totalBalita = Warga::where('kategori', 'balita')->count();
        $sudahAda = Balita::count();
        $kurang = max(0, 15 - $sudahAda);
        if ($kurang > 0) {
            Balita::factory($kurang)->create();
        }
    }
}
