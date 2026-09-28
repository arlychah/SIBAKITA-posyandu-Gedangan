@extends('layouts.app')
@section('title', 'Dashboard Petugas | SIBAKITA')
@section('page_title', 'Tinjauan pemeriksaan')
@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6"><a href="{{ route('verifikasi.index', ['status' => 'pending']) }}" class="card text-decoration-none h-100"><div class="card-body"><span class="text-muted">Menunggu verifikasi</span><strong class="d-block display-6 text-primary">{{ $menungguVerifikasi }}</strong><span class="small">Periksa hasil entri kader</span></div></a></div>
    <div class="col-md-6"><a href="{{ route('verifikasi.index', ['status' => 'needs_revision']) }}" class="card text-decoration-none h-100"><div class="card-body"><span class="text-muted">Dikembalikan untuk perbaikan</span><strong class="d-block display-6 text-warning">{{ $perluPerbaikan }}</strong><span class="small">Pantau koreksi dari kader</span></div></a></div>
</div>
<section aria-labelledby="agenda-petugas-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0" id="agenda-petugas-heading">Jadwal mendatang</h2>
        <a href="{{ route('jadwal.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Buat jadwal</a>
    </div>
    @forelse($jadwalMendatang as $jadwal)
        <article class="card mb-2"><div class="card-body d-flex flex-wrap justify-content-between gap-2"><div><strong>{{ $jadwal->judul }}</strong><div class="text-muted">{{ $jadwal->lokasi }} · {{ ucfirst(str_replace('_', ' ', $jadwal->kategori)) }}</div></div><div class="text-md-end">{{ $jadwal->tanggal->format('d M Y') }} · {{ substr($jadwal->waktu_mulai, 0, 5) }}</div></div></article>
    @empty
        <div class="card"><div class="card-body text-muted">Belum ada jadwal kegiatan mendatang.</div></div>
    @endforelse
</section>
@endsection
