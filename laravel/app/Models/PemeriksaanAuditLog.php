<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemeriksaanAuditLog extends Model
{
    protected $table = 'pemeriksaan_audit_logs';

    protected $fillable = [
        'jenis_pemeriksaan',
        'pemeriksaan_id',
        'actor_id',
        'aksi',
        'sebelum',
        'sesudah',
        'alasan',
    ];

    protected $casts = [
        'sebelum' => 'array',
        'sesudah' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
