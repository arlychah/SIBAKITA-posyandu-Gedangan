<?php

namespace App\Http\Controllers;

use App\Models\Lansia;
use App\Models\PemeriksaanLansia;
use App\Services\PemeriksaanAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LansiaController extends Controller
{
    public function periksa(Request $request, int $id)
    {
        return $this->handleForm($request, $id, null);
    }

    public function koreksi(Request $request, int $id, int $pemeriksaan)
    {
        return $this->handleForm($request, $id, $pemeriksaan);
    }

    private function handleForm(Request $request, int $id, ?int $correctionId)
    {
        $lansia = Lansia::with(['warga', 'riwayat'])->findOrFail($id);
        $correction = $correctionId !== null;
        $existingRecord = null;

        if ($correction) {
            $existingRecord = PemeriksaanLansia::where('id', $correctionId)
                ->where('lansia_id', $lansia->id)
                ->where('submitted_by', Auth::id())
                ->where('verification_status', 'needs_revision')
                ->firstOrFail();
        }

        if ($request->isMethod('POST')) {
            $data = $request->validate([
                'tanggal' => ['required', 'date', 'before_or_equal:today'],
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
            ]);

            $tinggi = $data['tinggi_badan'] ?? null;
            $berat = $data['berat_badan'] ?? null;
            $imt = $berat && $tinggi ? round($berat / (($tinggi / 100) ** 2), 2) : null;
            $payload = [
                'tanggal' => Carbon::parse($data['tanggal'])->toDateString(),
                'berat_badan' => $berat,
                'tinggi_badan' => $tinggi,
                'imt' => $imt,
                'tekanan_darah_sistolik' => $data['tekanan_darah_sistolik'] ?? null,
                'tekanan_darah_diastolik' => $data['tekanan_darah_diastolik'] ?? null,
                'gula_darah_puasa' => $data['gula_darah_puasa'] ?? null,
                'gula_darah_sewaktu' => $data['gula_darah_sewaktu'] ?? null,
                'kolesterol' => $data['kolesterol'] ?? null,
                'asam_urat' => $data['asam_urat'] ?? null,
                'skrining_jiwa' => $data['skrining_jiwa'] ?? '',
                'penglihatan' => $data['penglihatan'] ?? '',
                'pendengaran' => $data['pendengaran'] ?? '',
                'catatan' => $data['catatan'] ?? '',
            ];

            DB::transaction(function () use ($correction, $existingRecord, $payload, $id) {
                $audit = app(PemeriksaanAuditService::class);
                if ($correction) {
                    $before = $audit->snapshot($existingRecord);
                    $existingRecord->update($payload + [
                        'verification_status' => 'pending',
                        'verified_by' => null,
                        'verified_at' => null,
                        'return_reason' => null,
                    ]);
                    $audit->record('lansia', $existingRecord, Auth::id(), 'edited', $before, 'Koreksi kader atas permintaan petugas.');
                    return;
                }

                $record = PemeriksaanLansia::create($payload + [
                    'lansia_id' => $id,
                    'submitted_by' => Auth::id(),
                    'verification_status' => 'pending',
                ]);
                $audit->record('lansia', $record, Auth::id(), 'submitted');
            });

            return redirect()->route('lansia.periksa', $id)->with('success', $correction
                ? 'Koreksi tersimpan dan dikirim kembali untuk verifikasi petugas.'
                : 'Pemeriksaan tersimpan dan menunggu verifikasi petugas.');
        }

        $riwayat = PemeriksaanLansia::where('lansia_id', $id)->orderByDesc('tanggal')->get();

        return view('lansia.periksa', compact('lansia', 'riwayat', 'existingRecord', 'correction'));
    }
}
