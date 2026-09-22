<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Warga;

class BalitaFactory extends Factory
{
    protected $model = \App\Models\Balita::class;

    public function definition()
    {
        return [
            'warga_id' => Warga::factory()->balita(),
            'nama_ibu' => $this->faker->name('female'),
            'nama_ayah' => $this->faker->name('male'),
        ];
    }
}
