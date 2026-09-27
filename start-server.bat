@echo off
setlocal

rem Jalankan server Laravel dari folder proyek, bukan dari folder kerja Windows.
cd /d "%~dp0laravel"

if not exist "artisan" (
    echo [ERROR] File artisan tidak ditemukan di folder laravel.
    echo Pastikan file start-server.bat berada di root repository.
    pause
    exit /b 1
)

where php >nul 2>&1
if errorlevel 1 (
    echo [ERROR] PHP tidak ditemukan di PATH Windows.
    echo Pastikan PHP/Laragon sudah terpasang dan PATH sudah dikonfigurasi.
    pause
    exit /b 1
)

if not exist "vendor\autoload.php" (
    echo [ERROR] Dependensi Composer belum terpasang.
    echo Jalankan "composer install" dari folder laravel terlebih dahulu.
    pause
    exit /b 1
)

echo SIBAKITA Laravel server berjalan di http://127.0.0.1:8000
echo Tutup jendela ini atau tekan Ctrl+C untuk menghentikan server.
echo.
php artisan serve --host=127.0.0.1 --port=8000

if errorlevel 1 (
    echo.
    echo [ERROR] Server berhenti karena terjadi kesalahan.
)

pause
endlocal
