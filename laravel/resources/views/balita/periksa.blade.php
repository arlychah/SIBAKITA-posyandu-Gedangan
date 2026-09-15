@extends('layouts.app')
@section('title', 'Pemeriksaan Balita - ' . $balita->warga->nama_lengkap)
@section('page_title', 'Pemeriksaan Antropometri & Layanan Balita')
@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        <div class="card mb-4 bg-gradient-info text-white border-0">
            <div class="card-body py-4 text-center">
                <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-baby" style="font-size: 40px;"></i>
                </div>
                <h5 class="mb-0">{{ $balita->warga->nama_lengkap }}</h5>
                <small>{{ $balita->warga->nik }}</small>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-25 me-1">
                        @if($umur_tahun > 0){{ $umur_tahun }} Th {{ $umur_bulan % 12 }} Bln
                        @else{{ $umur_bulan }} Bulan@endif
                    </span>
                    <span class="badge bg-white bg-opacity-25">{{ $balita->warga->jenis_kelamin }}</span>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle text-info me-2"></i>Data Orang Tua</h6>
            </div>
            <div class="card-body small">
                <p class="mb-2"><strong>Ibu:</strong> {{ $balita->nama_ibu ?: '-' }}</p>
                <p class="mb-0"><strong>Ayah:</strong> {{ $balita->nama_ayah ?: '-' }}</p>
            </div>
        </div>
        <a href="{{ url("/warga/{$balita->warga->id}") }}" class="btn btn-outline-secondary w-100 mb-3">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Detail
        </a>
    </div>
    <div class="col-lg-9">
        <div class="card mb-4">
            <div class="card-header bg-info text-white border-0">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 rounded-3 p-2 me-3">
                        <i class="bi bi-thermometer-half" style="font-size: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Form Pemeriksaan Balita</h5>
                        <small class="text-white-50">Antropometri & Status Gizi Otomatis (Kurva WHO/Kemenkes RI)</small>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ url("/balita/periksa/{$balita->id}") }}">
                @csrf
                <div class="card-body">
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-info"><i class="bi bi-rulers me-2"></i>Parameter Pengukuran Antropometri</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Tanggal Pemeriksaan</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Berat Badan (kg) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="berat_badan" required class="form-control" placeholder="contoh: 12.5">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.1" name="tinggi_badan" required class="form-control" placeholder="contoh: 90.5">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Lingkar Kepala (cm)</label>
                                    <input type="number" step="0.1" name="lingkar_kepala" class="form-control" placeholder="contoh: 48.2">
                                </div>
                            </div>
                            <div class="alert alert-info mt-3 mb-0 small border-0">
                                <i class="bi bi-lightbulb me-2"></i><strong>Status gizi otomatis dihitung</strong>: BB/U (Z-Score), TB/U (Z-Score), dan BB/TB berdasarkan Kurva Pertumbuhan WHO 2006 & Standar Kemenkes RI.
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-info"><i class="bi bi-cup-hot me-2"></i>ASI & Imunisasi Rutin</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pemberian ASI Eksklusif</label>
                                    <select name="asi_eksklusif" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option>Ya, Eksklusif 0-6 Bln</option>
                                        <option>Sebagian (MP-ASI)</option>
                                        <option>Tidak / Sudah Berhenti</option>
                                        <option>Masih Diberikan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pemberian Vitamin A (Bulan ke-)</label>
                                    <select name="vitamin_a_bulan_ke" class="form-select">
                                        <option value="">Tidak Diberikan</option>
                                        <option value="1">Vit A Bulan Ke-1</option>
                                        <option value="2">Vit A Bulan Ke-2</option>
                                        <option value="3">Vit A Bulan Ke-3</option>
                                        <option value="6">Vit A Bulan Ke-6</option>
                                        <option value="12">Vit A Bulan Ke-12</option>
                                        <option value="18">Vit A Bulan Ke-18</option>
                                        <option value="24">Vit A Bulan Ke-24</option>
                                    </select>
                                </div>
                            </div>
                            <label class="form-label fw-bold mb-2 d-block"><i class="bi bi-syringe me-2"></i>Riwayat Imunisasi Rutin (Centang bila sudah)</label>
                            <div class="row g-2">
                                <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input form-switch" type="checkbox" name="imunisasi_bcg"><label class="form-check-label fw-bold">BCG</label></div></div>
                                <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input form-switch" type="checkbox" name="imunisasi_polio1"><label class="form-check-label fw-bold">Polio 1</label></div></div>
                                <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input form-switch" type="checkbox" name="imunisasi_polio2"><label class="form-check-label fw-bold">Polio 2</label></div></div>
                                <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input form-switch" type="checkbox" name="imunisasi_polio3"><label class="form-check-label fw-bold">Polio 3</label></div></div>
                                <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input form-switch" type="checkbox" name="imunisasi_dpt1"><label class="form-check-label fw-bold">DPT-HB-Hib 1</label></div></div>
                                <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input form-switch" type="checkbox" name="imunisasi_dpt2"><label class="form-check-label fw-bold">DPT-HB-Hib 2</label></div></div>
                                <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input form-switch" type="checkbox" name="imunisasi_dpt3"><label class="form-check-label fw-bold">DPT-HB-Hib 3</label></div></div>
                                <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input form-switch" type="checkbox" name="imunisasi_campak"><label class="form-check-label fw-bold">Campak / MR</label></div></div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-info"><i class="bi bi-basket2 me-2"></i>PMT & Catatan Lanjutan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pemberian Makanan Tambahan (PMT)</label>
                                    <select name="pmt_diterima" class="form-select">
                                        <option value="">Tidak Menerima</option>
                                        <option>PMT Pemulihan (Gizi Buruk)</option>
                                        <option>PMT Pencegahan (Risiko Stunting)</option>
                                        <option>Biskuit Balita</option>
                                        <option>Telur & Susu</option>
                                        <option>Makanan Lokal</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Catatan Kader / Nakes</label>
                                    <input type="text" name="catatan" class="form-control" placeholder="Catatan rujukan, konseling, dll.">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
                    <button type="submit" class="btn btn-info text-white px-5">
                        <i class="bi bi-save-fill me-2"></i>Simpan & Hitung Status Gizi
                    </button>
                </div>
            </form>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history text-info me-2"></i>Riwayat Pemeriksaan</h5>
                <span class="badge bg-info text-white">{{ $riwayat->count() }} Data</span>
            </div>
            <div class="card-body p-0">
                @if($riwayat->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>BB / TB / LK</th>
                                <th>Status Gizi (BB/U)</th>
                                <th>Status Gizi (TB/U)</th>
                                <th>Status Gizi (BB/TB)</th>
                                <th class="pe-4">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat->sortByDesc('tanggal') as $p)
                            <tr>
                                <td class="ps-4"><strong>@php $bln=['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']; echo date('d', strtotime($p->tanggal)).' '.$bln[date('n', strtotime($p->tanggal))].' '.date('Y', strtotime($p->tanggal)); @endphp</strong></td>
                                <td><small>{{ $p->berat_badan }} kg / {{ $p->tinggi_badan }} cm<br>LK: {{ $p->lingkar_kepala ?: '-' }} cm</small></td>
                                <td>@php $c = 'status-gizi-normal'; if(str_contains($p->status_gizi_bbu,'Kurang')) $c='status-gizi-kurang'; if(str_contains($p->status_gizi_bbu,'Buruk') || str_contains($p->status_gizi_bbu,'Sangat')) $c='status-gizi-buruk'; if(str_contains($p->status_gizi_bbu,'Lebih')) $c='status-gizi-lebih'; @endphp<span class="badge {{$c}}">{{$p->status_gizi_bbu}}</span></td>
                                <td>@php $c = 'status-gizi-normal'; if(str_contains($p->status_gizi_tbu,'Pendek')) $c='status-gizi-kurang'; if(str_contains($p->status_gizi_tbu,'Sangat')) $c='status-gizi-buruk'; if(str_contains($p->status_gizi_tbu,'Tinggi') && !str_contains($p->status_gizi_tbu,'Normal')) $c='status-gizi-lebih'; @endphp<span class="badge {{$c}}">{{$p->status_gizi_tbu}}</span></td>
                                <td>@php $c = 'status-gizi-normal'; if(str_contains($p->status_gizi_bbtb,'Buruk')) $c='status-gizi-buruk'; elseif(str_contains($p->status_gizi_bbtb,'Kurang')) $c='status-gizi-kurang'; elseif(str_contains($p->status_gizi_bbtb,'Lebih') || str_contains($p->status_gizi_bbtb,'Obesitas') || str_contains($p->status_gizi_bbtb,'Overweight')) $c='status-gizi-lebih'; @endphp<span class="badge {{$c}}" style="font-size: 0.7rem;">{{$p->status_gizi_bbtb}}</span></td>
                                <td class="pe-4">
                                    <small>
                                        ASI: <strong>{{ $p->asi_eksklusif ?: '-' }}</strong><br>
                                        @if($p->vitamin_a_bulan_ke)Vit A: <strong>Blm ke-{{ $p->vitamin_a_bulan_ke }}</strong>@endif
                                    </small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 48px;"></i><br>
                    Belum ada riwayat pemeriksaan. Isi form di atas untuk memulai.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
