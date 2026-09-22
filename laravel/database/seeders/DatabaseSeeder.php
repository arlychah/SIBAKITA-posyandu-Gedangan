<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            WargaSeeder::class,
            BalitaSeeder::class,
            IbuHamilSeeder::class,
            LansiaSeeder::class,
            PemeriksaanSeeder::class,
        ]);
    }
}
