<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Balita;

class PemeriksaanBalitaFactory extends Factory
{
    protected $model = \App\Models\PemeriksaanBalita::class;

    public function definition()
    {
        $berat_badan = $this->faker->randomFloat(1, 2.5, 25);
        $tinggi_badan = $this->faker->randomFloat(1, 45, 120);
        $lingkar_kepala = $this->faker->optional()->randomFloat(1, 30, 55);

        $statusGiziOptions = [
            'Berat Badan Sangat Kurang (Severely Underweight)',
            'Berat Badan Kurang (Underweight)',
            'Berat Badan Normal',
            'Risiko Berat Badan Lebih',
        ];
        $statusTbuOptions = [
            'Sangat Pendek (Severely Stunted)',
            'Pendek (Stunted)',
            'Tinggi Normal',
            'Tinggi',
        ];
        $statusBbtbOptions = [
            'Gizi Buruk (Severely Wasted)',
            'Gizi Kurang (Wasted)',
            'Gizi Normal',
            'Beresiko Gizi Lebih',
            'Gizi Lebih (Overweight)',
            'Obesitas',
        ];

        return [
            'balita_id' => Balita::factory(),
            'tanggal' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'berat_badan' => $berat_badan,
            'tinggi_badan' => $tinggi_badan,
            'lingkar_kepala' => $lingkar_kepala,
            'status_gizi_bbu' => $this->faker->randomElement($statusGiziOptions),
            'status_gizi_tbu' => $this->faker->randomElement($statusTbuOptions),
            'status_gizi_bbtb' => $this->faker->randomElement($statusBbtbOptions),
            'asi_eksklusif' => $this->faker->randomElement(['Ya', 'Tidak', 'Sebagian']),
            'imunisasi_bcg' => $this->faker->boolean(70),
            'imunisasi_dpt1' => $this->faker->boolean(65),
            'imunisasi_dpt2' => $this->faker->boolean(60),
            'imunisasi_dpt3' => $this->faker->boolean(55),
            'imunisasi_polio1' => $this->faker->boolean(70),
            'imunisasi_polio2' => $this->faker->boolean(65),
            'imunisasi_polio3' => $this->faker->boolean(60),
            'imunisasi_campak' => $this->faker->boolean(50),
            'vitamin_a_bulan_ke' => $this->faker->optional()->randomElement([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]),
            'pmt_diterima' => $this->faker->optional()->randomElement([
                'PMT Pemulihan (Ruskita)',
                'PMT Tambahan',
                'Biskuit Balita',
                'Telur + Susu',
            ]),
            'catatan' => $this->faker->optional()->sentence(8),
        ];
    }
}
