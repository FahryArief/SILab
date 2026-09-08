<?php

namespace App\Services;

use App\Models\Peminjaman;
use DomainException;

class PeminjamanStatusService
{
    private const TRANSITIONS = [
        'pending' => ['divalidasi_teknisi', 'ditolak'],
        'divalidasi_teknisi' => ['disetujui', 'ditolak'],
        'disetujui' => ['dikembalikan'],
        'ditolak' => [],
        'dikembalikan' => [],
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
