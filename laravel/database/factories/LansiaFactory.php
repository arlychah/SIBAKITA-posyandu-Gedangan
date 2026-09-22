<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Warga;

class LansiaFactory extends Factory
{
    protected $model = \App\Models\Lansia::class;

    public function definition()
    {
        return [
            'warga_id' => Warga::factory()->lansia(),
        ];
    }
}
