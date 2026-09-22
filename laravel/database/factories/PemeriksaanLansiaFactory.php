<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lansia;

class PemeriksaanLansiaFactory extends Factory
{
    protected $model = \App\Models\PemeriksaanLansia::class;

    public function definition()
    {
        return [
            'lansia_id' => Lansia::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'berat_badan' => $this->faker->randomFloat(1, 35, 90),
            'tinggi_badan' => $this->faker->randomFloat(1, 140, 175),
            'tekanan_darah_sistolik' => $this->faker->numberBetween(100, 180),
            'tekanan_darah_diastolik' => $this->faker->numberBetween(60, 110),
            'gula_darah_puasa' => $this->faker->optional()->randomFloat(1, 70, 250),
            'gula_darah_sewaktu' => $this->faker->optional()->randomFloat(1, 80, 300),
            'kolesterol' => $this->faker->optional()->randomFloat(1, 120, 350),
            'asam_urat' => $this->faker->optional()->randomFloat(1, 2, 10),
            'skrining_jiwa' => $this->faker->optional()->randomElement([
                'Sehat Jiwa',
                'Gangguan Ringan',
                'Gangguan Sedang',
                'Perlu Rujuk',
            ]),
            'penglihatan' => $this->faker->optional()->randomElement([
                'Normal',
                'Buram',
                'Katarak',
                'Glaukoma',
                'Memakai Kacamata',
            ]),
            'pendengaran' => $this->faker->optional()->randomElement([
                'Normal',
                'Telinga Tersumbat',
                'Gangguan Pendengaran Ringan',
                'Gangguan Pendengaran Berat',
            ]),
            'catatan' => $this->faker->optional()->sentence(10),
        ];
    }
}
