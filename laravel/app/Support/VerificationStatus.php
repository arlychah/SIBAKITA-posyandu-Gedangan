<?php

namespace App\Support;

final class VerificationStatus
{
    public const PENDING = 'pending';
    public const LEGACY_REVIEW = 'legacy_review';
    public const VERIFIED = 'verified';
    public const NEEDS_REVISION = 'needs_revision';

    public static function labels(): array
    {
        return [
            self::PENDING => 'Menunggu verifikasi',
            self::LEGACY_REVIEW => 'Perlu ditinjau (data lama)',
            self::VERIFIED => 'Terverifikasi',
            self::NEEDS_REVISION => 'Perlu perbaikan',
        ];
    }
}
