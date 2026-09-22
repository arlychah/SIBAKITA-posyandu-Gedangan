<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Warga;

class IbuHamilFactory extends Factory
{
    protected $model = \App\Models\IbuHamil::class;

    public function definition()
    {
        return [
            'warga_id' => Warga::factory()->ibuHamil(),
            'nama_suami' => $this->faker->name('male'),
        ];
    }
}
