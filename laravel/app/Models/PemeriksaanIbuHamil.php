<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'nadi',
        'hemoglobin',
        'ttd_diberikan',
        'jumlah_ttd',
        'imunisasi_tt',
        'catatan',
        'submitted_by',
        'verification_status',
        'verified_by',
        'verified_at',
        'return_reason',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'verified_at' => 'datetime',
        'ttd_diberikan' => 'boolean',
        'nadi' => 'integer',
        'hemoglobin' => 'float',
    ];

    public function ibu_hamil(): BelongsTo
    {
        return $this->belongsTo(IbuHamil::class, 'ibu_hamil_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(PemeriksaanAuditLog::class, 'pemeriksaan_id')
            ->where('jenis_pemeriksaan', 'ibu_hamil');
    }
}
