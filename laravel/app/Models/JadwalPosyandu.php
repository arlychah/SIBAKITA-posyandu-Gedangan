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
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TERJADWAL);
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
