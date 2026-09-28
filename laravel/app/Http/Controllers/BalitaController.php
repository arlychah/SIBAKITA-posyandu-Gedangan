<?php

namespace App\Http\Controllers;

use App\Helpers\StatusGizi;
use App\Models\Balita;
use App\Models\PemeriksaanBalita;
use App\Services\PemeriksaanAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BalitaController extends Controller
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
        $balita = Balita::with(['warga', 'riwayat'])->findOrFail($id);
        $warga = $balita->warga;
        $correction = $correctionId !== null;
        $existingRecord = null;

        if ($correction) {
            $existingRecord = PemeriksaanBalita::where('id', $correctionId)
                ->where('balita_id', $balita->id)
                ->where('submitted_by', Auth::id())
                ->where('verification_status', 'needs_revision')
                ->firstOrFail();
        }

        if ($request->isMethod('POST')) {
            $data = $request->validate([
                'tanggal' => ['required', 'date', 'before_or_equal:today'],
                'berat_badan' => ['required', 'numeric', 'gt:0', 'max:60'],
                'tinggi_badan' => ['required', 'numeric', 'between:45,120'],
                'lingkar_kepala' => ['nullable', 'numeric', 'between:20,70'],
                'metode_pengukuran' => ['required', Rule::in(['standing', 'recumbent'])],
                'asi_eksklusif' => ['nullable', 'string', 'max:20'],
                'vitamin_a_bulan_ke' => ['nullable', 'integer', 'min:1', 'max:24'],
                'pmt_diterima' => ['nullable', 'string', 'max:100'],
                'catatan' => ['nullable', 'string', 'max:4000'],
            ]);

            $tanggal = Carbon::parse($data['tanggal']);
            $umurBulan = Carbon::parse($warga->tanggal_lahir)->diffInMonths($tanggal);
            $jk = $warga->jenis_kelamin;
            $hasilBBTB = StatusGizi::bbtb(
                (float) $data['berat_badan'],
                (float) $data['tinggi_badan'],
                $umurBulan,
                $jk,
                $data['metode_pengukuran']
            );

            if ($hasilBBTB['z_score'] === null) {
                return back()->withErrors(['tinggi_badan' => $hasilBBTB['status']])->withInput();
            }

            $payload = [
                'tanggal' => $tanggal->toDateString(),
                'berat_badan' => (float) $data['berat_badan'],
                'tinggi_badan' => (float) $data['tinggi_badan'],
                'lingkar_kepala' => $data['lingkar_kepala'] ?? null,
                'metode_pengukuran' => $data['metode_pengukuran'],
                'rujukan_bbtb' => $hasilBBTB['reference'],
                'z_score_bbtb' => $hasilBBTB['z_score'],
                'status_gizi_bbu' => StatusGizi::bbu((float) $data['berat_badan'], $umurBulan, $jk),
                'status_gizi_tbu' => StatusGizi::tbu((float) $data['tinggi_badan'], $umurBulan, $jk),
                'status_gizi_bbtb' => $hasilBBTB['status'],
                'asi_eksklusif' => $data['asi_eksklusif'] ?? '',
                'imunisasi_bcg' => $request->boolean('imunisasi_bcg'),
                'imunisasi_dpt1' => $request->boolean('imunisasi_dpt1'),
                'imunisasi_dpt2' => $request->boolean('imunisasi_dpt2'),
                'imunisasi_dpt3' => $request->boolean('imunisasi_dpt3'),
                'imunisasi_polio1' => $request->boolean('imunisasi_polio1'),
                'imunisasi_polio2' => $request->boolean('imunisasi_polio2'),
                'imunisasi_polio3' => $request->boolean('imunisasi_polio3'),
                'imunisasi_campak' => $request->boolean('imunisasi_campak'),
                'vitamin_a_bulan_ke' => $data['vitamin_a_bulan_ke'] ?? null,
                'pmt_diterima' => $data['pmt_diterima'] ?? '',
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
                    $audit->record('balita', $existingRecord, Auth::id(), 'edited', $before, 'Koreksi kader atas permintaan petugas.');
                    return;
                }

                $record = PemeriksaanBalita::create($payload + [
                    'balita_id' => $id,
                    'submitted_by' => Auth::id(),
                    'verification_status' => 'pending',
                ]);
                $audit->record('balita', $record, Auth::id(), 'submitted');
            });

            $message = $correction
                ? 'Koreksi tersimpan dan dikirim kembali untuk verifikasi petugas.'
                : 'Pemeriksaan tersimpan dan menunggu verifikasi petugas.';

            return redirect()->route('balita.periksa', $balita->id)->with('success', $message);
        }

        $riwayat = PemeriksaanBalita::where('balita_id', $id)->orderByDesc('tanggal')->get();
        $umur_tahun = $warga->umur_tahun;
        $umur_bulan = $warga->umur_bulan;

        return view('balita.periksa', compact('balita', 'umur_tahun', 'umur_bulan', 'riwayat', 'existingRecord', 'correction'));
    }
}
