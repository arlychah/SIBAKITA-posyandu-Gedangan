@extends('layouts.app')
@section('title', 'Jadwal Posyandu | SIBAKITA')
@section('page_title', 'Jadwal Posyandu')
@section('content')
@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
<section aria-labelledby="jadwal-heading">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h2 class="h5 mb-1" id="jadwal-heading">Agenda kegiatan</h2><p class="text-muted mb-0">Jadwal menurut kategori layanan Posyandu.</p></div>
        @if(auth()->user()->role === 'petugas')<a href="{{ route('jadwal.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Buat jadwal</a>@endif
    </div>
    @forelse($jadwal as $item)
        <article class="card mb-3 @if($item->status === 'cancelled') border-secondary @else border-start border-4 border-primary @endif">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2"><h3 class="h6 mb-0">{{ $item->judul }}</h3><span class="badge {{ $item->status === 'scheduled' ? 'bg-primary' : 'bg-secondary' }}">{{ $item->status === 'scheduled' ? 'Terjadwal' : 'Dibatalkan' }}</span><span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $item->kategori)) }}</span></div>
                    <p class="text-muted mb-1 mt-2"><i class="bi bi-geo-alt me-1"></i>{{ $item->lokasi }}</p>
                    @if($item->keterangan)<p class="mb-0">{{ $item->keterangan }}</p>@endif
                </div>
                <div class="text-md-end"><strong>{{ $item->tanggal->translatedFormat('l, d F Y') }}</strong><br><span class="text-muted">{{ substr($item->waktu_mulai, 0, 5) }}@if($item->waktu_selesai)–{{ substr($item->waktu_selesai, 0, 5) }}@endif</span></div>
                @if(auth()->user()->role === 'petugas' && $item->status === 'scheduled')
                    <div class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="{{ route('jadwal.edit', $item) }}">Ubah</a><form method="post" action="{{ route('jadwal.cancel', $item) }}" onsubmit="return confirm('Batalkan jadwal ini?')">@csrf<button class="btn btn-sm btn-outline-danger" type="submit">Batalkan</button></form></div>
                @endif
            </div>
        </article>
    @empty
        <div class="card"><div class="card-body py-5 text-center text-muted"><i class="bi bi-calendar-x fs-2 d-block mb-2" aria-hidden="true"></i>{{ auth()->user()->role === 'warga' ? 'Belum ada jadwal berikutnya untuk kategori Anda.' : 'Belum ada jadwal kegiatan.' }}</div></div>
    @endforelse
    <div class="mt-3">{{ $jadwal->links() }}</div>
</section>
@endsection
