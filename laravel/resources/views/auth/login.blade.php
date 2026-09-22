<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | SIBAKITA Posyandu Gedangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #084298 0%, #1e3a8a 50%, #3b82f6 100%);
            position: relative;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -200px;
            right: -200px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, transparent 70%);
            border-radius: 50%;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -150px;
            left: -150px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            width: 100%;
            max-width: 960px;
            display: flex;
            flex-wrap: wrap;
        }
        .login-hero {
            flex: 1;
            min-width: 320px;
            background: linear-gradient(160deg, #1e40af 0%, #084298 50%, #0f172a 100%);
            color: #fff;
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .login-hero::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
        }
        .login-hero::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        .hero-content {
            position: relative;
            z-index: 1;
        }
        .logo-badge {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            backdrop-filter: blur(10px);
        }
        .logo-badge i {
            font-size: 42px;
            color: #fff;
        }
        .hero-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.3;
        }
        .hero-subtitle {
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 32px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .feature-list li {
            display: flex;
            align-items: center;
            margin-bottom: 14px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }
        .feature-list li i {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 15px;
        }
        .hero-footer {
            position: relative;
            z-index: 1;
            margin-top: 30px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        .hero-footer small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            display: block;
            line-height: 1.6;
        }
        .login-form-wrap {
            flex: 1;
            min-width: 320px;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-header {
            margin-bottom: 32px;
        }
        .form-header h2 {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }
        .form-header p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
        }
        .form-floating {
            margin-bottom: 18px;
        }
        .form-floating .form-control {
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 18px 14px 18px 45px;
            height: auto;
            font-size: 15px;
            transition: all 0.2s;
        }
        .form-floating .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .form-floating label {
            padding-left: 45px;
            color: #94a3b8;
            font-size: 14px;
        }
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            z-index: 5;
        }
        .form-floating:focus-within .input-icon {
            color: #2563eb;
        }
        .form-check-label {
            color: #475569;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: 0.3px;
            transition: all 0.3s;
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
            margin-top: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(37, 99, 235, 0.4);
            color: #fff;
        }
        .btn-login:active {
            transform: translateY(0);
        }
        .alert {
            border-radius: 12px;
            border: none;
            font-size: 14px;
            padding: 14px 18px;
        }
        .invalid-feedback {
            font-size: 13px;
            padding-left: 4px;
        }
        .credit-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02));
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            padding: 16px 18px;
            margin-top: 10px;
            backdrop-filter: blur(6px);
        }
        .credit-card strong {
            color: #fff;
            font-size: 13px;
            display: block;
            margin-bottom: 6px;
        }
        .credit-card small {
            color: rgba(255,255,255,0.75);
            font-size: 12px;
            display: block;
            line-height: 1.7;
        }
        .credit-card code {
            color: #fbbf24;
            background: rgba(0,0,0,0.2);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 12px;
        }
        @media (max-width: 768px) {
            .login-hero {
                padding: 40px 28px;
            }
            .login-form-wrap {
                padding: 40px 28px;
            }
            .hero-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-hero">
                <div class="hero-content">
                    <div class="logo-badge">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <h1 class="hero-title">Sistem Informasi Balita, Ibu Hamil & Lansia Terintegrasi</h1>
                    <p class="hero-subtitle">
                        Platform digital Posyandu Gedangan untuk memudahkan pencatatan data warga,
                        pelayanan kesehatan, dan pelaporan yang terintegrasi dengan SATUSEHAT & ASIK.
                    </p>
                    <ul class="feature-list">
                        <li>
                            <i class="bi bi-people-fill"></i>
                            <span>Manajemen Data Warga (Balita, Ibu Hamil, Lansia)</span>
                        </li>
                        <li>
                            <i class="bi bi-clipboard2-pulse-fill"></i>
                            <span>Pencatatan Pemeriksaan & Riwayat Kesehatan</span>
                        </li>
                        <li>
                            <i class="bi bi-bar-chart-line-fill"></i>
                            <span>Pelaporan & Rekap Otomatis per Kategori</span>
                        </li>
                        <li>
                            <i class="bi bi-shield-lock-fill"></i>
                            <span>Akses Aman Berdasarkan Peran (Role)</span>
                        </li>
                    </ul>
                </div>
                <div class="hero-footer">
                    <div class="credit-card">
                        <strong><i class="bi bi-key-fill me-1"></i> Akun Demo</strong>
                        <small>
                            Admin: <code>admin@sibakita.test</code><br>
                            Kader: <code>kader@sibakita.test</code><br>
                            Warga: <code>budi@sibakita.test</code>
                        </small>
                    </div>
                    <small class="mt-3 d-block">
                        <i class="bi bi-info-circle me-1"></i>
                        &copy; {{ date('Y') }} SIBAKITA Posyandu Gedangan &mdash; Untuk keperluan praktikum.
                    </small>
                </div>
            </div>
            <div class="login-form-wrap">
                @if(session('success'))
                    <div class="alert alert-success mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif
                <div class="form-header">
                    <h2>Masuk ke Sistem</h2>
                    <p>Silakan masukkan kredensial akun Anda untuk melanjutkan.</p>
                </div>
                <form method="POST" action="{{ url('/login') }}">
                    @csrf
                    <div class="form-floating position-relative">
                        <i class="bi bi-envelope-fill input-icon"></i>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            placeholder="name@example.com"
                            value="{{ old('email') }}"
                            required
                            autofocus>
                        <label for="email">Alamat Email</label>
                        @error('email')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating position-relative">
                        <i class="bi bi-lock-fill input-icon"></i>
                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Password"
                            required>
                        <label for="password">Kata Sandi</label>
                        @error('password')
                            <div class="invalid-feedback">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
