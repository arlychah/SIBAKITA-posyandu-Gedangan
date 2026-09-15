<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    use HasFactory;

    protected $table = 'warga';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'whatsapp',
        'puskesmas',
        'pustu',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'kategori',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    protected $appends = [
        'umur_tahun',
        'umur_bulan',
        'umur_formatted',
    ];

    protected function umurTahun(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tanggal_lahir?->diffInYears(now()) ?? 0,
        );
    }

    protected function umurBulan(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tanggal_lahir?->diffInMonths(now()) ?? 0,
        );
    }

    protected function umurFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                $tahun = $this->umur_tahun;
                $bulan = $this->tanggal_lahir ? ($this->tanggal_lahir->diffInMonths(now()) % 12) : 0;
                if ($tahun > 0) {
                    return "{$tahun} tahun {$bulan} bulan";
                }
                return "{$bulan} bulan";
            },
        );
    }

    public function balita(): HasOne
    {
        return $this->hasOne(Balita::class, 'warga_id');
    }

    public function ibu_hamil(): HasOne
    {
        return $this->hasOne(IbuHamil::class, 'warga_id');
    }

    public function lansia(): HasOne
    {
        return $this->hasOne(Lansia::class, 'warga_id');
    }
}
