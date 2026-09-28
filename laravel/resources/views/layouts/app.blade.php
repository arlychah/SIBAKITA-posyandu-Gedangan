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
        }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #1e3a8a 0%, #084298 100%);
            z-index: 1040;
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
            min-width: 0;
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
        @media (max-width: 767.98px) {
            .main-content {
                width: 100%;
                min-width: 0;
                margin-left: 0;
                padding: 16px;
            }
            .sidebar {
                display: flex;
                top: 0;
                bottom: 0;
                left: 0;
                width: min(84vw, 320px);
                min-height: 100vh;
                min-height: 100dvh;
                height: 100vh;
                height: 100dvh;
                overflow-y: auto;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
                box-shadow: 0 12px 32px rgba(15, 23, 42, 0.24);
            }
            .sidebar.mobile-nav-open {
                transform: translateX(0);
            }
            .mobile-nav-toggle {
                display: inline-flex;
                width: 44px;
                height: 44px;
                flex: 0 0 44px;
                align-items: center;
                justify-content: center;
                padding: 0;
            }
            .mobile-nav-close {
                position: absolute;
                top: 12px;
                right: 12px;
                z-index: 1;
                display: inline-flex;
                width: 44px;
                height: 44px;
                align-items: center;
                justify-content: center;
                padding: 0;
            }
            .mobile-nav-backdrop {
                position: fixed;
                inset: 0;
                z-index: 1030;
                border: 0;
                background: rgba(15, 23, 42, 0.48);
            }
            .mobile-nav-backdrop[hidden] {
                display: none;
            }
            .page-header {
                flex-wrap: nowrap;
                align-items: flex-start;
            }
            .page-header__title-group {
                min-width: 0;
                flex: 1 1 auto;
            }
            .page-header__profile {
                flex: 0 0 auto;
            }
            .main-content .row,
            .main-content .card,
            .main-content .table-responsive {
                min-width: 0;
                max-width: 100%;
            }
            .main-content .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
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
        <button class="mobile-nav-backdrop" type="button" aria-label="Tutup menu navigasi" hidden></button>
        <nav class="sidebar position-fixed flex-column" id="appSidebar" aria-label="Navigasi utama">
            <button class="btn btn-sm btn-outline-light mobile-nav-close d-md-none" type="button" aria-label="Tutup menu navigasi">
                <i class="bi bi-x-lg" aria-hidden="true"></i>
            </button>
            <div class="p-4 text-white text-center border-bottom border-white-20">
                <div class="mb-2">
                    <i class="bi bi-heart-pulse-fill" style="font-size: 40px;"></i>
                </div>
                <h5 class="mb-0 fw-bold">SIBAKITA Posyandu Gedangan</h5>
                <small class="text-white-50">Sistem Terintegrasi</small>
            </div>
            <div class="nav flex-column py-3">
                @php($currentRole = Auth::user()->role)
                <a href="{{ route('dashboard') }}" class="nav-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                @if($currentRole === 'warga')
                    <a href="{{ route('jadwal.index') }}" class="nav-link">
                        <i class="bi bi-calendar-event"></i> Jadwal Posyandu
                    </a>
                    <a href="{{ route('dashboard.anggota') }}#hasil-pemeriksaan" class="nav-link">
                        <i class="bi bi-file-medical"></i> Hasil pemeriksaan
                    </a>
                @endif

                @if(in_array($currentRole, ['kader', 'petugas'], true))
                    <h6 class="sidebar-heading text-white-50 px-4 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">DATA WARGA</h6>
                    <a href="{{ route('warga.index') }}" class="nav-link">
                        <i class="bi bi-people-fill"></i> Daftar Warga
                    </a>
                @endif

                @if($currentRole === 'petugas')
                    <a href="{{ route('warga.create') }}" class="nav-link">
                        <i class="bi bi-person-plus-fill"></i> Registrasi Warga
                    </a>
                    <h6 class="sidebar-heading text-white-50 px-4 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">TINJAUAN</h6>
                    <a href="{{ route('verifikasi.index') }}" class="nav-link">
                        <i class="bi bi-clipboard-check"></i> Verifikasi pemeriksaan
                    </a>
                    <a href="{{ route('jadwal.index') }}" class="nav-link">
                        <i class="bi bi-calendar-event"></i> Jadwal Posyandu
                    </a>
                @endif

                @if($currentRole === 'kader')
                    <h6 class="sidebar-heading text-white-50 px-4 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">INPUT PEMERIKSAAN</h6>
                    <a href="{{ route('dashboard.kader') }}?kategori=balita" class="nav-link"><i class="bi bi-baby"></i> Balita</a>
                    <a href="{{ route('dashboard.kader') }}?kategori=ibu_hamil" class="nav-link"><i class="bi bi-person-pregnant"></i> Ibu hamil</a>
                    <a href="{{ route('dashboard.kader') }}?kategori=lansia" class="nav-link"><i class="bi bi-person-heart"></i> Lansia</a>
                @endif

                @if(in_array($currentRole, ['admin', 'petugas'], true))
                    <h6 class="sidebar-heading text-white-50 px-4 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">AKUN</h6>
                    <a href="{{ route('akun-anggota.index') }}" class="nav-link">
                        <i class="bi bi-person-vcard"></i> Akun anggota
                    </a>
                @endif

                @if($currentRole === 'petugas')
                    <h6 class="sidebar-heading text-white-50 px-4 mt-4 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">PELAPORAN</h6>
                    <div class="px-3 mb-1">
                        <button class="btn btn-link text-white text-decoration-none w-100 text-start px-3 py-2" style="border-radius: 8px;" data-bs-toggle="collapse" data-bs-target="#laporanMenu" aria-expanded="false" aria-controls="laporanMenu">
                            <i class="bi bi-bar-chart-line-fill me-2" style="width: 24px; display: inline-block;"></i>Laporan <i class="bi bi-chevron-down float-end mt-1"></i>
                        </button>
                    </div>
                    <div class="collapse" id="laporanMenu">
                        <a href="{{ route('laporan.index') }}" class="nav-link ms-3"><i class="bi bi-file-earmark-text"></i> Rekap semua</a>
                        <a href="{{ route('laporan.balita') }}" class="nav-link ms-3"><i class="bi bi-baby"></i> Laporan Balita</a>
                        <a href="{{ route('laporan.ibu-hamil') }}" class="nav-link ms-3"><i class="bi bi-person-pregnant"></i> Laporan Ibu Hamil</a>
                        <a href="{{ route('laporan.lansia') }}" class="nav-link ms-3"><i class="bi bi-person-heart"></i> Laporan Lansia</a>
                    </div>
                @endif

                <div class="mx-3 mt-4 p-3 rounded-3" style="background: rgba(255,255,255,0.10); backdrop-filter: blur(6px);">
                    <div class="d-flex align-items-center mb-2">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 38px; height: 38px;">
                            <i class="bi bi-person-fill fs-6"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-white fw-semibold small text-truncate">{{ Auth::user()->name ?? 'Tamu' }}</div>
                            <div class="text-white-50 small text-truncate" style="font-size: 11px;">
                                @php
                                    $role = Auth::user()->role ?? 'warga';
                                    $roleMap = ['admin' => 'Administrator', 'kader' => 'Kader Posyandu', 'warga' => 'Warga', 'petugas' => 'Petugas Kesehatan'];
                                    echo $roleMap[$role] ?? ucfirst($role);
                                @endphp
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="d-grid">
                        @csrf
                        <button type="submit" class="btn btn-sm text-white fw-semibold py-2" style="background: rgba(239, 68, 68, 0.85); border-radius: 10px; transition: all 0.2s;" onmouseover="this.style.background='rgba(220,38,38,0.95)'" onmouseout="this.style.background='rgba(239,68,68,0.85)'">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar
                        </button>
                    </form>
                </div>
                <div class="mx-3 mt-3 mb-4 p-3 rounded-3" style="background-color: rgba(255,255,255,0.08);">
                    <small class="text-white-50 d-block mb-1"><i class="bi bi-info-circle"></i> Kompatibel dengan:</small>
                    <small class="text-white d-block fw-bold">SATUSEHAT & ASIK</small>
                </div>
            </div>
        </nav>
        <main class="main-content flex-grow-1">
            <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-2" style="padding-top: 16px;">
                <div class="page-header__title-group d-flex align-items-center gap-2">
                    <button class="btn btn-primary mobile-nav-toggle d-md-none" id="mobileNavToggle" type="button" aria-controls="appSidebar" aria-expanded="false" aria-label="Buka menu navigasi">
                        <i class="bi bi-list fs-4" aria-hidden="true"></i>
                    </button>
                    <div>
                        <h4 class="mb-0 text-primary">
                            <i class="bi bi-chevron-right me-2"></i>@yield('page_title', 'Dashboard')
                        </h4>
                    </div>
                </div>
                <div class="page-header__profile d-flex align-items-center">
                    <span class="text-muted me-3 small d-none d-md-inline">
                        <i class="bi bi-calendar-event-fill"></i>
                        @php
                            $hari = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                            $bulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            $tgl = $hari[date('w')].', '.date('d').' '.$bulan[date('n')].' '.date('Y');
                            echo $tgl;
                        @endphp
                    </span>
                    <div class="dropdown">
                        <button class="btn border-0 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 42px; height: 42px;">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2" style="min-width: 240px;">
                            <li>
                                <div class="px-3 py-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;">
                                            <i class="bi bi-person-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark mb-0">{{ Auth::user()->name ?? 'Tamu' }}</div>
                                            <small class="text-muted">{{ Auth::user()->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <span class="dropdown-item-text small text-muted px-3 py-2">
                                    <i class="bi bi-person-gear me-2"></i>
                                    @php
                                        $role = Auth::user()->role ?? 'warga';
                                        $roleBadge = ['admin' => 'Administrator', 'kader' => 'Kader Posyandu', 'warga' => 'Warga', 'petugas' => 'Petugas Kesehatan Puskesmas'];
                                        echo $roleBadge[$role] ?? ucfirst($role);
                                    @endphp
                                </span>
                            </li>
                            <li><hr class="dropdown-divider mx-3 my-1"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger rounded-2 px-3 py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar dari Sistem
                                    </button>
                                </form>
                            </li>
                        </ul>
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
    <script>
        (function () {
            const sidebar = document.getElementById('appSidebar');
            const toggle = document.getElementById('mobileNavToggle');
            const backdrop = document.querySelector('.mobile-nav-backdrop');
            const closeButton = sidebar && sidebar.querySelector('.mobile-nav-close');

            if (!sidebar || !toggle || !backdrop || !closeButton) return;

            const mobileViewport = window.matchMedia('(max-width: 767.98px)');

            function closeNavigation(returnFocus) {
                sidebar.classList.remove('mobile-nav-open');
                backdrop.hidden = true;
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Buka menu navigasi');
                document.body.style.overflow = '';

                if (mobileViewport.matches) {
                    sidebar.setAttribute('aria-hidden', 'true');
                    sidebar.setAttribute('inert', '');
                }

                if (returnFocus && mobileViewport.matches) toggle.focus();
            }

            function openNavigation() {
                if (!mobileViewport.matches) return;

                sidebar.removeAttribute('inert');
                sidebar.setAttribute('aria-hidden', 'false');
                sidebar.classList.add('mobile-nav-open');
                backdrop.hidden = false;
                toggle.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-label', 'Tutup menu navigasi');
                document.body.style.overflow = 'hidden';
                closeButton.focus();
            }

            function syncNavigationState() {
                closeNavigation(false);
                if (!mobileViewport.matches) {
                    sidebar.removeAttribute('aria-hidden');
                    sidebar.removeAttribute('inert');
                }
            }

            toggle.addEventListener('click', openNavigation);
            closeButton.addEventListener('click', function () { closeNavigation(true); });
            backdrop.addEventListener('click', function () { closeNavigation(true); });
            sidebar.querySelectorAll('a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (mobileViewport.matches) closeNavigation(false);
                });
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && sidebar.classList.contains('mobile-nav-open')) {
                    closeNavigation(true);
                }
            });
            mobileViewport.addEventListener('change', syncNavigationState);
            syncNavigationState();
        })();
    </script>
</body>
</html>
