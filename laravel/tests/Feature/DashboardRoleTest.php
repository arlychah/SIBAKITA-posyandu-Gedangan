<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_kader_can_log_in_and_open_their_dashboard(): void
    {
        $this->assertRoleCanOpenDashboard('kader', 'kader@test.local', 'dashboard.kader');
    }

    public function test_warga_can_log_in_and_open_their_dashboard(): void
    {
        $this->assertRoleCanOpenDashboard('warga', 'warga@test.local', 'dashboard.anggota');
    }

    public function test_petugas_can_log_in_and_open_their_dashboard(): void
    {
        $this->assertRoleCanOpenDashboard('petugas', 'petugas@test.local', 'dashboard.petugas');
    }

    public function test_demo_user_seeder_is_safe_to_run_more_than_once(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);

        $this->assertSame(4, User::whereIn('email', [
            'admin@sibakita.test',
            'kader@sibakita.test',
            'petugas@sibakita.test',
            'warga@sibakita.test',
        ])->count());
    }

    private function assertRoleCanOpenDashboard(string $role, string $email, string $dashboardRoute): void
    {
        $password = 'test-password';

        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);

        $this->post('/login', [
            'email' => $email,
            'password' => $password,
        ])->assertRedirect('/');

        $this->get('/')->assertRedirect(route($dashboardRoute));

        $this->get(route($dashboardRoute))
            ->assertOk()
            ->assertDontSee('@if(')
            ->assertDontSee('@endif')
            ->assertDontSee('{{ Auth::user()');
    }
}
