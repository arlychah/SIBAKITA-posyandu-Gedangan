<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lansia;
use App\Models\Warga;

class LansiaSeeder extends Seeder
{
    public function run()
    {
        $wargaLansia1 = Warga::where('nik', '3515120101010003')->first();
        if ($wargaLansia1) {
            Lansia::create([
                'warga_id' => $wargaLansia1->id,
            ]);
        }

        $wargaLansia2 = Warga::where('nik', '3515120101010004')->first();
        if ($wargaLansia2) {
            Lansia::create([
                'warga_id' => $wargaLansia2->id,
            ]);
        }

        $wargaLansiaRandom = Warga::where('kategori', 'lansia')
            ->whereNotIn('nik', ['3515120101010003', '3515120101010004'])
            ->limit(13)
            ->get();

        foreach ($wargaLansiaRandom as $warga) {
            if (!Lansia::where('warga_id', $warga->id)->exists()) {
                Lansia::factory()->create(['warga_id' => $warga->id]);
            }
        }

        $totalLansia = Warga::where('kategori', 'lansia')->count();
        $sudahAda = Lansia::count();
        $kurang = max(0, 15 - $sudahAda);
        if ($kurang > 0) {
            Lansia::factory($kurang)->create();
        }
    }
}
