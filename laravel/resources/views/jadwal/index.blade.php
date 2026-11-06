@extends('layouts.app')
@section('title', 'Jadwal Posyandu | SIBAKITA')
@section('page_title', 'Jadwal Posyandu')
@section('content')
@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Pengajuan belum dapat diproses.</strong>
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
<section aria-labelledby="jadwal-heading">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h2 class="h5 mb-1" id="jadwal-heading">Agenda kegiatan</h2><p class="text-muted mb-0">Jadwal menurut kategori layanan Posyandu. Pengajuan kader akan ditampilkan kepada warga setelah disetujui petugas.</p></div>
        @if(in_array(auth()->user()->role, ['kader', 'petugas'], true))<a href="{{ route('jadwal.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>{{ auth()->user()->role === 'kader' ? 'Ajukan jadwal' : 'Buat jadwal' }}</a>@endif
    </div>
    @forelse($jadwal as $item)
        <article class="card mb-3 @if($item->status === 'cancelled') border-secondary @else border-start border-4 border-primary @endif">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h3 class="h6 mb-0">{{ $item->judul }}</h3>
                        @if($item->status === 'cancelled')
                            <span class="badge bg-secondary">Dibatalkan</span>
                        @elseif($item->approval_status === \App\Models\JadwalPosyandu::APPROVAL_PENDING)
                            <span class="badge bg-warning text-dark">Menunggu persetujuan</span>
                        @elseif($item->approval_status === \App\Models\JadwalPosyandu::APPROVAL_NEEDS_REVISION)
                            <span class="badge bg-danger">Perlu revisi</span>
                        @else
                            <span class="badge bg-success">Disetujui</span>
                        @endif
                        <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $item->kategori)) }}</span>
                    </div>
                    @if($item->creator)<p class="small text-muted mb-1 mt-2">Diajukan oleh {{ $item->creator->name }}</p>@endif
                    <p class="text-muted mb-1 mt-2"><i class="bi bi-geo-alt me-1"></i>{{ $item->lokasi }}</p>
                    @if($item->keterangan)<p class="mb-0">{{ $item->keterangan }}</p>@endif
                    @if($item->approval_status === \App\Models\JadwalPosyandu::APPROVAL_NEEDS_REVISION && $item->review_note)
                        <div class="alert alert-warning mt-3 mb-0"><strong>Catatan petugas:</strong> {{ $item->review_note }}</div>
                    @endif
                </div>
                <div class="text-md-end"><strong>{{ $item->tanggal->translatedFormat('l, d F Y') }}</strong><br><span class="text-muted">{{ substr($item->waktu_mulai, 0, 5) }}@if($item->waktu_selesai)–{{ substr($item->waktu_selesai, 0, 5) }}@endif</span></div>
                @if(auth()->user()->role === 'petugas' && $item->approval_status === \App\Models\JadwalPosyandu::APPROVAL_PENDING && $item->status === 'scheduled')
                    <div class="d-flex flex-column gap-2">
                        <form method="post" action="{{ route('jadwal.approve', $item) }}">@csrf<button class="btn btn-sm btn-success w-100" type="submit">Setujui jadwal</button></form>
                        <form method="post" action="{{ route('jadwal.revision', $item) }}" class="d-flex flex-column gap-2">@csrf
                            <label class="form-label small mb-0" for="review-note-{{ $item->id }}">Catatan revisi</label>
                            <textarea id="review-note-{{ $item->id }}" name="review_note" class="form-control form-control-sm" rows="2" maxlength="2000" required>{{ old('review_note') }}</textarea>
                            <button class="btn btn-sm btn-outline-warning" type="submit">Kirim catatan revisi</button>
                        </form>
                    </div>
                @elseif(auth()->user()->role === 'kader' && (int) $item->created_by === (int) auth()->id() && $item->approval_status === \App\Models\JadwalPosyandu::APPROVAL_NEEDS_REVISION)
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('jadwal.edit', $item) }}">Perbaiki dan kirim ulang</a>
                @elseif(auth()->user()->role === 'petugas' && $item->status === 'scheduled' && $item->approval_status === \App\Models\JadwalPosyandu::APPROVAL_APPROVED)
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
