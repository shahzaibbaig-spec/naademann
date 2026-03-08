<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'api_token' => Str::random(60),
        ]);

        return response()->json([
            'message' => 'Account created successfully.',
            'token' => $user->api_token,
            'user' => $this->serializeUser($user),
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->forceFill([
            'api_token' => Str::random(60),
        ])->save();

        return response()->json([
            'message' => 'Login successful.',
            'token' => $user->api_token,
            'user' => $this->serializeUser($user),
        ]);
    }

    public function me(Request $request)
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return response()->json([
            'user' => $this->serializeUser($user),
        ]);
    }

    public function logout(Request $request)
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $user->forceFill(['api_token' => null])->save();

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    private function resolveUser(Request $request): ?User
    {
        $token = $request->bearerToken() ?: $request->input('token');

        if (!$token) {
            return null;
        }

        return User::query()->where('api_token', $token)->first();
    }

    private function serializeUser(User $user): array
    {
        $user->loadMissing('artists');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
            'artists' => $user->artists->map(fn ($artist) => [
                'id' => $artist->id,
                'name' => $artist->name,
                'slug' => $artist->slug,
                'genre' => $artist->genre,
            ])->values(),
        ];
    }
}
