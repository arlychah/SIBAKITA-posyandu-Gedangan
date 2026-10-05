<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\JadwalPosyandu;
use App\Models\Lansia;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalAndLaporanTest extends TestCase
{
    use RefreshDatabase;

    private User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petugas = User::factory()->create(['role' => 'petugas']);
    }

    public function test_petugas_can_create_update_and_cancel_a_jadwal(): void
    {
        $this->actingAs($this->petugas)
            ->get(route('jadwal.create'))
            ->assertOk();

        $this->post(route('jadwal.store'), $this->jadwalPayload())
            ->assertRedirect(route('jadwal.index'));

        $jadwal = JadwalPosyandu::firstOrFail();
        $this->assertSame('scheduled', $jadwal->status);
        $this->assertSame($this->petugas->id, (int) $jadwal->created_by);

        $this->get(route('jadwal.edit', $jadwal))->assertOk();
        $this->post(route('jadwal.update', $jadwal), $this->jadwalPayload(['judul' => 'Posyandu Bulanan Revisi']))
            ->assertRedirect(route('jadwal.index'));
        $this->assertDatabaseHas('jadwal_posyandu', ['id' => $jadwal->id, 'judul' => 'Posyandu Bulanan Revisi']);

        $this->post(route('jadwal.cancel', $jadwal))->assertRedirect(route('jadwal.index'));
        $this->assertDatabaseHas('jadwal_posyandu', ['id' => $jadwal->id, 'status' => 'cancelled']);
    }

    public function test_cancelled_jadwal_cannot_be_edited_or_cancelled_again(): void
    {
        $jadwal = $this->createJadwal(['status' => 'cancelled']);

        $this->actingAs($this->petugas)
            ->get(route('jadwal.edit', $jadwal))
            ->assertStatus(409);
        $this->post(route('jadwal.update', $jadwal), $this->jadwalPayload())->assertStatus(409);
        $this->post(route('jadwal.cancel', $jadwal))->assertStatus(409);
    }

    public function test_warga_only_sees_upcoming_scheduled_events_for_their_category_or_everyone(): void
    {
        $profile = Warga::factory()->balita()->create();
        $warga = User::factory()->create(['role' => 'warga', 'warga_id' => $profile->id]);
        $matching = $this->createJadwal(['judul' => 'Jadwal Balita', 'kategori' => 'balita']);
        $everyone = $this->createJadwal(['judul' => 'Jadwal Umum', 'kategori' => 'semua']);
        $this->createJadwal(['judul' => 'Jadwal Lansia', 'kategori' => 'lansia']);
        $this->createJadwal(['judul' => 'Jadwal Lampau', 'tanggal' => today()->subDay()->toDateString()]);
        $this->createJadwal(['judul' => 'Jadwal Batal', 'status' => 'cancelled']);

        $this->actingAs($warga)
            ->get(route('jadwal.index'))
            ->assertOk()
            ->assertSee($matching->judul)
            ->assertSee($everyone->judul)
            ->assertDontSee('Jadwal Lansia')
            ->assertDontSee('Jadwal Lampau')
            ->assertDontSee('Jadwal Batal');
    }

    public function test_schedule_time_range_is_validated(): void
    {
        $this->actingAs($this->petugas)
            ->from(route('jadwal.create'))
            ->post(route('jadwal.store'), $this->jadwalPayload([
                'waktu_mulai' => '11:00',
                'waktu_selesai' => '10:00',
            ]))
            ->assertRedirect(route('jadwal.create'))
            ->assertSessionHasErrors('waktu_selesai');

        $this->assertDatabaseCount('jadwal_posyandu', 0);
    }

    public function test_reports_are_limited_to_verified_records_and_petugas_only(): void
    {
        $this->createVerifiedReports();
        $this->assertSame(1, PemeriksaanBalita::where('verification_status', 'verified')->count());
        $this->assertSame(1, PemeriksaanIbuHamil::where('verification_status', 'verified')->count());
        $this->assertSame(1, PemeriksaanLansia::where('verification_status', 'verified')->count());

        $this->actingAs($this->petugas)
            ->get(route('laporan.index'))
            ->assertOk()
            ->assertSee('Laporan Balita');
        $balitaReport = $this->get(route('laporan.balita'));
        $balitaReport->assertOk()
            ->assertViewHas('start', today()->subDays(30)->toDateString())
            ->assertViewHas('end', today()->toDateString())
            ->assertViewHas('total', 1)
            ->assertViewHas('data', function ($data) {
            return $data->count() === 1;
        });
        $balitaReport->assertSee('Nama Balita Uji');
        $this->get(route('laporan.ibu-hamil'))
            ->assertOk()
            ->assertSee('Nama Ibu Uji')
            ->assertSee('1');
        $this->get(route('laporan.lansia'))
            ->assertOk()
            ->assertSee('Nama Lansia Uji')
            ->assertSee('1 Data');

        $warga = User::factory()->create(['role' => 'warga']);
        $this->actingAs($warga)->get(route('laporan.index'))->assertForbidden();
        $this->get(route('laporan.balita'))->assertForbidden();
    }

    public function test_report_date_filters_reject_invalid_and_reversed_ranges(): void
    {
        $this->actingAs($this->petugas);

        foreach (['laporan.balita', 'laporan.ibu-hamil', 'laporan.lansia'] as $route) {
            $this->get(route($route, ['start' => 'not-a-date', 'end' => today()->toDateString()]))
                ->assertSessionHasErrors('start');
            $this->get(route($route, ['start' => today()->toDateString(), 'end' => today()->subDay()->toDateString()]))
                ->assertSessionHasErrors('end');
            $this->get(route($route, ['start' => ['bad'], 'end' => today()->toDateString()]))
                ->assertSessionHasErrors('start');
        }
    }

    private function createVerifiedReports(): void
    {
        $balitaWarga = Warga::factory()->balita()->create([
            'nama_lengkap' => 'Nama Balita Uji',
            'tanggal_lahir' => today()->subMonths(30)->toDateString(),
        ]);
        $balita = Balita::create(['warga_id' => $balitaWarga->id]);
        PemeriksaanBalita::create([
            'balita_id' => $balita->id,
            'tanggal' => today()->toDateString(),
            'berat_badan' => 12,
            'tinggi_badan' => 90,
            'status_gizi_bbu' => 'Berat Badan Normal',
            'status_gizi_tbu' => 'Tinggi Normal',
            'status_gizi_bbtb' => 'Gizi Normal',
            'verification_status' => 'verified',
        ]);
        PemeriksaanBalita::create([
            'balita_id' => $balita->id,
            'tanggal' => today()->toDateString(),
            'berat_badan' => 11,
            'tinggi_badan' => 89,
            'verification_status' => 'pending',
        ]);

        $ibuWarga = Warga::factory()->ibuHamil()->create(['nama_lengkap' => 'Nama Ibu Uji']);
        $ibu = IbuHamil::create(['warga_id' => $ibuWarga->id]);
        PemeriksaanIbuHamil::create([
            'ibu_hamil_id' => $ibu->id,
            'tanggal' => today()->toDateString(),
            'lila' => 22,
            'verification_status' => 'verified',
        ]);

        $lansiaWarga = Warga::factory()->lansia()->create(['nama_lengkap' => 'Nama Lansia Uji']);
        $lansia = Lansia::create(['warga_id' => $lansiaWarga->id]);
        PemeriksaanLansia::create([
            'lansia_id' => $lansia->id,
            'tanggal' => today()->toDateString(),
            'tekanan_darah_sistolik' => 145,
            'tekanan_darah_diastolik' => 92,
            'verification_status' => 'verified',
        ]);
    }

    private function createJadwal(array $overrides = []): JadwalPosyandu
    {
        return JadwalPosyandu::create(array_replace($this->jadwalPayload(), [
            'created_by' => $this->petugas->id,
        ], $overrides));
    }

    private function jadwalPayload(array $overrides = []): array
    {
        return array_replace([
            'judul' => 'Posyandu Bulanan',
            'kategori' => 'balita',
            'tanggal' => today()->addDays(3)->toDateString(),
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'lokasi' => 'Balai Desa Gedangan',
            'keterangan' => 'Layanan bulanan',
        ], $overrides);
    }
}
