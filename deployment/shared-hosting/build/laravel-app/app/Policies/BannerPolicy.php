<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use App\Policies\Concerns\AdminPolicyChecks;

class BannerPolicy
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

    public function update(User $user, Banner $banner): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Banner $banner): bool
    {
        return $this->isAdmin($user);
    }
}
