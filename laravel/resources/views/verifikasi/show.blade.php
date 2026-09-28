@extends('layouts.app')
@section('title', 'Periksa hasil ' . $label . ' | SIBAKITA')
@section('page_title', 'Periksa hasil ' . $label)
@section('content')
@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
<section class="card mb-4" aria-labelledby="review-summary-heading">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2"><h2 class="h5 mb-0" id="review-summary-heading">{{ $record->balita->warga->nama_lengkap ?? $record->ibu_hamil->warga->nama_lengkap ?? $record->lansia->warga->nama_lengkap ?? 'Anggota' }}</h2><span class="badge bg-secondary">{{ \App\Support\VerificationStatus::labels()[$record->verification_status] }}</span></div>
    <div class="card-body">
        <p class="text-muted">{{ $record->tanggal->format('d M Y') }} · Dientri oleh {{ optional($record->submitter)->name ?? 'Akun kader tidak tersedia' }}</p>
        <div class="table-responsive"><table class="table table-sm"><caption class="visually-hidden">Nilai pemeriksaan yang dikirim kader</caption><tbody>
        @foreach($record->getAttributes() as $field => $value)
            @continue(in_array($field, ['id','created_at','updated_at','verification_status','submitted_by','verified_by','verified_at','return_reason','catatan'], true))
            <tr><th scope="row">{{ ucwords(str_replace('_', ' ', $field)) }}</th><td>{{ is_null($value) ? '—' : (string) $value }}</td></tr>
        @endforeach
        @if($record->catatan)<tr><th scope="row">Catatan</th><td>{{ $record->catatan }}</td></tr>@endif
        @if($type === 'balita')<tr><th scope="row">Status BB/TB WHO</th><td>{{ $record->status_gizi_bbtb ?? 'Belum dihitung' }}@if($record->z_score_bbtb !== null) · z {{ number_format($record->z_score_bbtb, 2) }} ({{ $record->rujukan_bbtb }})@endif</td></tr>@endif
        </tbody></table></div>
        @if($record->return_reason)<div class="alert alert-warning mb-0"><strong>Alasan perbaikan:</strong> {{ $record->return_reason }}</div>@endif
    </div>
    @if($record->verification_status !== 'verified')
        <div class="card-footer bg-white d-flex flex-wrap gap-2 justify-content-end">
            <a class="btn btn-outline-primary" href="{{ route('verifikasi.edit', [$type, $record->id]) }}"><i class="bi bi-pencil me-1" aria-hidden="true"></i>Ubah dengan alasan</a>
            @if($record->verification_status === 'pending')
                <form method="post" action="{{ route('verifikasi.approve', [$type, $record->id]) }}">@csrf<button class="btn btn-success" type="submit"><i class="bi bi-check2-circle me-1" aria-hidden="true"></i>Verifikasi hasil</button></form>
                <form method="post" action="{{ route('verifikasi.return', [$type, $record->id]) }}" class="d-flex gap-2">@csrf<label class="visually-hidden" for="return-reason">Alasan perbaikan wajib diisi</label><input class="form-control" id="return-reason" name="alasan" required minlength="5" maxlength="2000" placeholder="Alasan pengembalian"><button class="btn btn-warning text-dark" type="submit">Kembalikan untuk perbaikan</button></form>
            @endif
        </div>
    @endif
</section>
<section class="card" aria-labelledby="audit-heading">
    <div class="card-header"><h2 class="h6 mb-0" id="audit-heading">Riwayat tindakan</h2></div>
    <div class="list-group list-group-flush">
        @forelse($auditLogs as $log)
            <div class="list-group-item"><strong>{{ ucfirst($log->aksi) }}</strong> · {{ optional($log->actor)->name ?? 'Pengguna tidak tersedia' }} · <span class="text-muted">{{ $log->created_at->format('d M Y H:i') }}</span>@if($log->alasan)<p class="mb-1">{{ $log->alasan }}</p>@endif@if($log->sebelum)<details class="small"><summary>Lihat nilai sebelum perubahan</summary><pre class="mt-2 mb-0">{{ json_encode($log->sebelum, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></details>@endif</div>
        @empty<div class="list-group-item text-muted">Belum ada tindakan tercatat.</div>@endforelse
    </div>
</section>
@endsection
