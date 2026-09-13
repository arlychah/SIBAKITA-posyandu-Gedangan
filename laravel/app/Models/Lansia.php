<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lansia extends Model
{
    use HasFactory;

    protected $table = 'lansia';

    protected $fillable = [
        'warga_id',
    ];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(PemeriksaanLansia::class, 'lansia_id');
    }
}
