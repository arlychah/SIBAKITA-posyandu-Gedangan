@extends('layouts.app')
@section('title', 'Verifikasi Pemeriksaan | SIBAKITA')
@section('page_title', 'Verifikasi pemeriksaan')
@section('content')
<section aria-labelledby="queue-heading">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
        <div><h2 class="h5 mb-1" id="queue-heading">Antrean hasil pemeriksaan</h2><p class="text-muted mb-0">Tinjau data kader sebelum hasil dibagikan kepada anggota.</p></div>
        <form method="get" action="{{ route('verifikasi.index') }}" class="d-flex flex-wrap gap-2">
            <div><label for="filter-status" class="form-label small mb-1">Status</label><select id="filter-status" name="status" class="form-select"><option value="pending" @selected($status === 'pending')>Menunggu verifikasi</option><option value="needs_revision" @selected($status === 'needs_revision')>Perlu perbaikan</option><option value="verified" @selected($status === 'verified')>Terverifikasi</option></select></div>
            <div><label for="filter-jenis" class="form-label small mb-1">Jenis</label><select id="filter-jenis" name="jenis" class="form-select"><option value="">Semua jenis</option><option value="balita" @selected($type === 'balita')>Balita</option><option value="ibu_hamil" @selected($type === 'ibu_hamil')>Ibu hamil</option><option value="lansia" @selected($type === 'lansia')>Lansia</option></select></div>
            <button class="btn btn-primary align-self-end" type="submit">Terapkan</button>
        </form>
    </div>
    <div class="table-responsive card">
        <table class="table table-hover align-middle mb-0">
            <caption class="visually-hidden">Antrean pemeriksaan yang perlu ditinjau</caption>
            <thead><tr><th scope="col">Tanggal</th><th scope="col">Anggota</th><th scope="col">Kategori</th><th scope="col">Jenis pemeriksaan</th><th scope="col">Pengentry</th><th scope="col">Status</th><th scope="col">Aksi</th></tr></thead>
            <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->record->tanggal->format('d M Y') }}</td>
                    <td>{{ $item->member }}</td><td>{{ $item->category }}</td><td>{{ $item->label }}</td><td>{{ $item->submitted_by_name }}</td>
                    <td><span class="badge {{ $item->record->verification_status === 'verified' ? 'bg-success' : ($item->record->verification_status === 'needs_revision' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ \App\Support\VerificationStatus::labels()[$item->record->verification_status] }}</span></td>
                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('verifikasi.show', [$item->type, $item->record->id]) }}">Periksa hasil</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5"><i class="bi bi-inbox fs-2 d-block mb-2" aria-hidden="true"></i>Tidak ada hasil dengan status ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
