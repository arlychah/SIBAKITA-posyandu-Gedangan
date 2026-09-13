<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanLansia extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_lansia';

    protected $fillable = [
        'lansia_id',
        'tanggal',
        'berat_badan',
        'tinggi_badan',
        'tekanan_darah_sistolik',
        'tekanan_darah_diastolik',
        'gula_darah_puasa',
        'gula_darah_sewaktu',
        'kolesterol',
        'asam_urat',
        'skrining_jiwa',
        'penglihatan',
        'pendengaran',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function lansia(): BelongsTo
    {
        return $this->belongsTo(Lansia::class, 'lansia_id');
    }
}
