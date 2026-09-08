<?php

namespace App\Policies;

use App\Models\BookingRuangan;
use App\Models\User;

class BookingRuanganPolicy
{
    /**
     * Determine if the user can view the booking.
     */
    public function view(User $user, BookingRuangan $booking): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi', 'kepala_lab'])
               || $user->id === $booking->user_id;
    }

    /**
     * Determine if the user can approve.
     */
    public function approve(User $user, BookingRuangan $booking): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi'])
               && $booking->status === 'pending';
    }

    /**
     * Determine if the user can reject.
     */
    public function reject(User $user, BookingRuangan $booking): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi'])
               && $booking->status === 'pending';
    }

    /**
     * Determine if the user can mark as done.
     */
    public function markAsDone(User $user, BookingRuangan $booking): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi'])
               && $booking->status === 'disetujui';
    }

    /**
     * Determine if the user can download the surat.
     */
    public function downloadSurat(User $user, BookingRuangan $booking): bool
    {
        return in_array($user->role, ['super_admin', 'teknisi', 'kepala_lab'])
               || $user->id === $booking->user_id;
    }
}
