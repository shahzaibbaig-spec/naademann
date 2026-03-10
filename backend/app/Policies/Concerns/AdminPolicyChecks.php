<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait AdminPolicyChecks
{
    protected function isAdmin(User $user): bool
    {
        return $user->isAdmin();
    }

    protected function isSuperAdmin(User $user): bool
    {
        return $user->role === 'super_admin';
    }
}
