@extends('layouts.app')
@section('title', 'Dashboard Kader | SIBAKITA')
@section('page_title', 'Entri pemeriksaan')
@section('content')
@if($perluPerbaikan->isNotEmpty())
    <div class="alert alert-warning" role="status">
        <strong><i class="bi bi-arrow-return-left me-2"></i>{{ $perluPerbaikan->count() }} pemeriksaan perlu diperbaiki.</strong>
        <span>Petugas mengembalikan hasil berikut beserta catatan koreksi.</span>
    </div>
    <div class="table-responsive card mb-4">
        <table class="table table-hover align-middle mb-0">
            <caption class="visually-hidden">Pemeriksaan kader yang dikembalikan untuk koreksi</caption>
            <thead><tr><th scope="col">Tanggal</th><th scope="col">Anggota</th><th scope="col">Jenis</th><th scope="col">Alasan</th><th scope="col">Aksi</th></tr></thead>
            <tbody>
            @foreach($perluPerbaikan as $entry)
                <tr>
                    <td>{{ $entry->record->tanggal->format('d M Y') }}</td>
                    <td>{{ $entry->member }}</td>
                    <td>{{ $entry->label }}</td>
                    <td>{{ $entry->record->return_reason }}</td>
                    <td><a class="btn btn-warning btn-sm" href="{{ $entry->correction_url }}">Perbaiki hasil</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

<section aria-labelledby="worklist-heading">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
        <div><h2 class="h5 mb-1" id="worklist-heading">Pilih anggota untuk dicatat</h2><p class="text-muted mb-0">Pemeriksaan baru tersimpan sebagai menunggu verifikasi.</p></div>
        <form method="get" action="{{ route('dashboard.kader') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label for="filter-kategori" class="form-label mb-1">Kategori</label>
                <select id="filter-kategori" name="kategori" class="form-select">
                    <option value="">Semua kategori</option>
                    <option value="balita" @selected($kategori === 'balita')>Balita</option>
                    <option value="ibu_hamil" @selected($kategori === 'ibu_hamil')>Ibu hamil</option>
                    <option value="lansia" @selected($kategori === 'lansia')>Lansia</option>
                </select>
            </div>
            <div class="col-auto">
                <label for="search-warga" class="form-label mb-1">Cari nama/NIK</label>
                <input id="search-warga" name="search" class="form-control" value="{{ $search }}" type="search" placeholder="Nama atau NIK">
            </div>
            <div class="col-auto"><button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i>Cari</button></div>
        </form>
    </div>

    <div class="table-responsive card">
        <table class="table table-hover align-middle mb-0">
            <caption class="visually-hidden">Daftar anggota Posyandu untuk input pemeriksaan</caption>
            <thead><tr><th scope="col">Nama anggota</th><th scope="col">Kategori</th><th scope="col">Puskesmas / Posyandu</th><th scope="col">Aksi pemeriksaan</th></tr></thead>
            <tbody>
            @forelse($warga as $person)
                <tr>
                    <td><strong>{{ $person->nama_lengkap }}</strong><br><small class="text-muted">NIK {{ $person->nik }}</small></td>
                    <td>{{ $person->kategori }}</td>
                    <td>{{ $person->puskesmas }}<br><small class="text-muted">{{ $person->pustu }}</small></td>
                    <td>
                        @if($person->balita)<a class="btn btn-info btn-sm text-white" href="{{ route('balita.periksa', $person->balita->id) }}">Catat pemeriksaan Balita</a>
                        @elseif($person->ibu_hamil)<a class="btn btn-warning btn-sm" href="{{ route('ibu_hamil.periksa', $person->ibu_hamil->id) }}">Catat pemeriksaan Ibu Hamil</a>
                        @elseif($person->lansia)<a class="btn btn-success btn-sm text-white" href="{{ route('lansia.periksa', $person->lansia->id) }}">Catat pemeriksaan Lansia</a>
                        @else<span class="text-muted">Profil pemeriksaan belum tersedia</span>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada anggota yang cocok dengan pencarian.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $warga->links() }}</div>
</section>
@endsection
