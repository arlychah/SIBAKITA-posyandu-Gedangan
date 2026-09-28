<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WargaController extends Controller
{
    public function index(Request $r)
    {
        $kategori = $r->input('kategori', '');
        $search = $r->input('search', '');

        $query = Warga::with(['balita', 'ibu_hamil', 'lansia']);

        if ($kategori) {
            $kategoriNormal = strtolower(str_replace(' ', '_', $kategori));
            $query->whereRaw("LOWER(REPLACE(kategori, ' ', '_')) = ?", [$kategoriNormal]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'LIKE', "%{$search}%")
                    ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        $wargaList = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('warga.index', compact('wargaList', 'kategori', 'search'));
    }

    public function create()
    {
        return view('warga.create');
    }

    public function store(Request $r)
    {
        $r->validate([
            'nik' => 'required|digits:16|unique:warga,nik',
            'nama_lengkap' => 'required|string|max:200',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'kategori' => 'required|string',
            'whatsapp' => 'required|string|max:15',
            'puskesmas' => 'required|string|max:150',
            'pustu' => 'required|string|max:150',
        ]);

        DB::beginTransaction();
        try {
            $warga = Warga::create([
                'nik' => trim($r->nik),
                'nama_lengkap' => trim($r->nama_lengkap),
                'whatsapp' => trim($r->whatsapp),
                'puskesmas' => trim($r->puskesmas),
                'pustu' => trim($r->pustu),
                'jenis_kelamin' => $r->jenis_kelamin,
                'tempat_lahir' => trim($r->tempat_lahir ?? ''),
                'tanggal_lahir' => $r->tanggal_lahir,
                'alamat' => trim($r->alamat ?? ''),
                'kategori' => $r->kategori,
            ]);

            if ($r->kategori == 'Balita') {
                Balita::create([
                    'warga_id' => $warga->id,
                    'nama_ibu' => trim($r->nama_ibu ?? ''),
                    'nama_ayah' => trim($r->nama_ayah ?? ''),
                ]);
            } elseif ($r->kategori == 'Ibu Hamil') {
                IbuHamil::create([
                    'warga_id' => $warga->id,
                    'nama_suami' => trim($r->nama_suami ?? ''),
                ]);
            } elseif ($r->kategori == 'Lansia') {
                Lansia::create([
                    'warga_id' => $warga->id,
                ]);
            }

            DB::commit();
            return redirect('/warga')->with('success', 'Data warga berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $warga = Warga::with([
            'balita.riwayat',
            'ibu_hamil.riwayat',
            'lansia.riwayat'
        ])->findOrFail($id);

        return view('warga.show', compact('warga'));
    }

    public function edit($id)
    {
        $warga = Warga::with(['balita', 'ibu_hamil', 'lansia'])->findOrFail($id);
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $r, $id)
    {
        $r->validate([
            'nik' => 'required|digits:16|unique:warga,nik,' . $id,
            'nama_lengkap' => 'required|string|max:200',
            'jenis_kelamin' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'kategori' => 'required|string',
            'whatsapp' => 'required|string|max:15',
            'puskesmas' => 'required|string|max:150',
            'pustu' => 'required|string|max:150',
        ]);

        DB::beginTransaction();
        try {
            $warga = Warga::findOrFail($id);
            $oldKategori = trim($warga->kategori);
            $newKategori = trim($r->kategori);

            $mapKategori = static function ($val){
                $v = strtolower(trim($val));
                if (strpos($v, 'balita') !== false) return 'Balita';
                if (strpos($v, 'ibu') !== false) return 'Ibu Hamil';
                if (strpos($v, 'lansia') !== false) return 'Lansia';
                return $val;
            };

            $warga->update([
                'nik' => trim($r->nik),
                'nama_lengkap' => trim($r->nama_lengkap),
                'whatsapp' => trim($r->whatsapp),
                'puskesmas' => trim($r->puskesmas),
                'pustu' => trim($r->pustu),
                'jenis_kelamin' => $r->jenis_kelamin,
                'tempat_lahir' => trim($r->tempat_lahir ?? ''),
                'tanggal_lahir' => $r->tanggal_lahir,
                'alamat' => trim($r->alamat ?? ''),
                'kategori' => $newKategori,
            ]);

            if ($oldKategori != $newKategori) {
                if ($oldKategori == 'Balita' || $oldKategori == 'balita') { $warga->balita()?->delete(); }
                elseif ($oldKategori == 'Ibu Hamil' || $oldKategori == 'ibu_hamil' || $oldKategori == 'ibu hamil') { $warga->ibu_hamil()?->delete(); }
                elseif ($oldKategori == 'Lansia' || $oldKategori == 'lansia') { $warga->lansia()?->delete(); }

                if ($newKategori == 'Balita') {
                    Balita::updateOrCreate(
                        ['warga_id' => $warga->id],
                        [
                            'nama_ibu' => trim($r->nama_ibu ?? ''),
                            'nama_ayah' => trim($r->nama_ayah ?? ''),
                        ]
                    );
                } elseif ($newKategori == 'Ibu Hamil') {
                    IbuHamil::updateOrCreate(
                        ['warga_id' => $warga->id],
                        [
                            'nama_suami' => trim($r->nama_suami ?? ''),
                        ]
                    );
                } elseif ($newKategori == 'Lansia') {
                    Lansia::firstOrCreate(['warga_id' => $warga->id]);
                }
            } else {
                if ($newKategori == 'Balita') {
                    Balita::updateOrCreate(
                        ['warga_id' => $warga->id],
                        [
                            'nama_ibu' => trim($r->nama_ibu ?? ''),
                            'nama_ayah' => trim($r->nama_ayah ?? ''),
                        ]
                    );
                } elseif ($newKategori == 'Ibu Hamil') {
                    IbuHamil::updateOrCreate(
                        ['warga_id' => $warga->id],
                        [
                            'nama_suami' => trim($r->nama_suami ?? ''),
                        ]
                    );
                } elseif ($newKategori == 'Lansia') {
                    Lansia::firstOrCreate(['warga_id' => $warga->id]);
                }
            }

            DB::commit();
            return redirect("/warga/{$id}")->with('success', 'Data warga berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $warga = Warga::findOrFail($id);

            Balita::where('warga_id', $id)->delete();
            IbuHamil::where('warga_id', $id)->delete();
            Lansia::where('warga_id', $id)->delete();

            $warga->delete();

            DB::commit();
            return back()->with('success', 'Data warga berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
