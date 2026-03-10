<?php

namespace App\Policies;

use App\Models\Artist;
use App\Models\User;
use App\Policies\Concerns\AdminPolicyChecks;

class ArtistPolicy
{
    use AdminPolicyChecks;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Artist $artist): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function delete(User $user, Artist $artist): bool
    {
        return $this->isSuperAdmin($user);
    }
}
