@extends('layouts.app')
@section('title', 'Akun Anggota | SIBAKITA')
@section('page_title', 'Manajemen akun anggota')
@section('content')
@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert"><strong>Periksa kembali data akun.</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="row g-4">
    <div class="col-xl-5">
        <section class="card" aria-labelledby="buat-akun-heading">
            <div class="card-header"><h2 class="h5 mb-0" id="buat-akun-heading">Buat akun anggota dan tautkan profil</h2></div>
            <form method="post" action="{{ route('akun-anggota.store') }}">
                @csrf
                <div class="card-body">
                    <div class="mb-3"><label class="form-label" for="member-name">Nama akun</label><input class="form-control" id="member-name" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"></div>
                    <div class="mb-3"><label class="form-label" for="member-email">Email</label><input class="form-control" id="member-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></div>
                    <div class="mb-3"><label class="form-label" for="member-password">Kata sandi sementara</label><input class="form-control" id="member-password" type="password" name="password" required minlength="8" autocomplete="new-password"><div class="form-text" id="member-password-hint">Minimal 8 karakter; sampaikan kredensial melalui jalur aman.</div></div>
                    <div class="mb-3"><label class="form-label" for="member-password-confirmation">Ulangi kata sandi</label><input class="form-control" id="member-password-confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" aria-describedby="member-password-hint"></div>
                    <div class="mb-0"><label class="form-label" for="member-warga">Profil Warga</label><select class="form-select" id="member-warga" name="warga_id" required><option value="">Pilih profil yang belum tertaut</option>@foreach($wargaBelumTertaut as $person)<option value="{{ $person->id }}" @selected((string) old('warga_id') === (string) $person->id)>{{ $person->nama_lengkap }} · {{ $person->nik }} · {{ ucfirst(str_replace('_', ' ', strtolower($person->kategori))) }}</option>@endforeach</select><div class="form-text">Satu akun warga hanya dapat ditautkan ke satu profil, dan setiap profil hanya memiliki satu akun.</div></div>
                </div>
                <div class="card-footer bg-white text-end"><button class="btn btn-primary" type="submit">Buat dan tautkan</button></div>
            </form>
        </section>
    </div>
    <div class="col-xl-7">
        <section class="card" aria-labelledby="daftar-akun-heading">
            <div class="card-header"><h2 class="h5 mb-0" id="daftar-akun-heading">Akun warga</h2></div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">Akun anggota dan profil Warga yang tertaut</caption>
                    <thead><tr><th scope="col">Akun</th><th scope="col">Profil warga</th><th scope="col">Kategori</th><th scope="col">Tautkan profil</th></tr></thead>
                    <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <td><strong>{{ $account->name }}</strong><br><small class="text-muted">{{ $account->email }}</small></td>
                            <td>{{ $account->warga->nama_lengkap ?? 'Belum tertaut' }}@if($account->warga)<br><small class="text-muted">NIK {{ $account->warga->nik }}</small>@endif</td>
                            <td>{{ $account->warga ? ucfirst(str_replace('_', ' ', strtolower($account->warga->kategori))) : '—' }}</td>
                            <td>
                                @if(!$account->warga && $wargaBelumTertaut->isNotEmpty())
                                    <form method="post" action="{{ route('akun-anggota.link', $account) }}" class="d-flex gap-2">
                                        @csrf
                                        <label class="visually-hidden" for="link-warga-{{ $account->id }}">Pilih profil untuk {{ $account->email }}</label>
                                        <select class="form-select form-select-sm" id="link-warga-{{ $account->id }}" name="warga_id" required><option value="">Pilih profil</option>@foreach($wargaBelumTertaut as $person)<option value="{{ $person->id }}">{{ $person->nama_lengkap }} · {{ $person->nik }}</option>@endforeach</select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Tautkan</button>
                                    </form>
                                @else
                                    <span class="badge {{ $account->warga ? 'bg-success' : 'bg-secondary' }}">{{ $account->warga ? 'Tertaut' : 'Belum ada profil tersedia' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada akun warga.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $accounts->links() }}</div>
        </section>
    </div>
</div>
@endsection
