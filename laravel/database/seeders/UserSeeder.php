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
            'password' => Hash::make('admin@sibakita2026'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kader Posyandu Melati',
            'email' => 'kader@sibakita.test',
            'email_verified_at' => now(),
            'password' => Hash::make('kader@posyandu2026'),
            'role' => 'kader',
        ]);

        User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@sibakita.test',
            'email_verified_at' => now(),
            'password' => Hash::make('warga@sibakita2026'),
            'role' => 'pengunjung',
        ]);

        User::factory(7)->create();
    }
}
