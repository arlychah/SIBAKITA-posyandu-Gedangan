<?php

namespace App\Http\Controllers;

use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KaderDashboardController extends Controller
{
    private const LABELS = ['balita' => 'Balita', 'ibu_hamil' => 'Ibu Hamil', 'lansia' => 'Lansia'];

    public function index(Request $request)
    {
        $kategori = $request->query('kategori');
        $search = trim((string) $request->query('search', ''));
        $normalizedCategory = $kategori ? strtolower(str_replace(' ', '_', $kategori)) : null;

        $warga = Warga::with(['balita', 'ibu_hamil', 'lansia'])
            ->when($normalizedCategory, fn ($query) => $query->whereRaw("LOWER(REPLACE(kategori, ' ', '_')) = ?", [$normalizedCategory]))
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            }))
            ->orderBy('nama_lengkap')->paginate(15)->withQueryString();

        $userId = Auth::id();
        $perluPerbaikan = collect();
        $sources = [
            ['type' => 'balita', 'model' => PemeriksaanBalita::class, 'relation' => 'balita.warga', 'owner' => 'balita_id', 'correction_route' => 'balita.periksa.koreksi'],
            ['type' => 'ibu_hamil', 'model' => PemeriksaanIbuHamil::class, 'relation' => 'ibu_hamil.warga', 'owner' => 'ibu_hamil_id', 'correction_route' => 'ibu_hamil.periksa.koreksi'],
            ['type' => 'lansia', 'model' => PemeriksaanLansia::class, 'relation' => 'lansia.warga', 'owner' => 'lansia_id', 'correction_route' => 'lansia.periksa.koreksi'],
        ];
        foreach ($sources as $source) {
            $records = $source['model']::with([$source['relation']])
                ->where('submitted_by', $userId)
                ->where('verification_status', 'needs_revision')
                ->latest('updated_at')->get();
            foreach ($records as $record) {
                $ownerRelation = explode('.', $source['relation'])[0];
                $owner = $record->{$ownerRelation};
                $person = $owner?->warga;
                if (!$person) continue;
                $perluPerbaikan->push((object) [
                    'record' => $record,
                    'type' => $source['type'],
                    'label' => self::LABELS[$source['type']],
                    'member' => $person->nama_lengkap,
                    'correction_url' => route($source['correction_route'], [$owner->id, $record->id]),
                ]);
            }
        }

        return view('dashboard.kader', compact('warga', 'kategori', 'search', 'perluPerbaikan'));
    }
}
