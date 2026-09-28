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
                    @if ($umur_tahun > 0)
                            {{ $umur_tahun }} Th {{ $umur_bulan % 12 }} Bln
                        @else
                            {{ $umur_bulan }} Bulan
                        @endif
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
            <form method="POST" action="{{ $correction ? route('balita.periksa.koreksi.update', [$balita->id, $existingRecord->id]) : route('balita.periksa.store', $balita->id) }}">
                @csrf
                <div class="card-body">
                    @if($correction)
                        <div class="alert alert-warning" role="status"><strong>Perlu perbaikan:</strong> {{ $existingRecord->return_reason }}</div>
                    @endif
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-info"><i class="bi bi-rulers me-2"></i>Parameter Pengukuran Antropometri</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="balita-tanggal" class="form-label fw-bold">Tanggal Pemeriksaan</label>
                                    <input id="balita-tanggal" type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $existingRecord?->tanggal?->format('Y-m-d') ?? date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="balita-berat" class="form-label fw-bold">Berat Badan (kg) <span class="text-danger">*</span></label>
                                    <input id="balita-berat" type="number" step="0.01" name="berat_badan" required class="form-control" placeholder="contoh: 12.5" value="{{ old('berat_badan', $existingRecord?->berat_badan) }}" aria-describedby="balita-berat-hint">
                                    <small class="form-text" id="balita-berat-hint">Gunakan kilogram.</small>
                                </div>
                                <div class="col-md-3">
                                    <label for="balita-stature" class="form-label fw-bold">Panjang / Tinggi (cm) <span class="text-danger">*</span></label>
                                    <input id="balita-stature" type="number" step="0.1" name="tinggi_badan" required class="form-control" placeholder="contoh: 90.5" value="{{ old('tinggi_badan', $existingRecord?->tinggi_badan) }}" aria-describedby="balita-stature-hint">
                                    <small class="form-text" id="balita-stature-hint">Panjang berbaring untuk acuan usia &lt;24 bulan; tinggi berdiri untuk usia ≥24 bulan. WHO menyesuaikan beda posisi ukur sebesar 0,7 cm.</small>
                                </div>
                                <div class="col-md-3">
                                    <label for="balita-lingkar-kepala" class="form-label fw-bold">Lingkar Kepala (cm)</label>
                                    <input id="balita-lingkar-kepala" type="number" step="0.1" name="lingkar_kepala" class="form-control" placeholder="contoh: 48.2" value="{{ old('lingkar_kepala', $existingRecord?->lingkar_kepala) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="balita-metode-ukur" class="form-label fw-bold">Metode pengukuran</label>
                                    <select id="balita-metode-ukur" name="metode_pengukuran" class="form-select" required aria-describedby="balita-metode-hint">
                                        <option value="">Pilih metode pengukuran</option>
                                        <option value="standing" @selected(old('metode_pengukuran', $existingRecord?->metode_pengukuran) === 'standing')>Berdiri (tinggi badan)</option>
                                        <option value="recumbent" @selected(old('metode_pengukuran', $existingRecord?->metode_pengukuran) === 'recumbent')>Berbaring (panjang badan)</option>
                                    </select>
                                    <small class="form-text" id="balita-metode-hint">Pilih sesuai posisi anak saat pengukuran; sistem menerapkan koreksi posisi WHO 0,7 cm bila diperlukan.</small>
                                </div>
                            </div>
                            <div class="alert alert-info mt-3 mb-0 small border-0">
                                <i class="bi bi-info-circle me-2" aria-hidden="true"></i><strong>BB/TB dihitung dengan metode LMS standar WHO 2006.</strong> Rujukan WFL digunakan sebelum 24 bulan dan WFH mulai 24 bulan; hasil di luar rentang tabel ditandai untuk evaluasi petugas.
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
                                    <label for="balita-asi" class="form-label fw-bold">Pemberian ASI Eksklusif</label>
                                    <select id="balita-asi" name="asi_eksklusif" class="form-select">
                                        <option value="">-- Pilih --</option>
                                        <option>Ya, Eksklusif 0-6 Bln</option>
                                        <option>Sebagian (MP-ASI)</option>
                                        <option>Tidak / Sudah Berhenti</option>
                                        <option>Masih Diberikan</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="balita-vitamin-a" class="form-label fw-bold">Pemberian Vitamin A (Bulan ke-)</label>
                                    <select id="balita-vitamin-a" name="vitamin_a_bulan_ke" class="form-select">
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
                            <fieldset class="border-0 p-0 m-0" aria-describedby="imunisasi-hint">
                                <legend class="form-label fw-bold mb-2"><i class="bi bi-syringe me-2" aria-hidden="true"></i>Riwayat Imunisasi Rutin</legend>
                                <p class="form-text" id="imunisasi-hint">Centang imunisasi yang sudah diberikan.</p>
                                <div class="row g-2">
                                    <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" id="imunisasi-bcg" type="checkbox" name="imunisasi_bcg" value="1" @checked(old('imunisasi_bcg', $existingRecord?->imunisasi_bcg))><label class="form-check-label fw-bold" for="imunisasi-bcg">BCG</label></div></div>
                                    <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" id="imunisasi-polio1" type="checkbox" name="imunisasi_polio1" value="1" @checked(old('imunisasi_polio1', $existingRecord?->imunisasi_polio1))><label class="form-check-label fw-bold" for="imunisasi-polio1">Polio 1</label></div></div>
                                    <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" id="imunisasi-polio2" type="checkbox" name="imunisasi_polio2" value="1" @checked(old('imunisasi_polio2', $existingRecord?->imunisasi_polio2))><label class="form-check-label fw-bold" for="imunisasi-polio2">Polio 2</label></div></div>
                                    <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" id="imunisasi-polio3" type="checkbox" name="imunisasi_polio3" value="1" @checked(old('imunisasi_polio3', $existingRecord?->imunisasi_polio3))><label class="form-check-label fw-bold" for="imunisasi-polio3">Polio 3</label></div></div>
                                    <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" id="imunisasi-dpt1" type="checkbox" name="imunisasi_dpt1" value="1" @checked(old('imunisasi_dpt1', $existingRecord?->imunisasi_dpt1))><label class="form-check-label fw-bold" for="imunisasi-dpt1">DPT-HB-Hib 1</label></div></div>
                                    <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" id="imunisasi-dpt2" type="checkbox" name="imunisasi_dpt2" value="1" @checked(old('imunisasi_dpt2', $existingRecord?->imunisasi_dpt2))><label class="form-check-label fw-bold" for="imunisasi-dpt2">DPT-HB-Hib 2</label></div></div>
                                    <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" id="imunisasi-dpt3" type="checkbox" name="imunisasi_dpt3" value="1" @checked(old('imunisasi_dpt3', $existingRecord?->imunisasi_dpt3))><label class="form-check-label fw-bold" for="imunisasi-dpt3">DPT-HB-Hib 3</label></div></div>
                                    <div class="col-md-4"><div class="form-check form-switch"><input class="form-check-input" id="imunisasi-campak" type="checkbox" name="imunisasi_campak" value="1" @checked(old('imunisasi_campak', $existingRecord?->imunisasi_campak))><label class="form-check-label fw-bold" for="imunisasi-campak">Campak / MR</label></div></div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-info"><i class="bi bi-basket2 me-2"></i>PMT & Catatan Lanjutan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="balita-pmt" class="form-label fw-bold">Pemberian Makanan Tambahan (PMT)</label>
                                    <select id="balita-pmt" name="pmt_diterima" class="form-select">
                                        <option value="">Tidak Menerima</option>
                                        <option>PMT Pemulihan (Gizi Buruk)</option>
                                        <option>PMT Pencegahan (Risiko Stunting)</option>
                                        <option>Biskuit Balita</option>
                                        <option>Telur & Susu</option>
                                        <option>Makanan Lokal</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="balita-catatan" class="form-label fw-bold">Catatan Kader / Nakes</label>
                                    <input id="balita-catatan" type="text" name="catatan" class="form-control" placeholder="Catatan rujukan, konseling, dll." value="{{ old('catatan', $existingRecord?->catatan) }}">
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
