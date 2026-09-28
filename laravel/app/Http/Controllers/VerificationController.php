<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanAuditLog;
use App\Services\PemeriksaanAuditService;
use App\Support\ExaminationRegistry;
use App\Support\StatusGizi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VerificationController extends Controller
{
    private const RELATIONS = [
        'balita' => 'balita.warga',
        'ibu_hamil' => 'ibu_hamil.warga',
        'lansia' => 'lansia.warga',
    ];

    private const LABELS = [
        'balita' => 'Balita',
        'ibu_hamil' => 'Ibu hamil',
        'lansia' => 'Lansia',
    ];

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        abort_unless(in_array($status, ['pending', 'legacy_review', 'needs_revision', 'verified'], true), 404);
        $type = $request->query('jenis');
        abort_if($type && !in_array($type, ExaminationRegistry::types(), true), 404);

        $items = collect();
        foreach (ExaminationRegistry::types() as $examType) {
            if ($type && $type !== $examType) {
                continue;
            }

            $model = ExaminationRegistry::model($examType);
            $records = $model::with([self::RELATIONS[$examType], 'submitter'])
                ->where('verification_status', $status)
                ->orderByDesc('created_at')
                ->limit(100)
                ->get();

            foreach ($records as $record) {
                $owner = $record->{explode('.', self::RELATIONS[$examType])[0]};
                $items->push((object) [
                    'record' => $record,
                    'type' => $examType,
                    'label' => self::LABELS[$examType],
                    'member' => $owner && $owner->warga ? $owner->warga->nama_lengkap : 'Profil warga tidak ditemukan',
                    'category' => $owner && $owner->warga ? $owner->warga->kategori : '',
                    'submitted_by_name' => optional($record->submitter)->name ?? 'Akun kader tidak tersedia',
                ]);
            }
        }

        $items = $items->sortByDesc(fn ($item) => $item->record->created_at)->values();

        return view('verifikasi.index', compact('items', 'status', 'type'));
    }

    public function show(string $type, int $id)
    {
        $record = $this->findRecord($type, $id, true);
        $label = self::LABELS[$type];
        $auditLogs = PemeriksaanAuditLog::with('actor')
            ->where('jenis_pemeriksaan', $type)
            ->where('pemeriksaan_id', $record->id)
            ->orderByDesc('created_at')
            ->get();

        return view('verifikasi.show', compact('record', 'type', 'label', 'auditLogs'));
    }

    public function approve(string $type, int $id, PemeriksaanAuditService $audit)
    {
        $record = $this->findRecord($type, $id);
        abort_unless(in_array($record->verification_status, ['pending', 'legacy_review'], true), 409, 'Hanya hasil yang menunggu tinjauan dapat disahkan.');

        if ($type === 'balita' && $record->z_score_bbtb === null) {
            return back()->with('error', 'Nilai BB/TB di luar rentang WHO. Koreksi data atau lakukan evaluasi manual sebelum verifikasi.');
        }

        DB::transaction(function () use ($record, $audit, $type) {
            $before = $audit->snapshot($record);
            $record->update([
                'verification_status' => 'verified',
                'verified_by' => request()->user()->id,
                'verified_at' => now(),
                'return_reason' => null,
            ]);
            $audit->record($type, $record, request()->user()->id, 'verified', $before);
        });

        return redirect()->route('verifikasi.show', [$type, $id])->with('success', 'Hasil pemeriksaan telah diverifikasi dan dapat dilihat anggota.');
    }

    public function returnForCorrection(Request $request, string $type, int $id, PemeriksaanAuditService $audit)
    {
        $data = $request->validate(['alasan' => ['required', 'string', 'min:5', 'max:2000']]);
        $record = $this->findRecord($type, $id);
        abort_unless(in_array($record->verification_status, ['pending', 'legacy_review'], true), 409, 'Hanya hasil yang menunggu tinjauan dapat dikembalikan.');

        DB::transaction(function () use ($record, $audit, $type, $data) {
            $before = $audit->snapshot($record);
            $record->update([
                'verification_status' => 'needs_revision',
                'verified_by' => request()->user()->id,
                'verified_at' => null,
                'return_reason' => $data['alasan'],
            ]);
            $audit->record($type, $record, request()->user()->id, 'returned', $before, $data['alasan']);
        });

        return redirect()->route('verifikasi.show', [$type, $id])->with('success', 'Hasil dikembalikan kepada kader untuk perbaikan.');
    }

    public function edit(string $type, int $id)
    {
        $record = $this->findRecord($type, $id, true);

        return view('verifikasi.edit', [
            'record' => $record,
            'type' => $type,
            'label' => self::LABELS[$type],
        ]);
    }

    public function update(Request $request, string $type, int $id, PemeriksaanAuditService $audit)
    {
        $record = $this->findRecord($type, $id);
        $validated = $request->validate($this->editRules($type));
        $reason = $validated['alasan_edit'];
        unset($validated['alasan_edit']);

        if ($type === 'balita') {
            $warga = $record->balita->warga;
            $ageMonths = Carbon::parse($warga->tanggal_lahir)->diffInMonths(Carbon::parse($validated['tanggal']));
            $resultBbtb = StatusGizi::bbtb(
                $validated['berat_badan'],
                $validated['tinggi_badan'],
                $ageMonths,
                $warga->jenis_kelamin,
                $validated['metode_pengukuran']
            );
            if ($resultBbtb['z_score'] === null) {
                return back()->withErrors(['tinggi_badan' => $resultBbtb['status']])->withInput();
            }
            $validated = $this->withChildNutritionValues($validated, $warga, $ageMonths, $resultBbtb);
        }

        DB::transaction(function () use ($record, $validated, $reason, $audit, $type) {
            $before = $audit->snapshot($record);
            $validated['verification_status'] = 'pending';
            $validated['verified_by'] = null;
            $validated['verified_at'] = null;
            $validated['return_reason'] = null;
            $record->update($validated);
            $audit->record($type, $record, request()->user()->id, 'edited', $before, $reason);
        });

        return redirect()->route('verifikasi.show', [$type, $id])->with('success', 'Perubahan tersimpan. Hasil kembali menunggu verifikasi.');
    }

    private function findRecord(string $type, int $id, bool $withHistory = false)
    {
        abort_unless(in_array($type, ExaminationRegistry::types(), true), 404);
        $model = ExaminationRegistry::model($type);
        $relations = [self::RELATIONS[$type], 'submitter', 'verifier'];
        if ($withHistory) {
            $relations[] = 'auditLogs.actor';
        }

        return $model::with($relations)->findOrFail($id);
    }

    private function withChildNutritionValues(array $values, $warga, int $ageMonths, array $bbtb): array
    {
        $sex = $warga->jenis_kelamin;
        $values['status_gizi_bbu'] = StatusGizi::bbu($values['berat_badan'], $ageMonths, $sex);
        $values['status_gizi_tbu'] = StatusGizi::tbu($values['tinggi_badan'], $ageMonths, $sex);
        $values['status_gizi_bbtb'] = $bbtb['status'];
        $values['rujukan_bbtb'] = $bbtb['reference'];
        $values['z_score_bbtb'] = $bbtb['z_score'];

        return $values;
    }

    private function editRules(string $type): array
    {
        $common = [
            'tanggal' => ['required', 'date'],
            'alasan_edit' => ['required', 'string', 'min:5', 'max:2000'],
        ];

        if ($type === 'balita') {
            return $common + [
                'berat_badan' => ['required', 'numeric', 'gt:0', 'max:60'],
                'tinggi_badan' => ['required', 'numeric', 'gt:0', 'between:45,120'],
                'lingkar_kepala' => ['nullable', 'numeric', 'between:20,70'],
                'metode_pengukuran' => ['required', Rule::in(['standing', 'recumbent'])],
                'asi_eksklusif' => ['nullable', 'string', 'max:20'],
                'imunisasi_bcg' => ['nullable', 'boolean'],
                'imunisasi_dpt1' => ['nullable', 'boolean'],
                'imunisasi_dpt2' => ['nullable', 'boolean'],
                'imunisasi_dpt3' => ['nullable', 'boolean'],
                'imunisasi_polio1' => ['nullable', 'boolean'],
                'imunisasi_polio2' => ['nullable', 'boolean'],
                'imunisasi_polio3' => ['nullable', 'boolean'],
                'imunisasi_campak' => ['nullable', 'boolean'],
                'vitamin_a_bulan_ke' => ['nullable', 'integer', 'min:1', 'max:24'],
                'pmt_diterima' => ['nullable', 'string', 'max:100'],
                'catatan' => ['nullable', 'string', 'max:4000'],
            ];
        }

        if ($type === 'ibu_hamil') {
            return $common + [
                'kehamilan_ke' => ['nullable', 'integer', 'min:1', 'max:10'],
                'usia_kehamilan' => ['nullable', 'integer', 'min:1', 'max:42'],
                'berat_badan' => ['nullable', 'numeric', 'gt:0', 'max:250'],
                'tekanan_darah_sistolik' => ['nullable', 'integer', 'between:40,300'],
                'tekanan_darah_diastolik' => ['nullable', 'integer', 'between:20,200'],
                'lila' => ['nullable', 'numeric', 'between:5,80'],
                'tinggi_fundus' => ['nullable', 'numeric', 'between:1,60'],
                'detak_jantung_janin' => ['nullable', 'integer', 'between:50,250'],
                'ttd_diberikan' => ['nullable', 'boolean'],
                'jumlah_ttd' => ['nullable', 'integer', 'min:0', 'max:1000'],
                'imunisasi_tt' => ['nullable', 'string', 'max:50'],
                'catatan' => ['nullable', 'string', 'max:4000'],
            ];
        }

        return $common + [
            'berat_badan' => ['nullable', 'numeric', 'gt:0', 'max:300'],
            'tinggi_badan' => ['nullable', 'numeric', 'between:80,250'],
            'tekanan_darah_sistolik' => ['nullable', 'integer', 'between:40,300'],
            'tekanan_darah_diastolik' => ['nullable', 'integer', 'between:20,200'],
            'gula_darah_puasa' => ['nullable', 'numeric', 'between:10,1500'],
            'gula_darah_sewaktu' => ['nullable', 'numeric', 'between:10,1500'],
            'kolesterol' => ['nullable', 'numeric', 'between:10,1500'],
            'asam_urat' => ['nullable', 'numeric', 'between:0,40'],
            'skrining_jiwa' => ['nullable', 'string', 'max:100'],
            'penglihatan' => ['nullable', 'string', 'max:100'],
            'pendengaran' => ['nullable', 'string', 'max:100'],
            'catatan' => ['nullable', 'string', 'max:4000'],
        ];
    }
}
