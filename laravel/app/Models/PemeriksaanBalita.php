<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanBalita extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan_balita';

    protected $fillable = [
        'balita_id',
        'tanggal',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'status_gizi_bbu',
        'status_gizi_tbu',
        'status_gizi_bbtb',
        'asi_eksklusif',
        'imunisasi_bcg',
        'imunisasi_dpt1',
        'imunisasi_dpt2',
        'imunisasi_dpt3',
        'imunisasi_polio1',
        'imunisasi_polio2',
        'imunisasi_polio3',
        'imunisasi_campak',
        'vitamin_a_bulan_ke',
        'pmt_diterima',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'imunisasi_bcg' => 'boolean',
        'imunisasi_dpt1' => 'boolean',
        'imunisasi_dpt2' => 'boolean',
        'imunisasi_dpt3' => 'boolean',
        'imunisasi_polio1' => 'boolean',
        'imunisasi_polio2' => 'boolean',
        'imunisasi_polio3' => 'boolean',
        'imunisasi_campak' => 'boolean',
    ];

    public function balita(): BelongsTo
    {
        return $this->belongsTo(Balita::class, 'balita_id');
    }
}
