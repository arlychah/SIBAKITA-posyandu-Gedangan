<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sibakita.test',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kader Posyandu Melati',
            'email' => 'kader@sibakita.test',
            'email_verified_at' => now(),
            'password' => Hash::make('kader123'),
            'role' => 'kader',
        ]);

        User::create([
            'name' => 'dr. Siti Rahmawati, Amd. Keb',
            'email' => 'petugas@sibakita.test',
            'email_verified_at' => now(),
            'password' => Hash::make('puskesmas123'),
            'role' => 'petugas',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'warga@sibakita.test',
            'email_verified_at' => now(),
            'password' => Hash::make('warga123'),
            'role' => 'warga',
        ]);

        User::factory(6)->create();
    }
}
