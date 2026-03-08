@extends('layouts.app')

@section('title', 'Naad-e-Maan | Register')

@section('content')
  <section class="mx-auto max-w-2xl px-6 py-16 lg:px-8">
    <div class="glass-card rounded-[2rem] border border-white/10 p-8">
      <p class="section-kicker">Create Your Account</p>
      <h1 class="mt-4 font-display text-4xl font-semibold">Join Naad-e-Maan</h1>
      <form method="POST" action="{{ route('register.store') }}" class="mt-8 grid gap-4 md:grid-cols-2">
        @csrf
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4 md:col-span-2">
          <span class="mb-2 block text-sm text-white/50">Full Name</span>
          <input name="name" type="text" value="{{ old('name') }}" class="w-full bg-transparent text-white outline-none" required>
        </label>
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
          <span class="mb-2 block text-sm text-white/50">Email</span>
          <input name="email" type="email" value="{{ old('email') }}" class="w-full bg-transparent text-white outline-none" required>
        </label>
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
          <span class="mb-2 block text-sm text-white/50">Role</span>
          <select name="role" class="w-full bg-transparent text-white outline-none">
            <option value="listener" class="bg-slate-950">Listener</option>
            <option value="creator" class="bg-slate-950">Creator</option>
          </select>
        </label>
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
          <span class="mb-2 block text-sm text-white/50">Stage Name</span>
          <input name="stage_name" type="text" value="{{ old('stage_name') }}" class="w-full bg-transparent text-white outline-none">
        </label>
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
          <span class="mb-2 block text-sm text-white/50">Primary Genre</span>
          <input name="genre" type="text" value="{{ old('genre') }}" class="w-full bg-transparent text-white outline-none">
        </label>
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
          <span class="mb-2 block text-sm text-white/50">Password</span>
          <input name="password" type="password" class="w-full bg-transparent text-white outline-none" required>
        </label>
        <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
          <span class="mb-2 block text-sm text-white/50">Confirm Password</span>
          <input name="password_confirmation" type="password" class="w-full bg-transparent text-white outline-none" required>
        </label>
        <div class="md:col-span-2">
          <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Create Account</button>
        </div>
      </form>
    </div>
  </section>
@endsection
