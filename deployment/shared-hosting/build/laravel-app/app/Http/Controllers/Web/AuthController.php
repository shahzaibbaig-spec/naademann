<?php

namespace App\Http\Controllers\Web;

use App\Models\Artist;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends WebController
{
    public function showLogin(Request $request): View
    {
        return view('auth.login', [
            ...$this->interactionState($request),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(match ($request->user()->role) {
            'admin', 'super_admin' => route('admin.dashboard'),
            'creator' => route('creator.dashboard'),
            default => route('home'),
        });
    }

    public function showRegister(Request $request): View
    {
        return view('auth.register', [
            ...$this->interactionState($request),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role' => ['required', 'in:listener,creator'],
            'stage_name' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($data): User {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            if ($data['role'] === 'creator') {
                $stageName = $data['stage_name'] ?: $data['name'];

                Artist::create([
                    'user_id' => $user->id,
                    'name' => $stageName,
                    'slug' => $this->uniqueArtistSlug($stageName),
                    'genre' => $data['genre'] ?: 'Independent',
                    'bio' => 'New creator on Naad-e-Maan.',
                    'image_url' => $user->avatar_url,
                    'monthly_listeners' => 0,
                    'followers' => 0,
                ]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->isCreator() ? 'creator.dashboard' : 'home');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function uniqueArtistSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $index = 1;

        while (Artist::query()->where('slug', $slug)->exists()) {
            $slug = "{$base}-{$index}";
            $index++;
        }

        return $slug;
    }
}
