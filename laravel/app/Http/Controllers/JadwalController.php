<?php

namespace App\Http\Controllers;

use App\Models\JadwalPosyandu;
use App\Models\User;
use App\Support\UserRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JadwalController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $query = JadwalPosyandu::with('creator');
        if ($user->role === UserRole::WARGA) {
            $kategori = $user->warga?->kategoriJadwal();
            $query->scheduled()->approved()->forMemberCategory($kategori)->whereDate('tanggal', '>=', today());
        } elseif ($user->role === UserRole::KADER) {
            $query->where(function ($query) use ($user) {
                $query->where('approval_status', JadwalPosyandu::APPROVAL_APPROVED)
                    ->orWhere(function ($query) use ($user) {
                        $query->where('created_by', $user->id)
                            ->whereIn('approval_status', [
                                JadwalPosyandu::APPROVAL_PENDING,
                                JadwalPosyandu::APPROVAL_NEEDS_REVISION,
                            ]);
                    });
            });
        }

        if ($user->role === UserRole::PETUGAS) {
            $query->orderByRaw('CASE WHEN approval_status = ? THEN 0 ELSE 1 END', [JadwalPosyandu::APPROVAL_PENDING]);
        }
        $query->orderBy('tanggal')->orderBy('waktu_mulai');

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
        if ($request->user()->role === UserRole::KADER) {
            $validated['approval_status'] = JadwalPosyandu::APPROVAL_PENDING;
        } else {
            $validated['approval_status'] = JadwalPosyandu::APPROVAL_APPROVED;
            $validated['reviewed_by'] = $request->user()->id;
            $validated['reviewed_at'] = now();
        }
        JadwalPosyandu::create($validated);

        $message = $request->user()->role === UserRole::KADER
            ? 'Pengajuan jadwal berhasil dikirim ke petugas untuk ditinjau.'
            : 'Jadwal Posyandu berhasil dibuat.';

        return redirect()->route('jadwal.index')->with('success', $message);
    }

    public function edit(JadwalPosyandu $jadwal)
    {
        $this->ensureCanEdit($jadwal, request()->user());

        return view('jadwal.form', [
            'jadwal' => $jadwal,
            'action' => route('jadwal.update', $jadwal),
        ]);
    }

    public function update(Request $request, JadwalPosyandu $jadwal)
    {
        $this->ensureCanEdit($jadwal, $request->user());
        $changes = $this->validated($request) + ['updated_by' => $request->user()->id];
        if ($request->user()->role === UserRole::KADER) {
            $changes += [
                'approval_status' => JadwalPosyandu::APPROVAL_PENDING,
                'review_note' => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ];
        }
        $jadwal->update($changes);

        $message = $request->user()->role === UserRole::KADER
            ? 'Jadwal hasil revisi berhasil dikirim kembali ke petugas.'
            : 'Jadwal Posyandu berhasil diperbarui.';

        return redirect()->route('jadwal.index')->with('success', $message);
    }

    public function cancel(Request $request, JadwalPosyandu $jadwal)
    {
        $this->ensureScheduled($jadwal);
        abort_unless($jadwal->approval_status === JadwalPosyandu::APPROVAL_APPROVED, 409);
        $jadwal->update([
            'status' => JadwalPosyandu::STATUS_DIBATALKAN,
            'updated_by' => $request->user()->id,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal dibatalkan. Riwayat jadwal tetap tersimpan.');
    }

    public function approve(Request $request, JadwalPosyandu $jadwal)
    {
        $this->ensurePendingReview($jadwal);
        $jadwal->update([
            'approval_status' => JadwalPosyandu::APPROVAL_APPROVED,
            'review_note' => null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal disetujui dan sekarang tampil untuk warga.');
    }

    public function requestRevision(Request $request, JadwalPosyandu $jadwal)
    {
        $this->ensurePendingReview($jadwal);
        $validated = $request->validate([
            'review_note' => ['required', 'string', 'max:2000'],
        ]);
        $jadwal->update([
            'approval_status' => JadwalPosyandu::APPROVAL_NEEDS_REVISION,
            'review_note' => $validated['review_note'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal dikembalikan kepada kader untuk diperbaiki.');
    }

    private function ensureCanEdit(JadwalPosyandu $jadwal, User $user): void
    {
        if ($user->role === UserRole::KADER) {
            abort_unless(
                (int) $jadwal->created_by === (int) $user->id
                    && $jadwal->approval_status === JadwalPosyandu::APPROVAL_NEEDS_REVISION,
                403
            );

            return;
        }

        $this->ensureScheduled($jadwal);
        abort_unless($jadwal->approval_status === JadwalPosyandu::APPROVAL_APPROVED, 409);
    }

    private function ensurePendingReview(JadwalPosyandu $jadwal): void
    {
        abort_unless(
            $jadwal->approval_status === JadwalPosyandu::APPROVAL_PENDING
                && $jadwal->status === JadwalPosyandu::STATUS_TERJADWAL,
            409,
            'Jadwal ini tidak sedang menunggu persetujuan.'
        );
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
