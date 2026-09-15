<?php

namespace App\Helpers;

class StatusGizi
{
    public static function bbu($berat, $umurBulan, $jk)
    {
        $jkCode = ($jk === 'Laki-laki') ? 'L' : 'P';
        $zScore = self::hitungZScoreBBU($berat, $umurBulan, $jkCode);
        return self::klasifikasiBBU($zScore);
    }

    public static function tbu($tinggi, $umurBulan, $jk)
    {
        $jkCode = ($jk === 'Laki-laki') ? 'L' : 'P';
        $zScore = self::hitungZScoreTBU($tinggi, $umurBulan, $jkCode);
        return self::klasifikasiTBU($zScore);
    }

    public static function bbtb($berat, $tinggi, $umurBulan, $jk)
    {
        $jkCode = ($jk === 'Laki-laki') ? 'L' : 'P';
        $zScore = self::hitungZScoreBBTB($berat, $tinggi, $jkCode);
        return self::klasifikasiBBTB($zScore);
    }

    private static function klasifikasiBBU($zScore)
    {
        if ($zScore < -3) {
            return "Berat Badan Sangat Kurang (Severely Underweight)";
        } elseif ($zScore < -2) {
            return "Berat Badan Kurang (Underweight)";
        } elseif ($zScore <= 2) {
            return "Berat Badan Normal";
        } else {
            return "Risiko Berat Badan Lebih";
        }
    }

    private static function klasifikasiTBU($zScore)
    {
        if ($zScore < -3) {
            return "Sangat Pendek (Severely Stunted)";
        } elseif ($zScore < -2) {
            return "Pendek (Stunted)";
        } elseif ($zScore <= 3) {
            return "Tinggi Normal";
        } else {
            return "Tinggi";
        }
    }

    private static function klasifikasiBBTB($zScore)
    {
        if ($zScore < -3) {
            return "Gizi Buruk (Severely Wasted)";
        } elseif ($zScore < -2) {
            return "Gizi Kurang (Wasted)";
        } elseif ($zScore <= 1) {
            return "Gizi Normal";
        } elseif ($zScore <= 2) {
            return "Beresiko Gizi Lebih";
        } elseif ($zScore <= 3) {
            return "Gizi Lebih (Overweight)";
        } else {
            return "Obesitas";
        }
    }

    private static function hitungZScoreBBU($beratBadan, $umurBulan, $jenisKelamin)
    {
        $medianTable = [
            'L' => [0 => 3.3, 1 => 4.5, 2 => 5.6, 3 => 6.4, 6 => 7.9, 9 => 8.9, 12 => 9.6, 18 => 10.9, 24 => 12.2, 30 => 13.3, 36 => 14.3, 42 => 15.3, 48 => 16.3, 54 => 17.1, 60 => 18.0],
            'P' => [0 => 3.2, 1 => 4.2, 2 => 5.1, 3 => 5.8, 6 => 7.3, 9 => 8.2, 12 => 8.9, 18 => 10.2, 24 => 11.5, 30 => 12.7, 36 => 13.9, 42 => 15.0, 48 => 16.0, 54 => 16.9, 60 => 17.7]
        ];
        $sdTable = [
            'L' => [0 => 0.4, 1 => 0.5, 2 => 0.5, 3 => 0.6, 6 => 0.7, 9 => 0.8, 12 => 0.9, 18 => 1.0, 24 => 1.1, 30 => 1.2, 36 => 1.3, 42 => 1.4, 48 => 1.5, 54 => 1.6, 60 => 1.7],
            'P' => [0 => 0.4, 1 => 0.4, 2 => 0.5, 3 => 0.5, 6 => 0.6, 9 => 0.7, 12 => 0.8, 18 => 0.9, 24 => 1.0, 30 => 1.1, 36 => 1.2, 42 => 1.3, 48 => 1.4, 54 => 1.5, 60 => 1.5]
        ];

        $keys = array_keys($medianTable[$jenisKelamin]);
        sort($keys);
        $lower = 0;
        $upper = 60;

        foreach ($keys as $k) {
            if ($umurBulan >= $k) {
                $lower = $k;
            }
            if ($umurBulan <= $k) {
                $upper = $k;
                break;
            }
        }

        if ($lower == $upper) {
            $median = $medianTable[$jenisKelamin][$lower];
            $sd = $sdTable[$jenisKelamin][$lower];
        } else {
            $ratio = ($umurBulan - $lower) / ($upper - $lower);
            $median = $medianTable[$jenisKelamin][$lower] + $ratio * ($medianTable[$jenisKelamin][$upper] - $medianTable[$jenisKelamin][$lower]);
            $sd = $sdTable[$jenisKelamin][$lower] + $ratio * ($sdTable[$jenisKelamin][$upper] - $sdTable[$jenisKelamin][$lower]);
        }

        return $sd > 0 ? ($beratBadan - $median) / $sd : 0;
    }

