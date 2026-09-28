@extends('layouts.app')
@section('title', 'Ubah hasil ' . $label . ' | SIBAKITA')
@section('page_title', 'Ubah hasil ' . $label)
@section('content')
<div class="alert alert-info" role="note">Perubahan petugas dicatat pada audit dan hasil akan kembali ke status menunggu verifikasi. Isi alasan perubahan.</div>
@if($errors->any())<div class="alert alert-danger" role="alert"><strong>Periksa isian.</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="card" method="post" action="{{ route('verifikasi.update', [$type, $record->id]) }}">
    @csrf
    <div class="card-body">
        <div class="mb-3"><label for="exam-date" class="form-label">Tanggal pemeriksaan</label><input id="exam-date" class="form-control" type="date" name="tanggal" required value="{{ old('tanggal', $record->tanggal->format('Y-m-d')) }}"></div>
        @if($type === 'balita')
            <div class="row g-3">
                <div class="col-md-4"><label for="exam-weight" class="form-label">Berat badan (kg)</label><input id="exam-weight" class="form-control" type="number" step="0.01" name="berat_badan" required value="{{ old('berat_badan', $record->berat_badan) }}"></div>
                <div class="col-md-4"><label for="exam-stature" class="form-label">Panjang/Tinggi (cm)</label><input id="exam-stature" class="form-control" type="number" step="0.1" name="tinggi_badan" required value="{{ old('tinggi_badan', $record->tinggi_badan) }}"></div>
                <div class="col-md-4"><label for="exam-method" class="form-label">Metode pengukuran</label><select id="exam-method" class="form-select" name="metode_pengukuran" required><option value="standing" @selected(old('metode_pengukuran', $record->metode_pengukuran) === 'standing')>Berdiri</option><option value="recumbent" @selected(old('metode_pengukuran', $record->metode_pengukuran) === 'recumbent')>Berbaring</option></select></div>
                <div class="col-md-4"><label for="exam-head" class="form-label">Lingkar kepala (cm)</label><input id="exam-head" class="form-control" type="number" step="0.1" name="lingkar_kepala" value="{{ old('lingkar_kepala', $record->lingkar_kepala) }}"></div>
            </div>
            <p class="form-text">Sumber perhitungan: Standar Pertumbuhan Anak WHO 2006. Panjang berbaring digunakan sebelum 24 bulan dan tinggi berdiri mulai 24 bulan; beda posisi dikonversi 0,7 cm.</p>
        @elseif($type === 'ibu_hamil')
            @foreach(['kehamilan_ke'=>'Kehamilan ke','usia_kehamilan'=>'Usia kehamilan (minggu)','berat_badan'=>'Berat badan (kg)','tekanan_darah_sistolik'=>'Tekanan darah sistolik','tekanan_darah_diastolik'=>'Tekanan darah diastolik','lila'=>'LILA (cm)','tinggi_fundus'=>'Tinggi fundus (cm)','detak_jantung_janin'=>'Detak jantung janin','jumlah_ttd'=>'Jumlah TTD'] as $field => $labelText)
                <div class="mb-3"><label for="field-{{ $field }}" class="form-label">{{ $labelText }}</label><input class="form-control" id="field-{{ $field }}" name="{{ $field }}" value="{{ old($field, $record->{$field}) }}" type="number" step="0.1"></div>
            @endforeach
            <div class="form-check mb-3"><input id="field-ttd" class="form-check-input" type="checkbox" name="ttd_diberikan" value="1" @checked(old('ttd_diberikan', $record->ttd_diberikan))><label for="field-ttd" class="form-check-label">TTD diberikan</label></div>
            <div class="mb-3"><label for="field-imunisasi" class="form-label">Imunisasi TT</label><input id="field-imunisasi" class="form-control" name="imunisasi_tt" value="{{ old('imunisasi_tt', $record->imunisasi_tt) }}"></div>
        @else
            @foreach(['berat_badan'=>'Berat badan (kg)','tinggi_badan'=>'Tinggi badan (cm)','tekanan_darah_sistolik'=>'Tekanan darah sistolik','tekanan_darah_diastolik'=>'Tekanan darah diastolik','gula_darah_puasa'=>'Gula darah puasa','gula_darah_sewaktu'=>'Gula darah sewaktu','kolesterol'=>'Kolesterol','asam_urat'=>'Asam urat'] as $field => $labelText)
                <div class="mb-3"><label for="field-{{ $field }}" class="form-label">{{ $labelText }}</label><input class="form-control" id="field-{{ $field }}" name="{{ $field }}" value="{{ old($field, $record->{$field}) }}" type="number" step="0.1"></div>
            @endforeach
            @foreach(['skrining_jiwa'=>'Skrining jiwa','penglihatan'=>'Penglihatan','pendengaran'=>'Pendengaran'] as $field => $labelText)
                <div class="mb-3"><label for="field-{{ $field }}" class="form-label">{{ $labelText }}</label><input class="form-control" id="field-{{ $field }}" name="{{ $field }}" value="{{ old($field, $record->{$field}) }}"></div>
            @endforeach
        @endif
        <div class="mb-3"><label for="exam-notes" class="form-label">Catatan klinis</label><textarea id="exam-notes" class="form-control" rows="3" name="catatan">{{ old('catatan', $record->catatan) }}</textarea></div>
        <div class="mb-0"><label for="edit-reason" class="form-label">Alasan perubahan <span class="text-danger">(wajib)</span></label><textarea id="edit-reason" class="form-control" rows="3" name="alasan_edit" required minlength="5" maxlength="2000">{{ old('alasan_edit') }}</textarea></div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="{{ route('verifikasi.show', [$type, $record->id]) }}">Batal</a><button class="btn btn-primary" type="submit">Simpan perubahan & minta verifikasi ulang</button></div>
</form>
@endsection
