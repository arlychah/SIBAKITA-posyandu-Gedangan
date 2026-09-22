<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\IbuHamil;

class PemeriksaanIbuHamilFactory extends Factory
{
    protected $model = \App\Models\PemeriksaanIbuHamil::class;

    public function definition()
    {
        $kehamilan_ke = $this->faker->numberBetween(1, 5);
        $usia_kehamilan = $this->faker->numberBetween(4, 40);
        $berat_badan = $this->faker->randomFloat(1, 40, 95);
        $ttd_diberikan = $this->faker->boolean(85);

        return [
            'ibu_hamil_id' => IbuHamil::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-10 months', 'now'),
            'kehamilan_ke' => $kehamilan_ke,
            'usia_kehamilan' => $usia_kehamilan,
            'berat_badan' => $berat_badan,
            'tekanan_darah_sistolik' => $this->faker->numberBetween(90, 150),
            'tekanan_darah_diastolik' => $this->faker->numberBetween(60, 100),
            'lila' => $this->faker->randomFloat(1, 18, 35),
            'tinggi_fundus' => $usia_kehamilan > 12 ? $this->faker->randomFloat(0, 10, $usia_kehamilan + 4) : null,
            'detak_jantung_janin' => $usia_kehamilan > 10 ? $this->faker->numberBetween(110, 170) : null,
            'ttd_diberikan' => $ttd_diberikan,
            'jumlah_ttd' => $ttd_diberikan ? $this->faker->numberBetween(30, 90) : null,
            'imunisasi_tt' => $this->faker->optional()->randomElement(['TT1', 'TT2', 'TT3', 'TT4', 'TT5', 'Booster']),
            'catatan' => $this->faker->optional()->sentence(10),
        ];
    }
}
