<?php

namespace App\Policies;

use App\Models\Peminjaman;
use App\Models\User;

class PeminjamanPolicy
{
    /**
     * Determine if the user can view any peminjaman.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi', 'kepala_lab', 'peminjam']);
    }

    /**
     * Determine if the user can view the peminjaman.
     */
    public function view(User $user, Peminjaman $peminjaman): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi', 'kepala_lab'])
               || $user->id === $peminjaman->user_id;
    }

    /**
     * Determine if the user can approve (teknisi validation).
     */
    public function approve(User $user, Peminjaman $peminjaman): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi'])
               && $peminjaman->status === 'pending';
    }

    /**
     * Determine if the user can give final ACC (kepala lab).
     */
    public function accKepalaLab(User $user, Peminjaman $peminjaman): bool
    {
        return in_array($user->role, ['super_admin', 'kepala_lab'])
               && $peminjaman->status === 'divalidasi_teknisi';
    }

    /**
     * Determine if the user can reject.
     */
    public function reject(User $user, Peminjaman $peminjaman): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi', 'kepala_lab'])
               && in_array($peminjaman->status, ['pending', 'divalidasi_teknisi']);
    }

    /**
     * Determine if the user can process return.
     */
    public function kembalikan(User $user, Peminjaman $peminjaman): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi'])
               && $peminjaman->status === 'disetujui';
    }

    /**
     * Determine if the user can download the surat.
     */
    public function downloadSurat(User $user, Peminjaman $peminjaman): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi', 'kepala_lab'])
               || $user->id === $peminjaman->user_id;
    }
}
