<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\PemeriksaanAuditLog;
use App\Models\PemeriksaanBalita;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WargaCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petugas = User::factory()->create(['role' => 'petugas']);
    }

    public function test_petugas_can_create_and_view_warga_with_category_profile(): void
    {
        $payload = $this->wargaPayload(['kategori' => 'Balita']);

        $this->actingAs($this->petugas)
            ->post(route('warga.store'), $payload)
            ->assertRedirect(route('warga.index'));

        $warga = Warga::where('nik', $payload['nik'])->firstOrFail();
        $this->assertDatabaseHas('balita', ['warga_id' => $warga->id]);
        $this->assertDatabaseMissing('ibu_hamil', ['warga_id' => $warga->id]);

        $this->get(route('warga.index', ['search' => $payload['nama_lengkap']]))
            ->assertOk()
            ->assertSee($payload['nama_lengkap']);
        $this->get(route('warga.show', $warga->id))
            ->assertOk()
            ->assertSee($payload['nik']);
    }

    public function test_petugas_can_update_warga_without_losing_same_category_profile(): void
    {
        $warga = Warga::factory()->balita()->create();
        $balita = Balita::create(['warga_id' => $warga->id, 'nama_ibu' => 'Ibu Lama']);
        $payload = $this->wargaPayload([
            'nik' => $warga->nik,
            'kategori' => 'Balita',
            'nama_ibu' => 'Ibu Baru',
            'nama_ayah' => 'Ayah Baru',
        ]);

        $this->actingAs($this->petugas)
            ->post(route('warga.update', $warga->id), $payload)
            ->assertRedirect(route('warga.show', $warga->id));

        $this->assertDatabaseHas('warga', ['id' => $warga->id, 'nama_lengkap' => $payload['nama_lengkap']]);
        $this->assertDatabaseHas('balita', [
            'id' => $balita->id,
            'nama_ibu' => 'Ibu Baru',
            'nama_ayah' => 'Ayah Baru',
        ]);
    }

    public function test_warga_category_cannot_change_when_medical_history_exists(): void
    {
        $warga = Warga::factory()->balita()->create();
        $balita = Balita::create(['warga_id' => $warga->id]);
        PemeriksaanBalita::create([
            'balita_id' => $balita->id,
            'tanggal' => today()->toDateString(),
            'berat_badan' => 12.0,
            'tinggi_badan' => 90.0,
        ]);

        $this->actingAs($this->petugas)
            ->from(route('warga.edit', $warga->id))
            ->post(route('warga.update', $warga->id), $this->wargaPayload([
                'nik' => $warga->nik,
                'kategori' => 'Ibu Hamil',
                'jenis_kelamin' => 'Perempuan',
            ]))
            ->assertRedirect(route('warga.edit', $warga->id))
            ->assertSessionHasErrors('kategori');

        $this->assertDatabaseHas('warga', ['id' => $warga->id, 'kategori' => 'Balita']);
        $this->assertDatabaseHas('pemeriksaan_balita', ['balita_id' => $balita->id]);
        $this->assertDatabaseMissing('ibu_hamil', ['warga_id' => $warga->id]);
    }

    public function test_petugas_can_delete_warga_and_its_medical_and_audit_history(): void
    {
        $warga = Warga::factory()->balita()->create();
        $balita = Balita::create(['warga_id' => $warga->id]);
        $record = PemeriksaanBalita::create([
            'balita_id' => $balita->id,
            'tanggal' => today()->toDateString(),
            'berat_badan' => 12.0,
            'tinggi_badan' => 90.0,
            'verification_status' => 'pending',
        ]);
        PemeriksaanAuditLog::create([
            'jenis_pemeriksaan' => 'balita',
            'pemeriksaan_id' => $record->id,
            'actor_id' => $this->petugas->id,
            'aksi' => 'submitted',
            'sesudah' => ['verification_status' => 'pending'],
        ]);

        $this->actingAs($this->petugas)
            ->post(route('warga.destroy', $warga->id))
            ->assertRedirect(route('warga.index'));

        $this->assertDatabaseMissing('warga', ['id' => $warga->id]);
        $this->assertDatabaseMissing('balita', ['id' => $balita->id]);
        $this->assertDatabaseMissing('pemeriksaan_balita', ['id' => $record->id]);
        $this->assertDatabaseMissing('pemeriksaan_audit_logs', ['pemeriksaan_id' => $record->id]);
    }

    public function test_invalid_category_and_future_birth_date_are_rejected(): void
    {
        $payload = $this->wargaPayload([
            'kategori' => 'Lainnya',
            'tanggal_lahir' => today()->addDay()->toDateString(),
        ]);

        $this->actingAs($this->petugas)
            ->from(route('warga.create'))
            ->post(route('warga.store'), $payload)
            ->assertRedirect(route('warga.create'))
            ->assertSessionHasErrors(['kategori', 'tanggal_lahir']);

        $this->assertDatabaseMissing('warga', ['nik' => $payload['nik']]);
    }

    public function test_kader_can_read_warga_but_cannot_create_or_update_them(): void
    {
        $kader = User::factory()->create(['role' => 'kader']);
        $warga = Warga::factory()->create();

        $this->actingAs($kader)->get(route('warga.index'))->assertOk();
        $this->post(route('warga.store'), $this->wargaPayload())->assertForbidden();
        $this->post(route('warga.update', $warga->id), $this->wargaPayload(['nik' => $warga->nik]))->assertForbidden();
    }

    private function wargaPayload(array $overrides = []): array
    {
        return array_replace([
            'nik' => '1234567890123456',
            'nama_lengkap' => 'Nama Warga Uji',
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Gedangan',
            'tanggal_lahir' => '2020-01-01',
            'kategori' => 'Balita',
            'whatsapp' => '6281234567890',
            'puskesmas' => 'Puskesmas Gedangan',
            'pustu' => 'Posyandu Melati',
            'alamat' => 'Desa Gedangan',
            'nama_ibu' => 'Ibu Warga',
            'nama_ayah' => 'Ayah Warga',
        ], $overrides);
    }
}
