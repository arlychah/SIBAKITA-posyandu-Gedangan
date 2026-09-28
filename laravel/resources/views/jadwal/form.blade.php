@extends('layouts.app')
@section('title', $jadwal->exists ? 'Ubah Jadwal Posyandu' : 'Buat Jadwal Posyandu')
@section('page_title', $jadwal->exists ? 'Ubah jadwal' : 'Buat jadwal')
@section('content')
<section class="card" aria-labelledby="jadwal-form-heading">
    <div class="card-header"><h2 class="h5 mb-0" id="jadwal-form-heading">{{ $jadwal->exists ? 'Ubah jadwal Posyandu' : 'Jadwal kegiatan Posyandu' }}</h2></div>
    <form method="post" action="{{ $action }}">
        @csrf
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger" role="alert"><strong>Periksa kembali isian jadwal.</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div class="row g-3">
                <div class="col-md-8"><label for="jadwal-judul" class="form-label">Nama kegiatan</label><input id="jadwal-judul" name="judul" class="form-control" maxlength="160" required value="{{ old('judul', $jadwal->judul) }}"></div>
                <div class="col-md-4"><label for="jadwal-kategori" class="form-label">Sasaran kategori</label><select id="jadwal-kategori" name="kategori" class="form-select" required>@foreach(['balita'=>'Balita','ibu_hamil'=>'Ibu Hamil','lansia'=>'Lansia','semua'=>'Semua anggota'] as $value => $label)<option value="{{ $value }}" @selected(old('kategori', $jadwal->kategori ?: 'semua') === $value)>{{ $label }}</option>@endforeach</select></div>
                <div class="col-md-4"><label for="jadwal-tanggal" class="form-label">Tanggal</label><input id="jadwal-tanggal" type="date" name="tanggal" class="form-control" required value="{{ old('tanggal', $jadwal->tanggal?->format('Y-m-d')) }}"></div>
                <div class="col-md-4"><label for="jadwal-mulai" class="form-label">Waktu mulai</label><input id="jadwal-mulai" type="time" name="waktu_mulai" class="form-control" required value="{{ old('waktu_mulai', $jadwal->waktu_mulai) }}"></div>
                <div class="col-md-4"><label for="jadwal-selesai" class="form-label">Waktu selesai <span class="text-muted">(opsional)</span></label><input id="jadwal-selesai" type="time" name="waktu_selesai" class="form-control" value="{{ old('waktu_selesai', $jadwal->waktu_selesai) }}"></div>
                <div class="col-md-12"><label for="jadwal-lokasi" class="form-label">Lokasi Posyandu</label><input id="jadwal-lokasi" name="lokasi" class="form-control" maxlength="180" required value="{{ old('lokasi', $jadwal->lokasi) }}"></div>
                <div class="col-md-12"><label for="jadwal-keterangan" class="form-label">Keterangan layanan <span class="text-muted">(opsional)</span></label><textarea id="jadwal-keterangan" name="keterangan" class="form-control" rows="3" maxlength="2000">{{ old('keterangan', $jadwal->keterangan) }}</textarea></div>
            </div>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="{{ route('jadwal.index') }}">Batal</a><button class="btn btn-primary" type="submit">Simpan jadwal</button></div>
    </form>
</section>
@endsection
