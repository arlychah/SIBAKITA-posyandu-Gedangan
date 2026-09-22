<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Balita;
use App\Models\Warga;

class BalitaSeeder extends Seeder
{
    public function run(): void
    {
        // Buat data warga terlebih dahulu
        $warga = Warga::factory()->create();

        // Setelah warga ada, buat balita
        Balita::create([
            'warga_id' => $warga->id,
            'nama_ibu' => 'Siti Aminah',
        ]);

        // Buat 10 data balita tambahan
        Balita::factory(10)->create();
    }
}