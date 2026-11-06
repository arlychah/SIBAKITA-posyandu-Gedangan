<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPosyandu extends Model
{
    use HasFactory;

    public const KATEGORI = ['balita', 'ibu_hamil', 'lansia', 'semua'];
    public const STATUS_TERJADWAL = 'scheduled';
    public const STATUS_DIBATALKAN = 'cancelled';
    public const APPROVAL_PENDING = 'pending';
    public const APPROVAL_NEEDS_REVISION = 'needs_revision';
    public const APPROVAL_APPROVED = 'approved';

    protected $table = 'jadwal_posyandu';

    protected $fillable = [
        'judul',
        'kategori',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'keterangan',
        'status',
        'approval_status',
        'review_note',
        'created_by',
        'updated_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TERJADWAL);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }

    public function scopeForMemberCategory(Builder $query, ?string $category): Builder
    {
        if (!$category) {
            return $query->whereRaw('1 = 0');
        }

        $normalized = strtolower(str_replace(' ', '_', $category));

        return $query->whereIn('kategori', [$normalized, 'semua']);
    }
}
