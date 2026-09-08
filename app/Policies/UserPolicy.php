<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the admin can view the user list.
     */
    public function viewAny(User $admin): bool
    {
        return $admin->role === 'super_admin';
    }

    /**
     * Determine if the admin can update a user.
     */
    public function update(User $admin, User $targetUser): bool
    {
        return $admin->role === 'super_admin';
    }

    /**
     * Determine if the admin can delete a user.
     * Cannot delete self. Cannot delete last super_admin.
     */
    public function delete(User $admin, User $targetUser): bool
    {
        if ($admin->role !== 'super_admin') {
            return false;
        }

        // Cannot delete self
        if ($admin->id === $targetUser->id) {
            return false;
        }

        // Cannot delete last super admin
        if ($targetUser->role === 'super_admin') {
            return User::where('role', 'super_admin')->count() > 1;
        }

        return true;
    }
}
