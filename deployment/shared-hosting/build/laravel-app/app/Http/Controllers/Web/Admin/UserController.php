<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\WebController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends WebController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users', [
            'users' => User::query()->withCount('artists')->latest()->paginate(20)->withQueryString(),
            'roleCounts' => [
                'listeners' => User::query()->where('role', 'listener')->count(),
                'creators' => User::query()->where('role', 'creator')->count(),
                'admins' => User::query()->whereIn('role', ['admin', 'super_admin'])->count(),
            ],
            ...$this->interactionState($request),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validate([
            'role' => ['required', 'in:listener,creator,admin,super_admin'],
        ]);

        if (in_array($user->role, ['admin', 'super_admin'], true)
            && !in_array($data['role'], ['admin', 'super_admin'], true)
            && User::query()->whereIn('role', ['admin', 'super_admin'])->count() <= 1) {
            return back()->with('status', 'At least one admin account must remain assigned.');
        }

        $user->update($data);

        return back()->with('status', 'User role updated.');
    }
}
