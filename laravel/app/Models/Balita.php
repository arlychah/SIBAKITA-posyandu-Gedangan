<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Balita extends Model
{
    use HasFactory;

    protected $table = 'balita';

    protected $fillable = [
        'warga_id',
        'nama_ibu',
        'nama_ayah',
    ];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(PemeriksaanBalita::class, 'balita_id');
    }
}
