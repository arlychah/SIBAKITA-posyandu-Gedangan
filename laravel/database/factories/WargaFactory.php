<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warga>
 */
class WargaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nik' => fake()->numerify('################'),
            'nama_lengkap' => fake()->name(),
            'whatsapp' => fake()->numerify('08##########'),
            'puskesmas' => 'Puskesmas Gedangan',
            'pustu' => 'Pustu Gedangan',
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->date(),
            'alamat' => fake()->address(),
            'kategori' => 'Umum',
        ];
    }
}