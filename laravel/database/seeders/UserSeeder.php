<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $createSampleUsers = User::count() === 0;

        User::updateOrCreate(
            ['email' => 'admin@sibakita.test'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kader@sibakita.test'],
            [
                'name' => 'Kader Posyandu Melati',
                'email_verified_at' => now(),
                'password' => Hash::make('kader123'),
                'role' => 'kader',
            ]
        );

        User::updateOrCreate(
            ['email' => 'petugas@sibakita.test'],
            [
                'name' => 'dr. Siti Rahmawati, Amd. Keb',
                'email_verified_at' => now(),
                'password' => Hash::make('puskesmas123'),
                'role' => 'petugas',
            ]
        );

        User::updateOrCreate(
            ['email' => 'warga@sibakita.test'],
            [
                'name' => 'Budi Santoso',
                'email_verified_at' => now(),
                'password' => Hash::make('warga123'),
                'role' => 'warga',
            ]
        );

        if ($createSampleUsers) {
            User::factory(6)->create();
        }
    }
}
