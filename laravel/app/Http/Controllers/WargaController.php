<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use App\Models\PemeriksaanAuditLog;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WargaController extends Controller
{
    private const KATEGORI = ['Balita', 'Ibu Hamil', 'Lansia'];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'kategori' => ['nullable', 'string', Rule::in(self::KATEGORI)],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $kategori = $filters['kategori'] ?? '';
        $search = trim($filters['search'] ?? '');
        $query = Warga::with(['balita', 'ibu_hamil', 'lansia']);

        if ($kategori !== '') {
            $kategoriNormal = strtolower(str_replace(' ', '_', $kategori));
            $query->whereRaw("LOWER(REPLACE(kategori, ' ', '_')) = ?", [$kategoriNormal]);
        }

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('nama_lengkap', 'LIKE', "%{$search}%")
                    ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $wargaList = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('warga.index', compact('wargaList', 'kategori', 'search'));
    }

    public function create()
    {
        return view('warga.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());

        DB::transaction(function () use ($data) {
            $warga = Warga::create(Arr::only($data, $this->wargaFields()));
            $this->syncCategoryProfile($warga, $data);
        });

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil ditambahkan!');
    }

    public function show(int $id)
    {
        $warga = Warga::with([
            'balita.riwayat',
            'ibu_hamil.riwayat',
            'lansia.riwayat',
        ])->findOrFail($id);

        return view('warga.show', compact('warga'));
    }

    public function edit(int $id)
    {
        $warga = Warga::with(['balita', 'ibu_hamil', 'lansia'])->findOrFail($id);

        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, int $id)
    {
        $warga = Warga::with(['balita', 'ibu_hamil', 'lansia'])->findOrFail($id);
        $data = $request->validate($this->rules($warga));
        $newCategory = $data['kategori'];

        if ($newCategory !== $warga->kategori && $this->hasExaminationHistory($warga)) {
            return back()->withErrors([
                'kategori' => 'Kategori tidak dapat diubah karena warga sudah memiliki riwayat pemeriksaan.',
            ])->withInput();
        }

        DB::transaction(function () use ($warga, $data, $newCategory) {
            if ($newCategory !== $warga->kategori) {
                $warga->balita()->delete();
                $warga->ibu_hamil()->delete();
                $warga->lansia()->delete();
            }

            $warga->update(Arr::only($data, $this->wargaFields()));
            $this->syncCategoryProfile($warga, $data);
        });

        return redirect()->route('warga.show', $warga->id)->with('success', 'Data warga berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $warga = Warga::with(['balita', 'ibu_hamil', 'lansia'])->findOrFail($id);

        DB::transaction(function () use ($warga) {
            $this->deleteAuditHistory($warga);
            $warga->delete();
        });

        return redirect()->route('warga.index')->with('success', 'Data warga dan riwayat pemeriksaan berhasil dihapus.');
    }

    private function rules(?Warga $warga = null): array
    {
        return [
            'nik' => ['required', 'digits:16', Rule::unique('warga', 'nik')->ignore($warga?->id)],
            'nama_lengkap' => ['required', 'string', 'max:200'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'kategori' => ['required', Rule::in(self::KATEGORI)],
            'whatsapp' => ['required', 'digits_between:10,15'],
            'puskesmas' => ['required', 'string', 'max:150'],
            'pustu' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:2000'],
            'nama_ibu' => ['nullable', 'string', 'max:200'],
            'nama_ayah' => ['nullable', 'string', 'max:200'],
            'nama_suami' => ['nullable', 'string', 'max:200'],
        ];
    }

    private function wargaFields(): array
    {
        return [
            'nik', 'nama_lengkap', 'whatsapp', 'puskesmas', 'pustu',
            'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'kategori',
        ];
    }

    private function syncCategoryProfile(Warga $warga, array $data): void
    {
        if ($data['kategori'] === 'Balita') {
            $warga->balita()->updateOrCreate([], [
                'nama_ibu' => $data['nama_ibu'] ?? '',
                'nama_ayah' => $data['nama_ayah'] ?? '',
            ]);
        } elseif ($data['kategori'] === 'Ibu Hamil') {
            $warga->ibu_hamil()->updateOrCreate([], [
                'nama_suami' => $data['nama_suami'] ?? '',
            ]);
        } elseif ($data['kategori'] === 'Lansia') {
            $warga->lansia()->firstOrCreate([]);
        }
    }

    private function hasExaminationHistory(Warga $warga): bool
    {
        return ($warga->balita && PemeriksaanBalita::where('balita_id', $warga->balita->id)->exists())
            || ($warga->ibu_hamil && PemeriksaanIbuHamil::where('ibu_hamil_id', $warga->ibu_hamil->id)->exists())
            || ($warga->lansia && PemeriksaanLansia::where('lansia_id', $warga->lansia->id)->exists());
    }

    private function deleteAuditHistory(Warga $warga): void
    {
        $profiles = [
            ['balita', $warga->balita, 'balita_id', PemeriksaanBalita::class],
            ['ibu_hamil', $warga->ibu_hamil, 'ibu_hamil_id', PemeriksaanIbuHamil::class],
            ['lansia', $warga->lansia, 'lansia_id', PemeriksaanLansia::class],
        ];

        foreach ($profiles as [$type, $profile, $foreignKey, $model]) {
            if (!$profile) {
                continue;
            }

            $examinationIds = $model::where($foreignKey, $profile->id)->pluck('id');
            PemeriksaanAuditLog::where('jenis_pemeriksaan', $type)
                ->whereIn('pemeriksaan_id', $examinationIds)
                ->delete();
        }
    }
}
