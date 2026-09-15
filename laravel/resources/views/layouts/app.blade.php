<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | SIBAKITA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1e3a8a 0%, #084298 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85);
            border-radius: 8px;
            margin: 4px 8px;
            padding: 10px 16px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.20);
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 24px;
            display: inline-block;
        }
        .main-content {
            margin-left: 260px;
            padding: 24px;
        }
        .card-stat {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: all 0.3s;
        }
        .card-stat:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .card-stat .card-body {
            padding: 20px 24px;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: #fff;
        }
        .bg-gradient-warning {
            background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
            color: #fff;
        }
        .bg-gradient-info {
            background: linear-gradient(135deg, #56CCF2 0%, #2F80ED 100%);
            color: #fff;
        }
        .bg-gradient-danger {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            color: #fff;
        }
        .bg-gradient-purple {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            border-radius: 16px 16px 0 0 !important;
            padding: 16px 24px;
        }
        .card-body {
            padding: 24px;
        }
        .btn {
            border-radius: 10px;
            padding: 8px 20px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 14px;
            border: 1px solid #dee2e6;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
            border-color: #86b7fe;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: 600;
            color: #495057;
        }
        .badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.1rem;
        }
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
            .sidebar {
                display: none;
            }
        }
        .status-gizi-buruk { background-color: #ffe5e5; color: #b71c1c; }
        .status-gizi-kurang { background-color: #fff3e0; color: #e65100; }
        .status-gizi-normal { background-color: #e8f5e9; color: #2e7d32; }
        .status-gizi-lebih { background-color: #e3f2fd; color: #0d47a1; }
    </style>
</head>
<body>
    <div class="d-flex">
        <nav class="sidebar position-fixed flex-column">
            <div class="p-4 text-white text-center border-bottom border-white-20">
                <div class="mb-2">
                    <i class="bi bi-heart-pulse-fill" style="font-size: 40px;"></i>
                </div>
                <h5 class="mb-0 fw-bold">SIBAKITA Posyandu Gedangan</h5>
                <small class="text-white-50">Sistem Terintegrasi</small>
            </div>
            <div class="nav flex-column py-3">
                <a href="{{ url('/') }}" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <h6 class="sidebar-heading text-white-50 px-4 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">DATA WARGA</h6>
                <a href="{{ url('/warga') }}" class="nav-link">
                    <i class="bi bi-people-fill"></i> Daftar Warga
                </a>
                <a href="{{ url('/warga/tambah') }}" class="nav-link">
                    <i class="bi bi-person-plus-fill"></i> Registrasi Warga
                </a>
                <h6 class="sidebar-heading text-white-50 px-4 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">PELAYANAN</h6>
                <div class="px-3 mb-1">
                    <button class="btn btn-link text-white text-decoration-none w-100 text-start px-3 py-2" style="border-radius: 8px;" data-bs-toggle="collapse" data-bs-target="#pelayananMenu">
                        <i class="bi bi-thermometer-half me-2" style="width: 24px; display: inline-block;"></i>Pelayanan <i class="bi bi-chevron-down float-end mt-1"></i>
                    </button>
                </div>
                <div class="collapse" id="pelayananMenu">
                    <a href="{{ url('/warga?kategori=Balita') }}" class="nav-link ms-3">
                        <i class="bi bi-baby"></i> Input Pemeriksaan Balita
                    </a>
                    <a href="{{ url('/warga?kategori=Ibu+Hamil') }}" class="nav-link ms-3">
                        <i class="bi bi-person-pregnant"></i> Input Pemeriksaan Ibu Hamil
                    </a>
                    <a href="{{ url('/warga?kategori=Lansia') }}" class="nav-link ms-3">
                        <i class="bi bi-person-heart"></i> Input Pemeriksaan Lansia
                    </a>
                </div>
                <h6 class="sidebar-heading text-white-50 px-4 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">PELAPORAN</h6>
                <div class="px-3 mb-1">
                    <button class="btn btn-link text-white text-decoration-none w-100 text-start px-3 py-2" style="border-radius: 8px;" data-bs-toggle="collapse" data-bs-target="#laporanMenu">
                        <i class="bi bi-bar-chart-line-fill me-2" style="width: 24px; display: inline-block;"></i>Pelaporan <i class="bi bi-chevron-down float-end mt-1"></i>
                    </button>
                </div>
                <div class="collapse" id="laporanMenu">
                    <a href="{{ url('/laporan') }}" class="nav-link ms-3">
                        <i class="bi bi-file-earmark-text"></i> Rekap Semua
                    </a>
                    <a href="{{ url('/laporan/balita') }}" class="nav-link ms-3">
                        <i class="bi bi-baby"></i> Laporan Balita
                    </a>
                    <a href="{{ url('/laporan/ibu_hamil') }}" class="nav-link ms-3">
                        <i class="bi bi-person-pregnant"></i> Laporan Ibu Hamil
                    </a>
                    <a href="{{ url('/laporan/lansia') }}" class="nav-link ms-3">
                        <i class="bi bi-person-heart"></i> Laporan Lansia
                    </a>
                </div>
                <div class="mt-4 mx-3 p-3 rounded" style="background-color: rgba(255,255,255,0.1);">
                    <small class="text-white-50 d-block mb-1"><i class="bi bi-info-circle"></i> Kompatibel dengan:</small>
                    <small class="text-white d-block fw-bold">SATUSEHAT & ASIK</small>
                </div>
            </div>
        </nav>
        <main class="main-content flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-4" style="padding-top: 16px;">
                <div>
                    <h4 class="mb-0 text-primary">
                        <i class="bi bi-chevron-right me-2"></i>@yield('page_title', 'Dashboard')
                    </h4>
                </div>
                <div class="d-flex align-items-center">
                    <span class="text-muted me-3 small">
                        <i class="bi bi-calendar-event-fill"></i>
                        @php
                            $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                            $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            $tgl = $hari[date('w')].', '.date('d').' '.$bulan[date('n')].' '.date('Y');
                            echo $tgl;
                        @endphp
                    </span>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('extra_js')
</body>
</html>
