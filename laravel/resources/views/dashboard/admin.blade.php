@extends('layouts.app')
@section('title', 'Dashboard Admin | SIBAKITA')
@section('page_title', 'Ringkasan sistem')
@section('content')
<div class="alert alert-info" role="status"><i class="bi bi-shield-lock me-2"></i>Dashboard ini menampilkan ringkasan agregat. Akses tindakan klinis hanya tersedia bagi kader dan petugas kesehatan.</div>
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><span class="text-muted">Anggota terdaftar</span><strong class="d-block display-6">{{ $totalWarga }}</strong></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><span class="text-muted">Balita</span><strong class="d-block display-6">{{ $totalBalita }}</strong></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><span class="text-muted">Ibu hamil</span><strong class="d-block display-6">{{ $totalIbuHamil }}</strong></div></div></div>
    <div class="col-md-3"><div class="card h-100"><div class="card-body"><span class="text-muted">Lansia</span><strong class="d-block display-6">{{ $totalLansia }}</strong></div></div></div>
</div>
<div class="card"><div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3"><div><h2 class="h5 mb-1">Kelola akun anggota</h2><p class="text-muted mb-0">Tautkan satu akun warga ke satu profil Warga.</p></div><a class="btn btn-primary" href="{{ route('akun-anggota.index') }}">Akun anggota</a></div></div>
@endsection