    private static function hitungZScoreTBU($tinggiBadan, $umurBulan, $jenisKelamin)
    {
        $medianTable = [
            'L' => [0 => 50.4, 1 => 54.7, 2 => 58.4, 3 => 61.4, 6 => 67.6, 9 => 72.0, 12 => 75.7, 18 => 82.3, 24 => 87.8, 30 => 92.6, 36 => 96.1, 42 => 100.0, 48 => 103.3, 54 => 106.7, 60 => 110.0],
            'P' => [0 => 49.8, 1 => 53.7, 2 => 57.1, 3 => 59.8, 6 => 65.7, 9 => 70.1, 12 => 74.0, 18 => 80.7, 24 => 86.4, 30 => 91.2, 36 => 95.1, 42 => 98.9, 48 => 102.7, 54 => 106.0, 60 => 109.4]
        ];
        $sdTable = [
            'L' => [0 => 2.0, 1 => 2.1, 2 => 2.2, 3 => 2.3, 6 => 2.5, 9 => 2.7, 12 => 2.9, 18 => 3.2, 24 => 3.6, 30 => 3.8, 36 => 3.9, 42 => 4.1, 48 => 4.3, 54 => 4.4, 60 => 4.6],
            'P' => [0 => 1.9, 1 => 2.0, 2 => 2.1, 3 => 2.2, 6 => 2.4, 9 => 2.6, 12 => 2.8, 18 => 3.1, 24 => 3.5, 30 => 3.7, 36 => 3.8, 42 => 4.0, 48 => 4.2, 54 => 4.3, 60 => 4.5]
        ];

        $keys = array_keys($medianTable[$jenisKelamin]);
        sort($keys);
        $lower = 0;
        $upper = 60;

        foreach ($keys as $k) {
            if ($umurBulan >= $k) {
                $lower = $k;
            }
            if ($umurBulan <= $k) {
                $upper = $k;
                break;
            }
        }

        if ($lower == $upper) {
            $median = $medianTable[$jenisKelamin][$lower];
            $sd = $sdTable[$jenisKelamin][$lower];
        } else {
            $ratio = ($umurBulan - $lower) / ($upper - $lower);
            $median = $medianTable[$jenisKelamin][$lower] + $ratio * ($medianTable[$jenisKelamin][$upper] - $medianTable[$jenisKelamin][$lower]);
            $sd = $sdTable[$jenisKelamin][$lower] + $ratio * ($sdTable[$jenisKelamin][$upper] - $sdTable[$jenisKelamin][$lower]);
        }

        return $sd > 0 ? ($tinggiBadan - $median) / $sd : 0;
    }

    private static function hitungZScoreBBTB($beratBadan, $tinggiBadan, $jenisKelamin)
    {
        $tbBulat = (int)$tinggiBadan;
        if ($tbBulat < 49) $tbBulat = 49;
        if ($tbBulat > 120) $tbBulat = 120;

        if ($jenisKelamin == 'L') {
            $median = 0.0245 * $tbBulat * $tbBulat - 2.2 * $tbBulat + 52.0;
        } else {
            $median = 0.024 * $tbBulat * $tbBulat - 2.15 * $tbBulat + 51.0;
        }

        $sd = $median * 0.11;
        return $sd > 0 ? ($beratBadan - $median) / $sd : 0;
    }
}
