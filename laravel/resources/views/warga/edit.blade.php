@extends('layouts.app')
@section('title','Edit Data Warga - '.$warga->nama_lengkap)
@section('page_title', 'Edit Data Identitas Warga')
@section('content')
<div class="row">
    <div class="col-xl-8 mx-auto">
        <div class="card">
            <div class="card-header bg-info text-white border-0">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Ubah Data Warga</h5>
            </div>
            <form method="post" action="{{ url('/warga/'.$warga->id.'/edit') }}">
                @csrf
                <div class="card-body">
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-primary"><i class="bi bi-credit-card-2-front-fill me-2"></i>Data Identitas Dasar</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">NIK <span class="text-danger">*</span></label>
                                    <input type="text" name="nik" maxlength="16" required class="form-control" value="{{ old('nik', $warga->nik) }}" pattern="[0-9]{16}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" required class="form-control" value="{{ old('nama_lengkap', $warga->nama_lengkap) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jenis_kelamin" required class="form-select">
                                        <option value="Laki-laki" @selected(old('jenis_kelamin',$warga->jenis_kelamin)=='Laki-laki')>Laki-laki</option>
                                        <option value="Perempuan" @selected(old('jenis_kelamin',$warga->jenis_kelamin)=='Perempuan')>Perempuan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $warga->tempat_lahir) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_lahir" required class="form-control" value="{{ old('tanggal_lahir', date('Y-m-d', strtotime($warga->tanggal_lahir))) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                                    <select name="kategori" required class="form-select" id="kategoriSelect">
                                        <option value="Balita" @selected(old('kategori',$warga->kategori)=='Balita')>Bayi & Balita</option>
                                        <option value="Ibu Hamil" @selected(old('kategori',$warga->kategori)=='Ibu Hamil')>Ibu Hamil & Nifas</option>
                                        <option value="Lansia" @selected(old('kategori',$warga->kategori)=='Lansia')>Lansia & PTM</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Nomor WhatsApp <span class="text-danger">*</span></label>
                                    <input type="tel" name="whatsapp" required class="form-control" value="{{ old('whatsapp', $warga->whatsapp) }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Alamat Domisili</label>
                                    <textarea name="alamat" rows="2" class="form-control">{{ old('alamat', $warga->alamat) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 border">
                        <div class="card-header bg-light">
                            <h6 class="mb-0 text-primary"><i class="bi bi-hospital-fill me-2"></i>Data Puskesmas & Pustu</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Puskesmas Domisili <span class="text-danger">*</span></label>
                                    <select name="puskesmas" required class="form-select">
                                        <option @selected(old('puskesmas',$warga->puskesmas)=='Puskesmas Kecamatan A')>Puskesmas Kecamatan A</option>
                                        <option @selected(old('puskesmas',$warga->puskesmas)=='Puskesmas Kecamatan B')>Puskesmas Kecamatan B</option>
                                        <option @selected(old('puskesmas',$warga->puskesmas)=='Puskesmas Kecamatan C')>Puskesmas Kecamatan C</option>
                                        <option @selected(old('puskesmas',$warga->puskesmas)=='Puskesmas Pembantu 1')>Puskesmas Pembantu 1</option>
                                        <option @selected(old('puskesmas',$warga->puskesmas)=='Puskesmas Pembantu 2')>Puskesmas Pembantu 2</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pustu Domisili <span class="text-danger">*</span></label>
                                    <input type="text" name="pustu" required class="form-control" value="{{ old('pustu', $warga->pustu) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="extraFields">
                        <div class="card border balita-field" style="@if(old('kategori',$warga->kategori) != 'Balita') display:none; @endif">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-baby me-2"></i>Data Orang Tua Balita</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama Ibu</label>
                                        <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu', $warga->balita->nama_ibu ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama Ayah</label>
                                        <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah', $warga->balita->nama_ayah ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border ibu-hamil-field mt-4" style="@if(old('kategori',$warga->kategori) != 'Ibu Hamil') display:none; @endif">
                            <div class="card-header bg-warning">
                                <h6 class="mb-0"><i class="bi bi-person-pregnant me-2"></i>Data Suami</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nama Suami</label>
                                        <input type="text" name="nama_suami" class="form-control" value="{{ old('nama_suami', $warga->ibu_hamil->nama_suami ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 d-flex justify-content-between">
                    <a href="{{ url('/warga/'.$warga->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-info text-white px-5">
                        <i class="bi bi-save-fill me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('extra_js')
<script>
document.getElementById('kategoriSelect').addEventListener('change', function() {
    const val = this.value;
    document.querySelectorAll('.balita-field').forEach(e => e.style.display = val === 'Balita' ? 'block' : 'none');
    document.querySelectorAll('.ibu-hamil-field').forEach(e => e.style.display = val === 'Ibu Hamil' ? 'block' : 'none');
});
</script>
@endpush
