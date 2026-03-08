<?php

namespace App\Policies;

use App\Models\Genre;
use App\Models\User;
use App\Policies\Concerns\AdminPolicyChecks;

class GenrePolicy
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

    public function update(User $user, Genre $genre): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Genre $genre): bool
    {
        return $this->isAdmin($user);
    }
}
