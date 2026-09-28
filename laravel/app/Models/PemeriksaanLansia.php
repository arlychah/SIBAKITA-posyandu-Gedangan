<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'nadi',
        'imt',
        'gula_darah_puasa',
        'gula_darah_sewaktu',
        'kolesterol',
        'asam_urat',
        'skrining_jiwa',
        'penglihatan',
        'pendengaran',
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
        'nadi' => 'integer',
        'imt' => 'float',
    ];

    public function lansia(): BelongsTo
    {
        return $this->belongsTo(Lansia::class, 'lansia_id');
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
            ->where('jenis_pemeriksaan', 'lansia');
    }
}
