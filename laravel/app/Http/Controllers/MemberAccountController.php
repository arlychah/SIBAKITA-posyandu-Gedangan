<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warga;
use App\Support\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MemberAccountController extends Controller
{
    public function index()
    {
        $accounts = User::with('warga')->where('role', UserRole::WARGA)->orderBy('name')->paginate(15);
        $wargaBelumTertaut = Warga::whereDoesntHave('user')->orderBy('nama_lengkap')->get();

        return view('akun-anggota.index', compact('accounts', 'wargaBelumTertaut'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'warga_id' => ['required', 'integer', 'exists:warga,id', Rule::unique('users', 'warga_id')],
        ]);

        DB::transaction(function () use ($validated) {
            $warga = Warga::whereDoesntHave('user')->lockForUpdate()->findOrFail($validated['warga_id']);
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::WARGA,
                'warga_id' => $warga->id,
            ]);
        });

        return redirect()->route('akun-anggota.index')->with('success', 'Akun anggota berhasil dibuat dan ditautkan.');
    }

    public function link(Request $request, User $user)
    {
        abort_unless($user->role === UserRole::WARGA && !$user->warga_id, 404);
        $validated = $request->validate([
            'warga_id' => ['required', 'integer', 'exists:warga,id', Rule::unique('users', 'warga_id')],
        ]);

        $warga = Warga::whereDoesntHave('user')->findOrFail($validated['warga_id']);
        $user->warga()->associate($warga);
        $user->save();

        return redirect()->route('akun-anggota.index')->with('success', 'Akun berhasil ditautkan ke profil warga.');
    }
}
