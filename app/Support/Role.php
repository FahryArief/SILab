<?php

namespace App\Support;

final class Role
{
    public const SUPER_ADMIN = 'super_admin';
    public const TEKNISI = 'teknisi';
    public const KEPALA_LAB = 'kepala_lab';
    public const KA_PRODI = 'ka_prodi';
    public const PEMINJAM = 'peminjam';

    public const ALL = [
        self::SUPER_ADMIN,
        self::TEKNISI,
        self::KEPALA_LAB,
        self::KA_PRODI,
        self::PEMINJAM,
    ];
}
