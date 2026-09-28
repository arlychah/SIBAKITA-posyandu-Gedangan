<?php

namespace App\Support;

use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\PemeriksaanLansia;
use InvalidArgumentException;

final class ExaminationRegistry
{
    private const MODELS = [
        'balita' => PemeriksaanBalita::class,
        'ibu_hamil' => PemeriksaanIbuHamil::class,
        'lansia' => PemeriksaanLansia::class,
    ];

    private const FOREIGN_KEYS = [
        'balita' => 'balita_id',
        'ibu_hamil' => 'ibu_hamil_id',
        'lansia' => 'lansia_id',
    ];

    public static function model(string $type): string
    {
        return self::MODELS[$type] ?? throw new InvalidArgumentException('Jenis pemeriksaan tidak valid.');
    }

    public static function foreignKey(string $type): string
    {
        return self::FOREIGN_KEYS[$type] ?? throw new InvalidArgumentException('Jenis pemeriksaan tidak valid.');
    }

    public static function types(): array
    {
        return array_keys(self::MODELS);
    }
}
