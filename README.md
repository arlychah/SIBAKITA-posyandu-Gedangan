# SIBAKITA Posyandu Gedangan

SIBAKITA adalah aplikasi web untuk membantu pengelolaan kegiatan Posyandu di wilayah Gedangan. Aplikasi ini mencatat data warga dan pemeriksaan kesehatan, mendukung proses verifikasi, serta menyediakan jadwal kegiatan dan laporan.

Proyek ini dibuat oleh **Kelompok 5**:

1. Marindra Puspitawati
2. Arlichah
3. Elok Dewi Wulan Sari

## Fitur

- Dashboard sesuai peran pengguna.
- Pengelolaan data warga dalam kategori balita, ibu hamil, dan lansia.
- Pencatatan pemeriksaan oleh kader.
- Pemeriksaan hasil oleh petugas: menyetujui, mengembalikan untuk diperbaiki, atau membatalkan dengan alasan.
- Riwayat tindakan pemeriksaan yang tersimpan dalam catatan audit.
- Pengelolaan jadwal kegiatan Posyandu.
- Laporan pemeriksaan balita, ibu hamil, dan lansia dengan penyaring tanggal.
- Penghitungan status gizi balita berdasarkan standar WHO.
- Pembatasan akses berdasarkan peran: admin, petugas, kader, dan warga.

## Teknologi

- Laravel 9
- PHP 8.0 atau lebih baru
- MySQL atau MariaDB
- Composer
- Laravel Mix dan npm untuk kompilasi aset bila diperlukan

## Persiapan

Pastikan PHP, Composer, dan MySQL/MariaDB sudah tersedia. Jika menggunakan Laragon, jalankan layanan MySQL sebelum menyiapkan aplikasi.

Buat basis data bernama `sibakita`, lalu buka PowerShell dari folder utama proyek dan jalankan:

```powershell
Set-Location .\laravel
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Buka berkas `laravel/.env` dan sesuaikan pengaturan basis data dengan instalasi lokal. Contoh pengaturan bawaan:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sibakita
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi dan pengisian data contoh:

```powershell
php artisan migrate --seed
```

Seeder akan membuat akun demo dan data contoh untuk pengembangan. Jangan gunakan akun atau data demo di lingkungan produksi.

## Menjalankan aplikasi

Dari folder utama proyek di Windows, jalankan berkas berikut:

```text
start-server.bat
```

Berkas tersebut menjalankan server Laravel di `http://127.0.0.1:8000`. Sebagai alternatif, dari folder `laravel` jalankan:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Aset Bootstrap dan Bootstrap Icons dimuat dari jsDelivr, sehingga tampilan lengkap memerlukan koneksi internet. Jika melakukan perubahan pada aset JavaScript atau CSS lokal, jalankan perintah berikut dari folder `laravel`:

```powershell
npm install
npm run dev
```

## Akun demo

Akun berikut dibuat oleh `UserSeeder` untuk penggunaan lokal:

| Peran | Email | Kata sandi |
|---|---|---|
| Admin | `admin@sibakita.test` | `admin123` |
| Petugas | `petugas@sibakita.test` | `puskesmas123` |
| Kader | `kader@sibakita.test` | `kader123` |
| Warga | `warga@sibakita.test` | `warga123` |

Ganti kata sandi dan kredensial bawaan sebelum aplikasi digunakan di lingkungan selain pengembangan.

## Pengujian

Konfigurasi pengujian menggunakan SQLite dalam memori. Jalankan seluruh tes dari folder `laravel` dengan perintah:

```powershell
php artisan test
```

Tes mencakup autentikasi dan dashboard peran, CRUD warga, alur pemeriksaan dan audit, jadwal, serta laporan.

## Struktur aplikasi

Kode aplikasi Laravel berada di folder `laravel`. Beberapa lokasi penting:

- `laravel/app/Http/Controllers` — pengendali fitur aplikasi.
- `laravel/app/Models` — model dan relasi data.
- `laravel/database/migrations` — perubahan struktur basis data.
- `laravel/database/seeders` — akun dan data contoh.
- `laravel/resources/views` — tampilan Blade.
- `laravel/routes/web.php` — rute halaman web.
- `laravel/tests` — tes otomatis.

## Dokumentasi Laravel

Dokumentasi resmi Laravel tersedia di [laravel.com/docs](https://laravel.com/docs).