<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use App\Models\PemeriksaanAuditLog;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExaminationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $kader;

    private User $petugas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kader = User::factory()->create(['role' => 'kader']);
        $this->petugas = User::factory()->create(['role' => 'petugas']);
    }

    public function test_kader_can_submit_balita_examination_and_audit_entry_is_created(): void
    {
        $warga = Warga::factory()->balita()->create([
            'tanggal_lahir' => today()->subMonths(30)->toDateString(),
        ]);
        $balita = Balita::create(['warga_id' => $warga->id]);

        $this->actingAs($this->kader)
            ->get(route('balita.periksa', $balita->id))
            ->assertOk()
            ->assertSee('Form Pemeriksaan Balita');

        $this->post(route('balita.periksa.store', $balita->id), [
            'tanggal' => today()->toDateString(),
            'berat_badan' => 12.0,
            'tinggi_badan' => 90.0,
            'metode_pengukuran' => 'standing',
        ])->assertRedirect(route('balita.periksa', $balita->id));

        $record = PemeriksaanBalita::where('balita_id', $balita->id)->firstOrFail();
        $this->assertSame('pending', $record->verification_status);
        $this->assertNotNull($record->z_score_bbtb);
        $this->assertDatabaseHas('pemeriksaan_audit_logs', [
            'jenis_pemeriksaan' => 'balita',
            'pemeriksaan_id' => $record->id,
            'actor_id' => $this->kader->id,
            'aksi' => 'submitted',
        ]);
    }

    public function test_kader_can_open_and_submit_ibu_hamil_examination(): void
    {
        $warga = Warga::factory()->ibuHamil()->create();
        $ibuHamil = IbuHamil::create(['warga_id' => $warga->id]);

        $this->actingAs($this->kader)
            ->get(route('ibu_hamil.periksa', $ibuHamil->id))
            ->assertOk()
            ->assertSee('Form Pemeriksaan Antenatal Care');

        $this->post(route('ibu_hamil.periksa.store', $ibuHamil->id), [
            'tanggal' => today()->toDateString(),
            'usia_kehamilan' => 20,
            'lila' => 24.5,
        ])->assertRedirect(route('ibu_hamil.periksa', $ibuHamil->id));

        $this->assertDatabaseHas('pemeriksaan_ibu_hamil', [
            'ibu_hamil_id' => $ibuHamil->id,
            'submitted_by' => $this->kader->id,
            'verification_status' => 'pending',
            'usia_kehamilan' => 20,
        ]);
    }

    public function test_kader_can_submit_lansia_examination(): void
    {
        $warga = Warga::factory()->lansia()->create();
        $lansia = Lansia::create(['warga_id' => $warga->id]);

        $this->actingAs($this->kader)
            ->get(route('lansia.periksa', $lansia->id))
            ->assertOk()
            ->assertSee('Form Skrining Penyakit Tidak Menular');

        $this->post(route('lansia.periksa.store', $lansia->id), [
            'tanggal' => today()->toDateString(),
            'berat_badan' => 65,
            'tinggi_badan' => 160,
            'tekanan_darah_sistolik' => 145,
            'tekanan_darah_diastolik' => 92,
        ])->assertRedirect(route('lansia.periksa', $lansia->id));

        $record = PemeriksaanLansia::where('lansia_id', $lansia->id)->firstOrFail();
        $this->assertSame('pending', $record->verification_status);
        $this->assertSame(25.39, $record->imt);
    }

    public function test_petugas_return_and_kader_correct_ibu_hamil_examination_with_audit_trail(): void
    {
        $warga = Warga::factory()->ibuHamil()->create();
        $ibuHamil = IbuHamil::create(['warga_id' => $warga->id]);
        $record = PemeriksaanIbuHamil::create([
            'ibu_hamil_id' => $ibuHamil->id,
            'tanggal' => today()->toDateString(),
            'usia_kehamilan' => 20,
            'submitted_by' => $this->kader->id,
            'verification_status' => 'pending',
        ]);

        $this->actingAs($this->petugas)
            ->post(route('verifikasi.return', ['ibu_hamil', $record->id]), ['alasan' => 'Mohon lengkapi usia kehamilan.'])
            ->assertRedirect(route('verifikasi.show', ['ibu_hamil', $record->id]));

        $record->refresh();
        $this->assertSame('needs_revision', $record->verification_status);
        $this->assertSame('Mohon lengkapi usia kehamilan.', $record->return_reason);

        $this->actingAs($this->kader)
            ->get(route('ibu_hamil.periksa.koreksi', [$ibuHamil->id, $record->id]))
            ->assertOk()
            ->assertSee('Perlu perbaikan');

        $this->post(route('ibu_hamil.periksa.koreksi.update', [$ibuHamil->id, $record->id]), [
            'tanggal' => today()->toDateString(),
            'usia_kehamilan' => 21,
            'catatan' => 'Sudah diperiksa ulang',
        ])->assertRedirect(route('ibu_hamil.periksa', $ibuHamil->id));

        $record->refresh();
        $this->assertSame('pending', $record->verification_status);
        $this->assertNull($record->return_reason);
        $this->assertSame(21, (int) $record->usia_kehamilan);
        $this->assertSame(2, PemeriksaanAuditLog::where('jenis_pemeriksaan', 'ibu_hamil')
            ->where('pemeriksaan_id', $record->id)
            ->count());
    }

    public function test_petugas_can_approve_a_pending_lansia_examination(): void
    {
        $warga = Warga::factory()->lansia()->create();
        $lansia = Lansia::create(['warga_id' => $warga->id]);
        $record = PemeriksaanLansia::create([
            'lansia_id' => $lansia->id,
            'tanggal' => today()->toDateString(),
            'submitted_by' => $this->kader->id,
            'verification_status' => 'pending',
        ]);

        $this->actingAs($this->petugas)
            ->post(route('verifikasi.approve', ['lansia', $record->id]))
            ->assertRedirect(route('verifikasi.show', ['lansia', $record->id]));

        $record->refresh();
        $this->assertSame('verified', $record->verification_status);
        $this->assertSame($this->petugas->id, (int) $record->verified_by);
        $this->assertNotNull($record->verified_at);
    }

    public function test_petugas_can_cancel_each_examination_type_with_audited_reason(): void
    {
        $balitaWarga = Warga::factory()->balita()->create([
            'tanggal_lahir' => today()->subMonths(30)->toDateString(),
        ]);
        $balita = Balita::create(['warga_id' => $balitaWarga->id]);
        $ibuWarga = Warga::factory()->ibuHamil()->create();
        $ibuHamil = IbuHamil::create(['warga_id' => $ibuWarga->id]);
        $lansiaWarga = Warga::factory()->lansia()->create();
        $lansia = Lansia::create(['warga_id' => $lansiaWarga->id]);

        $records = [
            'balita' => PemeriksaanBalita::create([
                'balita_id' => $balita->id,
                'tanggal' => today()->toDateString(),
                'berat_badan' => 12,
                'tinggi_badan' => 90,
                'submitted_by' => $this->kader->id,
                'verification_status' => 'pending',
            ]),
            'ibu_hamil' => PemeriksaanIbuHamil::create([
                'ibu_hamil_id' => $ibuHamil->id,
                'tanggal' => today()->toDateString(),
                'submitted_by' => $this->kader->id,
                'verification_status' => 'pending',
            ]),
            'lansia' => PemeriksaanLansia::create([
                'lansia_id' => $lansia->id,
                'tanggal' => today()->toDateString(),
                'submitted_by' => $this->kader->id,
                'verification_status' => 'pending',
            ]),
        ];

        foreach ($records as $type => $record) {
            $reason = 'Data pemeriksaan keliru dan perlu dibatalkan.';

            $this->actingAs($this->petugas)
                ->post(route('verifikasi.cancel', [$type, $record->id]), [
                    'alasan_pembatalan' => $reason,
                ])
                ->assertRedirect(route('verifikasi.show', [$type, $record->id]));

            $record->refresh();
            $this->assertSame('cancelled', $record->verification_status);

            $auditLog = PemeriksaanAuditLog::where('jenis_pemeriksaan', $type)
                ->where('pemeriksaan_id', $record->id)
                ->where('aksi', 'cancelled')
                ->firstOrFail();
            $this->assertSame($this->petugas->id, (int) $auditLog->actor_id);
            $this->assertSame($reason, $auditLog->alasan);
            $this->assertSame('pending', $auditLog->sebelum['verification_status']);
            $this->assertSame('cancelled', $auditLog->sesudah['verification_status']);
        }
    }

    public function test_cancellation_requires_petugas_and_a_valid_reason_and_is_terminal(): void
    {
        $warga = Warga::factory()->lansia()->create();
        $lansia = Lansia::create(['warga_id' => $warga->id]);
        $record = PemeriksaanLansia::create([
            'lansia_id' => $lansia->id,
            'tanggal' => today()->toDateString(),
            'submitted_by' => $this->kader->id,
            'verified_by' => $this->petugas->id,
            'verified_at' => now(),
            'verification_status' => 'verified',
        ]);
        $cancelUrl = route('verifikasi.cancel', ['lansia', $record->id]);
        $showUrl = route('verifikasi.show', ['lansia', $record->id]);

        $this->actingAs($this->kader)
            ->post($cancelUrl, ['alasan_pembatalan' => 'Data sudah tidak sah.'])
            ->assertForbidden();
        $this->assertDatabaseCount('pemeriksaan_audit_logs', 0);

        foreach ([[], ['alasan_pembatalan' => 'tid']] as $invalidPayload) {
            $this->actingAs($this->petugas)
                ->from($showUrl)
                ->post($cancelUrl, $invalidPayload)
                ->assertSessionHasErrors('alasan_pembatalan');
        }
        $this->assertSame('verified', $record->fresh()->verification_status);
        $this->assertDatabaseCount('pemeriksaan_audit_logs', 0);

        $reason = 'Pemeriksaan tercatat ganda pada kunjungan ini.';
        $this->post($cancelUrl, ['alasan_pembatalan' => $reason])
            ->assertRedirect($showUrl);

        $record->refresh();
        $this->assertSame('cancelled', $record->verification_status);
        $this->assertNull($record->verified_by);
        $this->assertNull($record->verified_at);
        $this->assertDatabaseHas('pemeriksaan_audit_logs', [
            'jenis_pemeriksaan' => 'lansia',
            'pemeriksaan_id' => $record->id,
            'aksi' => 'cancelled',
            'alasan' => $reason,
        ]);

        $this->post($cancelUrl, ['alasan_pembatalan' => $reason])->assertStatus(409);
        $this->get(route('verifikasi.edit', ['lansia', $record->id]))->assertStatus(409);
        $this->post(route('verifikasi.update', ['lansia', $record->id]), [])->assertStatus(409);
        $this->get($showUrl)
            ->assertOk()
            ->assertSee('Dibatalkan')
            ->assertSee($reason)
            ->assertDontSee('Batalkan pemeriksaan');
        $this->get(route('verifikasi.index', ['status' => 'cancelled']))
            ->assertOk()
            ->assertSee('Dibatalkan');
        $this->get(route('laporan.lansia', [
            'start' => today()->toDateString(),
            'end' => today()->toDateString(),
        ]))->assertOk()->assertViewHas('total', 0);
    }
}
