<?php

namespace App\Services;

use App\Models\PemeriksaanAuditLog;
use Illuminate\Database\Eloquent\Model;

class PemeriksaanAuditService
{
    public function record(
        string $type,
        Model $examination,
        int $actorId,
        string $action,
        ?array $before = null,
        ?string $reason = null
    ): PemeriksaanAuditLog {
        $after = $examination->fresh()->getAttributes();

        return PemeriksaanAuditLog::create([
            'jenis_pemeriksaan' => $type,
            'pemeriksaan_id' => $examination->getKey(),
            'actor_id' => $actorId,
            'aksi' => $action,
            'sebelum' => $before,
            'sesudah' => $after,
            'alasan' => $reason,
        ]);
    }

    public function snapshot(Model $examination): array
    {
        return $examination->getAttributes();
    }
}
