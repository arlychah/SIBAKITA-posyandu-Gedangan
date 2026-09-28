<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warga;
use App\Support\UserRole;
use Illuminate\Database\Seeder;

class MemberProfileLinkSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'warga@sibakita.test')->where('role', UserRole::WARGA)->first();
        $warga = Warga::where('nik', '3515120101010001')->first();

        if ($user && $warga && !$user->warga_id && !$warga->user()->exists()) {
            $user->warga()->associate($warga);
            $user->save();
        }
    }
}
