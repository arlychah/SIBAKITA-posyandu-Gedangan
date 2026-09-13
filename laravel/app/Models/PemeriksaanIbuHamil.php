<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanIbuHamil extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_ibu_hamil';

    protected $fillable = [
        'ibu_hamil_id',
        'tanggal',
        'kehamilan_ke',
        'usia_kehamilan',
        'berat_badan',
        'tekanan_darah_sistolik',
        'tekanan_darah_diastolik',
        'lila',
        'tinggi_fundus',
        'detak_jantung_janin',
        'ttd_diberikan',
        'jumlah_ttd',
        'imunisasi_tt',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'ttd_diberikan' => 'boolean',
    ];

    public function ibu_hamil(): BelongsTo
    {
        return $this->belongsTo(IbuHamil::class, 'ibu_hamil_id');
    }
}
