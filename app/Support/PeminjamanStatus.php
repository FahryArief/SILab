<?php

namespace App\Support;

final class PeminjamanStatus
{
    public const PENDING = 'pending';
    public const VALIDATED = 'divalidasi_teknisi';
    public const APPROVED = 'disetujui';
    public const REJECTED = 'ditolak';
    public const RETURNED = 'dikembalikan';
}
