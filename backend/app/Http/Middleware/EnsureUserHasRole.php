<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$this->allowsRole($user->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }

    private function allowsRole(string $userRole, array $roles): bool
    {
        $hierarchy = [
            'listener' => 0,
            'creator' => 1,
            'admin' => 2,
            'super_admin' => 3,
        ];

        $userLevel = $hierarchy[$userRole] ?? -1;

        foreach ($roles as $role) {
            if ($userLevel >= ($hierarchy[$role] ?? PHP_INT_MAX)) {
                return true;
            }
        }

        return false;
    }
}
