<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\AdminPolicyChecks;

class UserPolicy
{
    use AdminPolicyChecks;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, User $target): bool
    {
        return $this->isAdmin($user);
    }
}
