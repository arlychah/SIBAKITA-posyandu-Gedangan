@extends('layouts.app')
@section('title', 'Pemeriksaan Ibu Hamil - ' . $ibu_hamil->warga->nama_lengkap)
@section('page_title', 'ANC & Layanan Ibu Hamil dan Nifas')
@section('content')
<div class="row g-4">
    <div class="col-lg-3">
        <div class="card mb-4 bg-gradient-warning border-0" style="color: #333;">
            <div class="card-body py-4 text-center">
                <div class="bg-white bg-opacity-30 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-person-pregnant" style="font-size: 40px;"></i>
                </div>
                <h5 class="mb-0">{{ $ibu_hamil->warga->nama_lengkap }}</h5>
                <small>{{ $ibu_hamil->warga->nik }}</small>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-50 me-1">
                        @php $umur = date('Y') - date('Y', strtotime($ibu_hamil->warga->tanggal_lahir)); @endphp
                        {{ $umur }} Tahun
                    </span>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle text-warning me-2"></i>Suami / Data Dukung</h6>
            </div>
            <div class="card-body small">
                <p class="mb-2"><strong>Nama Suami:</strong> {{ $ibu_hamil->nama_suami ?: '-' }}</p>
                <p class="mb-2"><strong>Puskesmas:</strong> {{ $ibu_hamil->warga->puskesmas }}</p>
                <p class="mb-0"><strong>Pustu:</strong> {{ $ibu_hamil->warga->pustu }}</p>
            </div>
        </div>
        <a href="{{ url("/warga/{$ibu_hamil->warga->id}") }}" class="btn btn-outline-secondary w-100 mb-3">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Detail
        </a>
    </div>
    <div class="col-lg-9">
        <div class="card mb-4">
            <div class="card-header bg-warning border-0">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-30 rounded-3 p-2 me-3">
                        <i class="bi bi-heart-pulse-fill" style="font-size: 20px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Form Pemeriksaan Antenatal Care (ANC)</h5>
                        <small>Pemeriksaan kehamilan, TTD, dan layanan nifas sesuai standar Kemenkes</small>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ url("/ibu_hamil/periksa/{$ibu_hamil->id}") }}">
                @csrf
                <div class="card-body">
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-warning"><i class="bi bi-calendar-week me-2"></i>Identitas Pemeriksaan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Tanggal Pemeriksaan</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Kehamilan Ke-</label>
                                    <input type="number" name="kehamilan_ke" min="1" max="10" class="form-control" placeholder="contoh: 1">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Usia Kehamilan (Minggu)</label>
                                    <input type="number" name="usia_kehamilan" min="1" max="42" class="form-control" placeholder="contoh: 28">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-warning"><i class="bi bi-rulers me-2"></i>Parameter Pengukuran & Tanda Vital</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Berat Badan (kg)</label>
                                    <input type="number" step="0.1" name="berat_badan" class="form-control" placeholder="contoh: 65.5">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Tinggi Fundus (cm)</label>
                                    <input type="number" step="0.5" name="tinggi_fundus" class="form-control" placeholder="contoh: 28">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">TD Sistolik</label>
                                    <input type="number" name="tekanan_darah_sistolik" class="form-control" placeholder="120">
                                    <small class="text-muted">mmHg</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">TD Diastolik</label>
                                    <input type="number" name="tekanan_darah_diastolik" class="form-control" placeholder="80">
                                    <small class="text-muted">mmHg</small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold">LILA (cm)</label>
                                    <input type="number" step="0.1" name="lila" class="form-control" placeholder="contoh: 25.5">
                                    <small class="text-danger">Deteksi KEK: < 23.5</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Detak Jantung Janin (x/menit)</label>
                                    <input type="number" name="detak_jantung_janin" min="100" max="180" class="form-control" placeholder="140">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-warning"><i class="bi bi-capsule me-2"></i>TTD, Imunisasi TT & Layanan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input form-switch" type="checkbox" name="ttd_diberikan" id="ttd">
                                        <label class="form-check-label fw-bold" for="ttd">Tablet Tambah Darah (TTD) Diberikan</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Jumlah Butir TTD</label>
                                    <input type="number" name="jumlah_ttd" class="form-control" placeholder="contoh: 30">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Imunisasi Tetanus Toksoid (TT)</label>
                                    <select name="imunisasi_tt" class="form-select">
                                        <option value="">-- Belum / Tidak --</option>
                                        <option>TT1</option>
                                        <option>TT2</option>
                                        <option>TT3</option>
                                        <option>TT4</option>
                                        <option>TT5</option>
                                        <option>Td (Ulang)</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Catatan Konseling / Rujukan / Tanda Bahaya</label>
                                    <textarea name="catatan" rows="2" class="form-control" placeholder="Rujukan ke Puskesmas/Kasus BBLR/Perdarahan dll"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning border-0">
                        <i class="bi bi-info-circle me-2"></i><strong>Satu kali input, data langsung terakumulasi ke laporan Puskesmas.</strong> Data siap diintegrasikan ke platform SATUSEHAT/ASIK.
                    </div>
                </div>
                <div class="card-footer bg-light border-0 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-4">Reset</button>
                    <button type="submit" class="btn btn-warning text-dark px-5 fw-bold">
                        <i class="bi bi-save-fill me-2"></i>Simpan Pemeriksaan ANC
                    </button>
                </div>
            </form>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history text-warning me-2"></i>Riwayat ANC / Pemeriksaan</h5>
                <span class="badge bg-warning text-dark">{{ $riwayat->count() }} Kali Pemeriksaan</span>
            </div>
            <div class="card-body p-0">
                @if($riwayat->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Trisemester</th>
                                <th>BB</th>
                                <th>Tekanan Darah</th>
                                <th>LILA</th>
                                <th>TTD</th>
                                <th class="pe-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat->sortByDesc('tanggal') as $p)
                            <tr>
                                <td class="ps-4"><strong>@php $bln=['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']; echo date('d', strtotime($p->tanggal)).' '.$bln[date('n', strtotime($p->tanggal))].' '.date('Y', strtotime($p->tanggal)); @endphp</strong></td>
                                <td>
                                    @if($p->usia_kehamilan)
                                        @if($p->usia_kehamilan <= 12)<span class="badge bg-danger">Trimester 1</span>
                                        @elseif($p->usia_kehamilan <= 27)<span class="badge bg-warning text-dark">Trimester 2</span>
                                        @else<span class="badge bg-info text-white">Trimester 3</span>@endif
                                        <br><small>{{ $p->usia_kehamilan }} mg</small>
                                    @else-@endif
                                </td>
                                <td>{{ $p->berat_badan ?: '-' }} kg</td>
                                <td>
                                    @if($p->tekanan_darah_sistolik && $p->tekanan_darah_diastolik)
                                    <span class="@if($p->tekanan_darah_sistolik >= 140)text-danger fw-bold @endif">
                                        {{ $p->tekanan_darah_sistolik }}/{{ $p->tekanan_darah_diastolik }}
                                    </span>
                                    @else-@endif
                                </td>
                                <td>
                                    @if($p->lila)
                                    <span class="@if($p->lila < 23.5)text-danger fw-bold @endif">{{ $p->lila }} cm</span>
                                    @if($p->lila < 23.5)<br><small class="text-danger">Risiko KEK</small>@endif
                                    @else-@endif
                                </td>
                                <td>
                                    @if($p->ttd_diberikan)
                                    <i class="bi bi-check2-circle text-success fw-bold"></i>
                                    <small>{{ $p->jumlah_ttd ?: 0 }} butir</small><br>
                                    @if($p->imunisasi_tt)<small>{{ $p->imunisasi_tt }}</small>@endif
                                    @else-@endif
                                </td>
                                <td class="pe-4 small">{{ $p->catatan ?: '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size: 48px;"></i><br>
                    Belum ada riwayat. Isi form di atas untuk mencatat ANC pertama.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
