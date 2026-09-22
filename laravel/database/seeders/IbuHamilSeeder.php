<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IbuHamil;
use App\Models\Warga;

class IbuHamilSeeder extends Seeder
{
    public function run()
    {
        $wargaIbu = Warga::where('nik', '3515120101010002')->first();
        if ($wargaIbu) {
            IbuHamil::create([
                'warga_id' => $wargaIbu->id,
                'nama_suami' => 'Bapak Siti Aisyah',
            ]);
        }

        $wargaIbuRandom = Warga::where('kategori', 'ibu_hamil')
            ->where('nik', '!=', '3515120101010002')
            ->limit(9)
            ->get();

        foreach ($wargaIbuRandom as $warga) {
            if (!IbuHamil::where('warga_id', $warga->id)->exists()) {
                IbuHamil::factory()->create(['warga_id' => $warga->id]);
            }
        }

        $totalIbu = Warga::where('kategori', 'ibu_hamil')->count();
        $sudahAda = IbuHamil::count();
        $kurang = max(0, 10 - $sudahAda);
        if ($kurang > 0) {
            IbuHamil::factory($kurang)->create();
        }
    }
}
