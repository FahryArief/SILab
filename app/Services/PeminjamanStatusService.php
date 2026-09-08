<?php

namespace App\Services;

use App\Models\Peminjaman;
use App\Support\PeminjamanStatus;
use DomainException;

class PeminjamanStatusService
{
    private const TRANSITIONS = [
        PeminjamanStatus::PENDING => [PeminjamanStatus::VALIDATED, PeminjamanStatus::REJECTED],
        PeminjamanStatus::VALIDATED => [PeminjamanStatus::APPROVED, PeminjamanStatus::REJECTED],
        PeminjamanStatus::APPROVED => [PeminjamanStatus::RETURNED],
        PeminjamanStatus::REJECTED => [],
        PeminjamanStatus::RETURNED => [],
    ];

    public function transition(Peminjaman $peminjaman, string $status): void
    {
        $allowed = self::TRANSITIONS[$peminjaman->status] ?? [];

        if (! in_array($status, $allowed, true)) {
            throw new DomainException(
                "Transisi status {$peminjaman->status} ke {$status} tidak diizinkan."
            );
        }

        $peminjaman->update(['status' => $status]);
    }
}
