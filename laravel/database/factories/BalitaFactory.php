<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Warga;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Balita>
 */
class BalitaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'warga_id' => Warga::factory(),
            'nama_ibu' => $this->faker->name('female'),
        ];
    }
}