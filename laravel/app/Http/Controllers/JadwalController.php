<?php

namespace App\Http\Controllers;

use App\Models\JadwalPosyandu;
use App\Support\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $query = JadwalPosyandu::with('creator')->orderBy('tanggal')->orderBy('waktu_mulai');
        if ($user->role === UserRole::WARGA) {
            $kategori = $user->warga?->kategoriJadwal();
            $query->scheduled()->forMemberCategory($kategori)->whereDate('tanggal', '>=', today());
        }

        $jadwal = $query->paginate(15);

        return view('jadwal.index', compact('jadwal'));
    }

    public function create()
    {
        return view('jadwal.form', ['jadwal' => new JadwalPosyandu(), 'action' => route('jadwal.store')]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['created_by'] = $request->user()->id;
        JadwalPosyandu::create($validated);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal Posyandu berhasil dibuat.');
    }

    public function edit(JadwalPosyandu $jadwal)
    {
        $this->ensureScheduled($jadwal);

        return view('jadwal.form', [
            'jadwal' => $jadwal,
            'action' => route('jadwal.update', $jadwal),
        ]);
    }

    public function update(Request $request, JadwalPosyandu $jadwal)
    {
        $this->ensureScheduled($jadwal);
        $jadwal->update($this->validated($request) + ['updated_by' => $request->user()->id]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal Posyandu berhasil diperbarui.');
    }

    public function cancel(Request $request, JadwalPosyandu $jadwal)
    {
        $this->ensureScheduled($jadwal);
        $jadwal->update([
            'status' => JadwalPosyandu::STATUS_DIBATALKAN,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal dibatalkan. Riwayat jadwal tetap tersimpan.');
    }

    private function ensureScheduled(JadwalPosyandu $jadwal): void
    {
        abort_unless($jadwal->status === JadwalPosyandu::STATUS_TERJADWAL, 409, 'Jadwal yang sudah dibatalkan tidak dapat diubah.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:160'],
            'kategori' => ['required', Rule::in(JadwalPosyandu::KATEGORI)],
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'waktu_mulai' => ['required', 'date_format:H:i'],
            'waktu_selesai' => ['nullable', 'date_format:H:i', 'after:waktu_mulai'],
            'lokasi' => ['required', 'string', 'max:180'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
