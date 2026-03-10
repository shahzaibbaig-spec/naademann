<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\WebController;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:listener,creator,admin,super_admin'],
        ]);

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        return back()->with('status', 'User account created.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ((int) $request->user()->id === (int) $user->id) {
            return back()->with('status', 'You cannot delete your own account.');
        }

        if (in_array($user->role, ['admin', 'super_admin'], true)
            && User::query()->whereIn('role', ['admin', 'super_admin'])->count() <= 1) {
            return back()->with('status', 'At least one admin account must remain assigned.');
        }

        $user->delete();

        return back()->with('status', 'User account removed.');
    }
}
