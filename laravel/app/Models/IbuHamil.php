<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IbuHamil extends Model
{
    use HasFactory;

    protected $table = 'ibu_hamil';

    protected $fillable = [
        'warga_id',
        'nama_suami',
    ];

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    public function riwayat(): HasMany
    {
        return $this->hasMany(PemeriksaanIbuHamil::class, 'ibu_hamil_id');
    }
}
