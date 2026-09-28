<?php

namespace App\Http\Controllers;

use App\Models\IbuHamil;
use App\Models\PemeriksaanIbuHamil;
use App\Services\PemeriksaanAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IbuHamilController extends Controller
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
        $ibuHamil = IbuHamil::with(['warga', 'riwayat'])->findOrFail($id);
        $correction = $correctionId !== null;
        $existingRecord = null;

        if ($correction) {
            $existingRecord = PemeriksaanIbuHamil::where('id', $correctionId)
                ->where('ibu_hamil_id', $ibuHamil->id)
                ->where('submitted_by', Auth::id())
                ->where('verification_status', 'needs_revision')
                ->firstOrFail();
        }

        if ($request->isMethod('POST')) {
            $data = $request->validate([
                'tanggal' => ['required', 'date', 'before_or_equal:today'],
                'kehamilan_ke' => ['nullable', 'integer', 'min:1', 'max:10'],
                'usia_kehamilan' => ['nullable', 'integer', 'min:1', 'max:42'],
                'berat_badan' => ['nullable', 'numeric', 'gt:0', 'max:250'],
                'tekanan_darah_sistolik' => ['nullable', 'integer', 'between:40,300'],
                'tekanan_darah_diastolik' => ['nullable', 'integer', 'between:20,200'],
                'lila' => ['nullable', 'numeric', 'between:5,80'],
                'tinggi_fundus' => ['nullable', 'numeric', 'between:1,60'],
                'detak_jantung_janin' => ['nullable', 'integer', 'between:50,250'],
                'jumlah_ttd' => ['nullable', 'integer', 'min:0', 'max:1000'],
                'imunisasi_tt' => ['nullable', 'string', 'max:50'],
                'catatan' => ['nullable', 'string', 'max:4000'],
            ]);

            $payload = [
                'tanggal' => Carbon::parse($data['tanggal'])->toDateString(),
                'kehamilan_ke' => $data['kehamilan_ke'] ?? null,
                'usia_kehamilan' => $data['usia_kehamilan'] ?? null,
                'berat_badan' => $data['berat_badan'] ?? null,
                'tekanan_darah_sistolik' => $data['tekanan_darah_sistolik'] ?? null,
                'tekanan_darah_diastolik' => $data['tekanan_darah_diastolik'] ?? null,
                'lila' => $data['lila'] ?? null,
                'tinggi_fundus' => $data['tinggi_fundus'] ?? null,
                'detak_jantung_janin' => $data['detak_jantung_janin'] ?? null,
                'ttd_diberikan' => $request->boolean('ttd_diberikan'),
                'jumlah_ttd' => $data['jumlah_ttd'] ?? null,
                'imunisasi_tt' => $data['imunisasi_tt'] ?? '',
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
                    $audit->record('ibu_hamil', $existingRecord, Auth::id(), 'edited', $before, 'Koreksi kader atas permintaan petugas.');
                    return;
                }

                $record = PemeriksaanIbuHamil::create($payload + [
                    'ibu_hamil_id' => $id,
                    'submitted_by' => Auth::id(),
                    'verification_status' => 'pending',
                ]);
                $audit->record('ibu_hamil', $record, Auth::id(), 'submitted');
            });

            return redirect()->route('ibu_hamil.periksa', $id)->with('success', $correction
                ? 'Koreksi tersimpan dan dikirim kembali untuk verifikasi petugas.'
                : 'Pemeriksaan tersimpan dan menunggu verifikasi petugas.');
        }

        $riwayat = PemeriksaanIbuHamil::where('ibu_hamil_id', $id)->orderByDesc('tanggal')->get();

        return view('ibu_hamil.periksa', [
            'ibu_hamil' => $ibuHamil,
            'riwayat' => $riwayat,
            'existingRecord' => $existingRecord,
            'correction' => $correction,
        ]);
    }
}
