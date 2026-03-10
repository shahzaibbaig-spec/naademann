@extends('layouts.app')

@section('title', 'Naad-e-Maan | Login')

@section('content')
  <section class="mx-auto max-w-xl px-6 py-16 lg:px-8">
    <div class="glass-card rounded-[2rem] border border-white/10 p-8">
      <p class="section-kicker">Welcome Back</p>
      <h1 class="mt-4 font-display text-4xl font-semibold">Login to Naad-e-Maan</h1>
      <p class="mt-5 text-base leading-8 text-white/65">Sign in with your account credentials to access your dashboard and music workspace.</p>

      <form method="POST" action="{{ route('login.store') }}" class="mt-8 grid gap-4">
        @csrf
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
          <span class="mb-2 block text-sm text-white/50">Email</span>
          <input name="email" type="email" value="{{ old('email') }}" class="w-full bg-transparent text-white outline-none" required>
          @error('email')<span class="mt-2 block text-sm text-neon-pink">{{ $message }}</span>@enderror
        </label>
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
          <span class="mb-2 block text-sm text-white/50">Password</span>
          <input name="password" type="password" class="w-full bg-transparent text-white outline-none" required>
        </label>
        <label class="flex items-center gap-3 text-sm text-white/65">
          <input name="remember" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-transparent">
          Remember me
        </label>
        <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Login</button>
      </form>
    </div>
  </section>
@endsection
