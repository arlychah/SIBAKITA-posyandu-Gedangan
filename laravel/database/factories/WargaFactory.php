<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WargaFactory extends Factory
{
    protected $model = \App\Models\Warga::class;

    public function definition()
    {
        $kategori = $this->faker->randomElement(['Balita', 'Ibu Hamil', 'Lansia']);
        $jenis_kelamin = $this->faker->randomElement(['Laki-laki', 'Perempuan']);

        $tanggal_lahir = match($kategori) {
            'Balita' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'Ibu Hamil' => $this->faker->dateTimeBetween('-40 years', '-18 years'),
            'Lansia' => $this->faker->dateTimeBetween('-80 years', '-60 years'),
        };

        $puskesmasOptions = [
            'Puskesmas Kecamatan A',
            'Puskesmas Kecamatan B',
            'Puskesmas Kecamatan C',
            'Puskesmas Pembantu 1',
            'Puskesmas Pembantu 2',
        ];

        return [
            'nik' => $this->faker->unique()->numerify('################'),
            'nama_lengkap' => $kategori === 'Ibu Hamil'
                ? $this->faker->name('female')
                : ($kategori === 'Balita'
                    ? $this->faker->name()
                    : $this->faker->name()),
            'whatsapp' => '08' . $this->faker->numerify('##########'),
            'puskesmas' => $this->faker->randomElement($puskesmasOptions),
            'pustu' => $this->faker->randomElement([
                'Posyandu Melati I',
                'Posyandu Melati II',
                'Posyandu Anggrek',
                'Posyandu Dahlia',
                'Posyandu Cempaka',
            ]),
            'jenis_kelamin' => $kategori === 'Ibu Hamil' ? 'Perempuan' : $jenis_kelamin,
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $tanggal_lahir,
            'golongan_darah' => $this->faker->randomElement(['A', 'B', 'AB', 'O', 'Tidak Tahu']),
            'agama' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu']),
            'status_perkawinan' => $kategori === 'Ibu Hamil'
                ? $this->faker->randomElement(['Kawin'])
                : $this->faker->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            'pekerjaan' => $kategori === 'Balita'
                ? 'Belum Bekerja'
                : $this->faker->randomElement(['IRT', 'PNS', 'TNI/Polri', 'Karyawan Swasta', 'Wiraswasta', 'Petani', 'Buruh', 'Pelajar/Mahasiswa', 'Tidak Bekerja']),
            'alamat' => 'Jl. ' . $this->faker->streetName() . ' No. ' . $this->faker->buildingNumber() . ', ' . $this->faker->city(),
            'kategori' => $kategori,
        ];
    }

    public function balita()
    {
        return $this->state(fn (array $attributes) => [
            'kategori' => 'Balita',
            'jenis_kelamin' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-5 years', 'now'),
        ]);
    }

    public function ibuHamil()
    {
        return $this->state(fn (array $attributes) => [
            'kategori' => 'Ibu Hamil',
            'jenis_kelamin' => 'Perempuan',
            'nama_lengkap' => $this->faker->name('female'),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-40 years', '-18 years'),
        ]);
    }

    public function lansia()
    {
        return $this->state(fn (array $attributes) => [
            'kategori' => 'Lansia',
            'tanggal_lahir' => $this->faker->dateTimeBetween('-80 years', '-60 years'),
        ]);
    }
}
