<?php

namespace App\Models;

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
