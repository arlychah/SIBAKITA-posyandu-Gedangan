@extends('layouts.app')
@section('title','Dashboard - Sistem Posyandu')
@section('page_title', 'Dashboard')
@section('content')
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat bg-gradient-primary">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1 text-uppercase small">Total Warga</h6>
                    <h2 class="mb-0 fw-bold">{{ $totalWarga }}</h2>
                    <small class="text-white-75">Jiwa</small>
                </div>
                <i class="bi bi-people-fill" style="font-size: 48px; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat bg-gradient-info">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1 text-uppercase small">Balita</h6>
                    <h2 class="mb-0 fw-bold">{{ $totalBalita }}</h2>
                    <small class="text-white-75">Pemeriksaan: {{ $pemeriksaanBalita }}</small>
                </div>
                <i class="bi bi-baby" style="font-size: 48px; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat bg-gradient-warning">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1 text-uppercase small">Ibu Hamil</h6>
                    <h2 class="mb-0 fw-bold">{{ $totalIbuHamil }}</h2>
                    <small class="text-white-75">Pemeriksaan: {{ $pemeriksaanIbuHamil }}</small>
                </div>
                <i class="bi bi-person-pregnant" style="font-size: 48px; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card card-stat bg-gradient-success">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-1 text-uppercase small">Lansia</h6>
                    <h2 class="mb-0 fw-bold">{{ $totalLansia }}</h2>
                    <small class="text-white-75">Pemeriksaan: {{ $pemeriksaanLansia }}</small>
                </div>
                <i class="bi bi-person-heart" style="font-size: 48px; opacity: 0.4;"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Warga Terdaftar Terbaru</h5>
                <a href="{{ url('/warga') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-eye me-1"></i> Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">NIK</th>
                                <th>Nama</th>
                                <th>Usia</th>
                                <th>Kategori</th>
                                <th>Puskesmas</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestWarga as $w)
                            <tr>
                                <td class="ps-4"><code class="text-muted">{{ $w->nik }}</code></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-person-fill"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $w->nama_lengkap }}</strong><br>
                                            <small class="text-muted"><i class="bi bi-whatsapp text-success"></i> {{ $w->whatsapp }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $w->umur_formatted }}
                                </td>
                                <td>
                                    @if($w->kategori == 'Balita')
                                    <span class="badge bg-info text-white">{{ $w->kategori }}</span>
                                    @elseif($w->kategori == 'Ibu Hamil')
                                    <span class="badge bg-warning text-white">{{ $w->kategori }}</span>
                                    @elseif($w->kategori == 'Lansia')
                                    <span class="badge bg-success text-white">{{ $w->kategori }}</span>
                                    @else
                                    <span class="badge bg-secondary">{{ $w->kategori }}</span>
                                    @endif
                                </td>
                                <td><small>{{ $w->puskesmas }}</small></td>
                                <td class="pe-4 text-end">
                                    <div class="btn-group">
                                        <a href="{{ url('/warga/'.$w->id) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ url('/warga/'.$w->id.'/edit') }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 48px;"></i><br>
                                    Belum ada data warga. <a href="{{ url('/warga/tambah') }}" class="text-primary">Tambahkan data pertama</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-plus-circle-dotted text-primary me-2"></i>Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ url('/warga/tambah') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus-fill me-2"></i>Registrasi Warga Baru
                    </a>
                    <a href="{{ url('/warga?kategori=Balita') }}" class="btn btn-outline-info">
                        <i class="bi bi-baby me-2"></i>Pemeriksaan Balita
                    </a>
                    <a href="{{ url('/warga?kategori=Ibu+Hamil') }}" class="btn btn-outline-warning">
                        <i class="bi bi-person-pregnant me-2"></i>Pemeriksaan Ibu Hamil
                    </a>
                    <a href="{{ url('/warga?kategori=Lansia') }}" class="btn btn-outline-success">
                        <i class="bi bi-person-heart me-2"></i>Pemeriksaan Lansia
                    </a>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-check text-primary me-2"></i>Satuan Layanan</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-hospital text-info me-2"></i>Input Sekali Tersimpan</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-send text-info me-2"></i>Siap Integrasi SATUSEHAT</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-chat-dots text-info me-2"></i>Support WhatsApp Chatbot</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span><i class="bi bi-file-earmark-medical text-info me-2"></i>Kemenkes RI Ready</span>
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card bg-gradient-purple border-0">
            <div class="card-body">
                <h6><i class="bi bi-lightbulb me-2"></i><strong>Tahukah Anda?</strong></h6>
                <p class="small mb-0">Data Posyandu yang terintegrasi dapat membantu Puskesmas dalam pemantauan kesehatan wilayah dan mendukung program pencegahan stunting nasional.</p>
            </div>
        </div>
    </div>
</div>
@endsection
