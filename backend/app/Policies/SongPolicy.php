<?php

namespace App\Policies;

use App\Models\Song;
use App\Models\User;
use App\Policies\Concerns\AdminPolicyChecks;

class SongPolicy
{
    use AdminPolicyChecks;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Song $song): bool
    {
        return $this->isAdmin($user);
    }
}
