@extends('layouts.app')
@section('title','Detail Warga - '.$warga->nama_lengkap)
@section('page_title', 'Detail Identitas Warga')
@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <div class="bg-@if($warga->jenis_kelamin == 'Laki-laki')primary @else info @endif bg-opacity-10 text-@if($warga->jenis_kelamin == 'Laki-laki')primary @else info @endif rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 120px; height: 120px;">
                    <i class="bi bi-person-fill" style="font-size: 64px;"></i>
                </div>
                <h4 class="mb-1">{{ $warga->nama_lengkap }}</h4>
                <p class="text-muted mb-2"><code>{{ $warga->nik }}</code></p>
                @if($warga->kategori == 'Balita')
                <span class="badge bg-info text-white fs-6"><i class="bi bi-baby me-1"></i>Balita</span>
                @elseif($warga->kategori == 'Ibu Hamil')
                <span class="badge bg-warning text-dark fs-6"><i class="bi bi-person-pregnant me-1"></i>Ibu Hamil & Nifas</span>
                @elseif($warga->kategori == 'Lansia')
                <span class="badge bg-success fs-6"><i class="bi bi-person-heart me-1"></i>Lansia & PTM</span>
                @endif
            </div>
        </div>
        <div class="d-grid gap-2 mb-4">
            @if($warga->kategori == 'Balita' && $warga->balita)
            <a href="{{ url('/balita/periksa/'.$warga->balita->id) }}" class="btn btn-info btn-lg text-white">
                <i class="bi bi-thermometer-half me-2"></i>Input Pemeriksaan Balita
            </a>
            @elseif($warga->kategori == 'Ibu Hamil' && $warga->ibu_hamil)
            <a href="{{ url('/ibu_hamil/periksa/'.$warga->ibu_hamil->id) }}" class="btn btn-warning btn-lg text-dark">
                <i class="bi bi-thermometer-half me-2"></i>Input Pemeriksaan Ibu Hamil
            </a>
            @elseif($warga->kategori == 'Lansia' && $warga->lansia)
            <a href="{{ url('/lansia/periksa/'.$warga->lansia->id) }}" class="btn btn-success btn-lg text-white">
                <i class="bi bi-thermometer-half me-2"></i>Input Pemeriksaan Lansia
            </a>
            @endif
            <div class="d-flex gap-2">
                <a href="{{ url('/warga/'.$warga->id.'/edit') }}" class="btn btn-outline-secondary flex-grow-1">
                    <i class="bi bi-pencil-square me-1"></i>Edit Data
                </a>
                <button class="btn btn-outline-danger" onclick="confirmDelete({{ $warga->id }})">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Info Kontak</h6>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="bi bi-whatsapp text-success me-2"></i>WhatsApp</span>
                        <a href="https://wa.me/{{ $warga->whatsapp }}" target="_blank" class="text-primary text-decoration-none"><strong>{{ $warga->whatsapp }}</strong></a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="bi bi-hospital me-2"></i>Puskesmas</span>
                        <strong class="text-end">{{ $warga->puskesmas }}</strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted"><i class="bi bi-house-heart me-2"></i>Pustu</span>
                        <strong class="text-end">{{ $warga->pustu }}</strong>
                    </li>
                    <li class="list-group-item px-0">
                        <span class="text-muted"><i class="bi bi-geo-alt me-2"></i>Alamat</span>
                        <p class="mb-0 mt-1"><strong>{{ $warga->alamat ?? '-' }}</strong></p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-lines-fill text-primary me-2"></i>Data Identitas Lengkap</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">NIK (Validasi Nasional)</div>
                    <div class="col-sm-8"><code><strong>{{ $warga->nik }}</strong></code></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Nama Lengkap</div>
                    <div class="col-sm-8"><strong>{{ $warga->nama_lengkap }}</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Tempat, Tanggal Lahir</div>
                    <div class="col-sm-8"><strong>
                        @php
                            $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            echo ($warga->tempat_lahir ?? '-').', '.date('d',strtotime($warga->tanggal_lahir)).' '.$bulan[date('n',strtotime($warga->tanggal_lahir))].' '.date('Y',strtotime($warga->tanggal_lahir));
                        @endphp
                    </strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Usia Saat Ini</div>
                    <div class="col-sm-8">
                        <strong>{{ $warga->umur_formatted }}</strong>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Jenis Kelamin</div>
                    <div class="col-sm-8">
                        <span class="badge @if($warga->jenis_kelamin == 'Laki-laki')bg-primary @else bg-info @endif">{{ $warga->jenis_kelamin }}</span>
                    </div>
                </div>
                @if($warga->kategori == 'Balita' && $warga->balita)
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Nama Ibu</div>
                    <div class="col-sm-8"><strong>{{ $warga->balita->nama_ibu ?? '-' }}</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Nama Ayah</div>
                    <div class="col-sm-8"><strong>{{ $warga->balita->nama_ayah ?? '-' }}</strong></div>
                </div>
                @elseif($warga->kategori == 'Ibu Hamil' && $warga->ibu_hamil)
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Nama Suami</div>
                    <div class="col-sm-8"><strong>{{ $warga->ibu_hamil->nama_suami ?? '-' }}</strong></div>
                </div>
                @endif
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted">Terdaftar Pada</div>
                    <div class="col-sm-8"><strong>
                        @php
                            $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            $tgl = $warga->created_at;
                            echo date('d',strtotime($tgl)).' '.$bulan[date('n',strtotime($tgl))].' '.date('Y',strtotime($tgl)).', '.date('H:i',strtotime($tgl));
                        @endphp
                    </strong></div>
                </div>
            </div>
        </div>
        @if($warga->kategori == 'Balita' && $warga->balita)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pemeriksaan Balita</h5>
                <span class="badge bg-info text-white">{{ count($warga->balita->riwayat) }} Kali Pemeriksaan</span>
            </div>
            <div class="card-body p-0">
                @if(count($warga->balita->riwayat) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>BB (kg)</th>
                                <th>TB (cm)</th>
                                <th>LK (cm)</th>
                                <th>Status Gizi</th>
                                <th class="pe-4">ASI / Imunisasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warga->balita->riwayat->sortByDesc('tanggal') as $p)
                            <tr>
                                <td class="ps-4"><strong>{{ date('d-m-Y', strtotime($p->tanggal)) }}</strong></td>
                                <td>{{ $p->berat_badan }}</td>
                                <td>{{ $p->tinggi_badan }}</td>
                                <td>{{ $p->lingkar_kepala ?? '-' }}</td>
                                <td>
                                    @php
                                        $cls = '';
                                        if (str_contains($p->status_gizi_bbtb, 'Buruk')) {
                                            $cls = 'status-gizi-buruk';
                                        } elseif (str_contains($p->status_gizi_bbtb, 'Kurang')) {
                                            $cls = 'status-gizi-kurang';
                                        } elseif (str_contains($p->status_gizi_bbtb, 'Normal')) {
                                            $cls = 'status-gizi-normal';
                                        } else {
                                            $cls = 'status-gizi-lebih';
                                        }
                                    @endphp
                                    <span class="badge {{ $cls }}" style="font-size: 0.75rem;">{{ $p->status_gizi_bbtb }}</span>
                                </td>
                                <td class="pe-4">
                                    <small>
                                        ASI: <strong>{{ $p->asi_eksklusif ?? '-' }}</strong><br>
                                        Vit A: <strong>{{ $p->vitamin_a_bulan_ke ?? '-' }}</strong>
                                        @if($p->imunisasi_campak)| <i class="bi bi-check2-circle text-success"></i> Campak @endif
                                    </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-medical" style="font-size: 48px;"></i><br>
                    Belum ada riwayat pemeriksaan. <a href="{{ url('/balita/periksa/'.$warga->balita->id) }}" class="text-info">Mulai pemeriksaan pertama</a>
                </div>
                @endif
            </div>
        </div>
        @elseif($warga->kategori == 'Ibu Hamil' && $warga->ibu_hamil)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pemeriksaan ANC</h5>
                <span class="badge bg-warning text-dark">{{ count($warga->ibu_hamil->riwayat) }} Kali Pemeriksaan</span>
            </div>
            <div class="card-body p-0">
                @if(count($warga->ibu_hamil->riwayat) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Usia Kehamilan</th>
                                <th>BB</th>
                                <th>Tekanan Darah</th>
                                <th>LILA</th>
                                <th>TTD / TT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warga->ibu_hamil->riwayat->sortByDesc('tanggal') as $p)
                            <tr>
                                <td class="ps-4"><strong>{{ date('d-m-Y', strtotime($p->tanggal)) }}</strong></td>
                                <td>{{ $p->usia_kehamilan ?? '-' }} Minggu</td>
                                <td>{{ $p->berat_badan ?? '-' }} kg</td>
                                <td>
                                    @if($p->tekanan_darah_sistolik && $p->tekanan_darah_diastolik)
                                    <span class="@if($p->tekanan_darah_sistolik >= 140)text-danger fw-bold @endif">
                                        {{ $p->tekanan_darah_sistolik }}/{{ $p->tekanan_darah_diastolik }}
                                    </span>
                                    @else - @endif
                                </td>
                                <td>
                                    @if($p->lila)
                                    <span class="@if($p->lila < 23.5)text-warning fw-bold @endif">{{ $p->lila }} cm</span>
                                    @else - @endif
                                </td>
                                <td>
                                    @if($p->ttd_diberikan)<i class="bi bi-check2-circle text-success"></i> {{ $p->jumlah_ttd ?? 0 }} butir @else - @endif
                                    @if($p->imunisasi_tt)| {{ $p->imunisasi_tt }} @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-medical" style="font-size: 48px;"></i><br>
                    Belum ada riwayat pemeriksaan ANC. <a href="{{ url('/ibu_hamil/periksa/'.$warga->ibu_hamil->id) }}" class="text-warning">Mulai pemeriksaan pertama</a>
                </div>
                @endif
            </div>
        </div>
        @elseif($warga->kategori == 'Lansia' && $warga->lansia)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pemeriksaan Lansia & PTM</h5>
                <span class="badge bg-success">{{ count($warga->lansia->riwayat) }} Kali Pemeriksaan</span>
            </div>
            <div class="card-body p-0">
                @if(count($warga->lansia->riwayat) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Tekanan Darah</th>
                                <th>Gula Darah</th>
                                <th>Kolesterol</th>
                                <th>Asam Urat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($warga->lansia->riwayat->sortByDesc('tanggal') as $p)
                            <tr>
                                <td class="ps-4"><strong>{{ date('d-m-Y', strtotime($p->tanggal)) }}</strong></td>
                                <td>
                                    @if($p->tekanan_darah_sistolik && $p->tekanan_darah_diastolik)
                                    <span class="@if($p->tekanan_darah_sistolik >= 140)text-danger fw-bold @endif">
                                        {{ $p->tekanan_darah_sistolik }}/{{ $p->tekanan_darah_diastolik }}
                                    </span>
                                    @else - @endif
                                </td>
                                <td>
                                    @if($p->gula_darah_puasa)Puasa: {{ $p->gula_darah_puasa }} @endif
                                    @if($p->gula_darah_sewaktu) | Sewaktu: {{ $p->gula_darah_sewaktu }} @endif
                                    @if(!$p->gula_darah_puasa && !$p->gula_darah_sewaktu) - @endif
                                </td>
                                <td>{{ $p->kolesterol ?? '-' }} mg/dL</td>
                                <td>{{ $p->asam_urat ?? '-' }} mg/dL</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-file-medical" style="font-size: 48px;"></i><br>
                    Belum ada riwayat pemeriksaan. <a href="{{ url('/lansia/periksa/'.$warga->lansia->id) }}" class="text-success">Mulai pemeriksaan pertama</a>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center py-5">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 80px; height: 80px;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 40px;"></i>
                </div>
                <h5>Hapus Data Warga?</h5>
                <p class="text-muted">Seluruh data dan riwayat pemeriksaan akan dihapus permanen.</p>
                <form id="deleteForm" method="post" class="d-flex gap-3 justify-content-center mt-4">
                    @csrf
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4"><i class="bi bi-trash-fill me-2"></i>Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('extra_js')
<script>
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
function confirmDelete(id) {
    document.getElementById('deleteForm').action = '/warga/' + id + '/hapus';
    deleteModal.show();
}
</script>
@endpush
