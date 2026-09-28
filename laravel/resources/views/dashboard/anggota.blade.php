@extends('layouts.app')
@section('title', 'Dashboard Anggota | SIBAKITA')
@section('page_title', 'Jadwal & hasil pemeriksaan')
@section('content')
@if(!$warga)
    <div class="alert alert-warning" role="status">
        <h5 class="alert-heading"><i class="bi bi-link-45deg me-2"></i>Akun belum terhubung ke data warga</h5>
        <p class="mb-0">Hubungi petugas Posyandu agar akun ini ditautkan ke profil anggota yang benar.</p>
    </div>
@else
    <p class="text-muted mb-4">Informasi untuk <strong>{{ $warga->nama_lengkap }}</strong> · {{ $warga->kategori }}</p>

    <section class="mb-4" aria-labelledby="jadwal-heading">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0" id="jadwal-heading"><i class="bi bi-calendar-event text-primary me-2"></i>Jadwal Posyandu</h2>
            <a class="btn btn-outline-primary btn-sm" href="{{ route('jadwal.index') }}">Lihat semua</a>
        </div>
        @forelse($jadwalBerikutnya as $jadwal)
            <article class="card mb-3 border-start border-4 border-primary">
                <div class="card-body d-flex flex-wrap justify-content-between gap-3">
                    <div>
                        <h3 class="h6 mb-1">{{ $jadwal->judul }}</h3>
                        <p class="text-muted mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $jadwal->lokasi }}</p>
                        @if($jadwal->keterangan)<p class="small mb-0 mt-2">{{ $jadwal->keterangan }}</p>@endif
                    </div>
                    <div class="text-md-end">
                        <strong>{{ $jadwal->tanggal->translatedFormat('l, d F Y') }}</strong><br>
                        <span class="text-muted">{{ substr($jadwal->waktu_mulai, 0, 5) }}@if($jadwal->waktu_selesai)–{{ substr($jadwal->waktu_selesai, 0, 5) }}@endif</span>
                        <span class="badge bg-light text-primary ms-2">{{ ucfirst(str_replace('_', ' ', $jadwal->kategori)) }}</span>
                    </div>
                </div>
            </article>
        @empty
            <div class="card"><div class="card-body text-muted"><i class="bi bi-calendar-x me-2"></i>Belum ada jadwal berikutnya untuk kategori Anda.</div></div>
        @endforelse
    </section>

    <section id="hasil-pemeriksaan" aria-labelledby="hasil-heading">
        <h2 class="h5 mb-3" id="hasil-heading"><i class="bi bi-file-medical text-primary me-2"></i>Hasil pemeriksaan terverifikasi</h2>
        @if($hasil->isEmpty())
            <div class="card"><div class="card-body text-muted"><i class="bi bi-shield-check me-2"></i>Belum ada hasil yang diverifikasi petugas.</div></div>
        @else
            <div class="table-responsive card">
                <table class="table table-hover align-middle mb-0">
                    <caption class="visually-hidden">Riwayat pemeriksaan yang telah diverifikasi petugas</caption>
                    <thead><tr><th scope="col">Tanggal</th><th scope="col">Jenis</th><th scope="col">Ringkasan hasil</th><th scope="col">Status</th></tr></thead>
                    <tbody>
                    @foreach($hasil as $record)
                        <tr>
                            <td>{{ $record->tanggal->format('d M Y') }}</td>
                            <td>{{ $warga->kategori }}</td>
                            <td>
                                @if($warga->balita)
                                    BB/TB {{ $record->status_gizi_bbtb ?: 'Menunggu evaluasi' }}
                                    @if($record->z_score_bbtb !== null)<span class="text-muted">(z {{ number_format($record->z_score_bbtb, 2) }})</span>@endif
                                @elseif($warga->ibu_hamil)
                                    ANC · usia kehamilan {{ $record->usia_kehamilan ?? '—' }} minggu
                                @else
                                    Tekanan darah {{ $record->tekanan_darah_sistolik ?? '—' }}/{{ $record->tekanan_darah_diastolik ?? '—' }} mmHg
                                @endif
                            </td>
                            <td><span class="badge bg-success">Terverifikasi</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endif
@endsection
