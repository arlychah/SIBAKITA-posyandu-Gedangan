<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warga;

class WargaSeeder extends Seeder
{
    public function run()
    {
        Warga::create([
            'nik' => '3515120101010001',
            'nama_lengkap' => 'Ananda Putra',
            'whatsapp' => '081234567890',
            'puskesmas' => 'Puskesmas Gedangan',
            'pustu' => 'Posyandu Melati I',
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Sidoarjo',
            'tanggal_lahir' => '2022-03-15',
            'alamat' => 'Jl. Raya Gedangan No. 123, Sidoarjo',
            'kategori' => 'balita',
        ]);

        Warga::create([
            'nik' => '3515120101010002',
            'nama_lengkap' => 'Siti Aisyah',
            'whatsapp' => '081234567891',
            'puskesmas' => 'Puskesmas Gedangan',
            'pustu' => 'Posyandu Melati I',
            'jenis_kelamin' => 'Perempuan',
            'tempat_lahir' => 'Sidoarjo',
            'tanggal_lahir' => '1995-07-20',
            'alamat' => 'Jl. Pahlawan No. 45, Sidoarjo',
            'kategori' => 'ibu_hamil',
        ]);

        Warga::create([
            'nik' => '3515120101010003',
            'nama_lengkap' => 'Mbah Slamet',
            'whatsapp' => '081234567892',
            'puskesmas' => 'Puskesmas Gedangan',
            'pustu' => 'Posyandu Dahlia',
            'jenis_kelamin' => 'Laki-laki',
            'tempat_lahir' => 'Sidoarjo',
            'tanggal_lahir' => '1950-05-10',
            'alamat' => 'Jl. Diponegoro No. 78, Sidoarjo',
            'kategori' => 'lansia',
        ]);

        Warga::create([
            'nik' => '3515120101010004',
            'nama_lengkap' => 'Mbah Siti Maryam',
            'whatsapp' => '081234567893',
            'puskesmas' => 'Puskesmas Porong',
            'pustu' => 'Posyandu Anggrek',
            'jenis_kelamin' => 'Perempuan',
            'tempat_lahir' => 'Porong',
            'tanggal_lahir' => '1955-11-22',
            'alamat' => 'Jl. Kenangan No. 10, Porong',
            'kategori' => 'lansia',
        ]);

        Warga::factory(30)->create();
    }
}
