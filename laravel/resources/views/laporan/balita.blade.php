@extends('layouts.app')
@section('title', 'Laporan Balita & Status Gizi')
@section('page_title', 'Laporan Balita & Status Gizi - Integrasi Puskesmas')
@section('content')
<div class="card mb-4">
    <div class="card-body">
        <form method="get" action="{{ url('/laporan/balita') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Dari Tanggal</label>
                <input type="date" name="start" class="form-control" value="{{ $start ?? '' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Sampai Tanggal</label>
                <input type="date" name="end" class="form-control" value="{{ $end ?? '' }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-info text-white w-100"><i class="bi bi-filter me-1"></i>Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ url('/laporan/balita') }}" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-clockwise me-1"></i>Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat bg-gradient-info text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1 text-uppercase small">Total Pemeriksaan</h6>
                    <h2 class="mb-0 fw-bold">{{ $total }}</h2>
                    <small>Kali Pemeriksaan</small>
                </div>
                <i class="bi bi-baby" style="font-size: 48px; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat bg-gradient-warning" style="color: #333;">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 text-uppercase small" style="color: #8b5a00;">BB Kurang / Underweight</h6>
                    <h2 class="mb-0 fw-bold">{{ $underweight }}</h2>
                    <small>Kasus BB/U < -2 SD</small>
                </div>
                <i class="bi bi-exclamation-triangle-fill" style="font-size: 48px; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat bg-gradient-danger text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1 text-uppercase small">Stunting (Pendek)</h6>
                    <h2 class="mb-0 fw-bold">{{ $stunted }}</h2>
                    <small>Kasus TB/U < -2 SD</small>
                </div>
                <i class="bi bi-person-down" style="font-size: 48px; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat bg-gradient-primary text-white">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1 text-uppercase small">Wasting (Kurang Akut)</h6>
                    <h2 class="mb-0 fw-bold">{{ $wasted }}</h2>
                    <small>Kasus BB/TB < -2 SD</small>
                </div>
                <i class="bi bi-fire" style="font-size: 48px; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-table text-primary me-2"></i>Data Pemeriksaan Balita ({{ $total }})</h5>
        <small class="text-muted"><i class="bi bi-shield-check me-1"></i>Sesuai Standar WHO/Kemenkes RI</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle small">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Nama Balita</th>
                        <th>Usia</th>
                        <th>BB / TB / LK</th>
                        <th>Status Gizi BB/U</th>
                        <th>Status Gizi TB/U</th>
                        <th>Status Gizi BB/TB</th>
                        <th>ASI / Vit A / PMT</th>
                        <th>Puskesmas</th>
                        <th class="pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $p)
                    <tr>
                        <td class="ps-4">@php $bln=['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']; echo date('d', strtotime($p->tanggal)).' '.$bln[date('n', strtotime($p->tanggal))].' '.date('Y', strtotime($p->tanggal)); @endphp</td>
                        <td>
                            <strong>{{ $p->balita->warga->nama_lengkap }}</strong><br>
                            <small class="text-muted">{{ $p->balita->warga->jenis_kelamin }}</small>
                        </td>
                        <td>
                            @php
                                $tl = $p->balita->warga->tanggal_lahir;
                                $bulan = (date('Y', strtotime($p->tanggal)) - date('Y', strtotime($tl))) * 12 + (date('n', strtotime($p->tanggal)) - date('n', strtotime($tl)));
                                if (date('j', strtotime($p->tanggal)) < date('j', strtotime($tl))) {
                                    $bulan--;
                                }
                            @endphp
                            @if($bulan >= 12)
                                {{ intval($bulan / 12) }} Th {{ $bulan % 12 }} Bln
                            @else
                                {{ $bulan }} Bulan
                            @endif
                        </td>
                        <td>
                            <small><strong>{{ $p->berat_badan }}</strong> kg<br>
                            <strong>{{ $p->tinggi_badan }}</strong> cm<br>
                            LK: {{ $p->lingkar_kepala ?: '-' }} cm</small>
                        </td>
                        <td>
                            @php
                                $c = 'status-gizi-normal';
                                if(str_contains($p->status_gizi_bbu,'Sangat')) $c = 'status-gizi-buruk';
                                elseif(str_contains($p->status_gizi_bbu,'Kurang')) $c = 'status-gizi-kurang';
                                elseif(str_contains($p->status_gizi_bbu,'Lebih')) $c = 'status-gizi-lebih';
                            @endphp
                            <span class="badge {{ $c }}">{{ $p->status_gizi_bbu }}</span>
                        </td>
                        <td>
                            @php
                                $c = 'status-gizi-normal';
                                if(str_contains($p->status_gizi_tbu,'Sangat')) $c = 'status-gizi-buruk';
                                elseif(str_contains($p->status_gizi_tbu,'Pendek')) $c = 'status-gizi-kurang';
                                elseif(!str_contains($p->status_gizi_tbu,'Normal')) $c = 'status-gizi-lebih';
                            @endphp
                            <span class="badge {{ $c }}">{{ $p->status_gizi_tbu }}</span>
                        </td>
                        <td>
                            @php
                                $c = 'status-gizi-normal';
                                if(str_contains($p->status_gizi_bbtb,'Buruk')) $c = 'status-gizi-buruk';
                                elseif(str_contains($p->status_gizi_bbtb,'Kurang')) $c = 'status-gizi-kurang';
                                elseif(str_contains($p->status_gizi_bbtb,'Lebih') || str_contains($p->status_gizi_bbtb,'Overweight') || str_contains($p->status_gizi_bbtb,'Obesitas')) $c = 'status-gizi-lebih';
                            @endphp
                            <span class="badge {{ $c }}">{{ $p->status_gizi_bbtb }}</span>
                        </td>
                        <td><small>
                            ASI: <strong>{{ $p->asi_eksklusif ?: '-' }}</strong><br>
                            Vit A: <strong>{{ $p->vitamin_a_bulan_ke ?: '-' }}</strong><br>
                            PMT: <strong>{{ $p->pmt_diterima ?: '-' }}</strong>
                        </small></td>
                        <td><small>{{ $p->balita->warga->puskesmas }}<br><strong>{{ $p->balita->warga->pustu }}</strong></small></td>
                        <td class="pe-4">
                            <a href="{{ url("/warga/{$p->balita->warga->id}") }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-bar-graph" style="font-size: 48px;"></i><br>
                            Belum ada data pemeriksaan balita pada periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
