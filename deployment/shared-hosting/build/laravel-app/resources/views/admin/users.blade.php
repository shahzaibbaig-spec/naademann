@extends('layouts.app')

@section('title', 'Naad-e-Maan | Admin Users')

@php
    $roleMeta = [
        'listener' => 'border-white/10 bg-white/5 text-white/70',
        'creator' => 'border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan',
        'admin' => 'border-neon-pink/20 bg-neon-pink/10 text-neon-pink',
        'super_admin' => 'border-amber-400/20 bg-amber-400/10 text-amber-300',
    ];
@endphp

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    @include('admin._nav')

    <div class="grid gap-5 md:grid-cols-3">
      <x-stats-card label="Listeners" :value="number_format($roleCounts['listeners'])" description="Accounts focused on discovery and playback." />
      <x-stats-card label="Creators" :value="number_format($roleCounts['creators'])" accent="pink" description="Accounts eligible to manage creator workspaces." />
      <x-stats-card label="Admins" :value="number_format($roleCounts['admins'])" accent="blue" description="Accounts with full control over the platform." />
    </div>

    <div class="mt-8 glass-card rounded-[2rem] border border-white/10 p-6">
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <p class="section-kicker">Manage Users</p>
          <h1 class="mt-3 font-display text-4xl font-semibold">Users table with role controls</h1>
        </div>
        <p class="text-sm text-white/45">{{ number_format($users->total()) }} users</p>
      </div>

      <div class="mt-6 overflow-x-auto">
        <table class="w-full min-w-[980px] text-left text-sm">
          <thead class="text-white/45">
            <tr>
              <th class="pb-4">User</th>
              <th class="pb-4">Role</th>
              <th class="pb-4">Linked Artists</th>
              <th class="pb-4">Joined</th>
              <th class="pb-4 text-right">Update</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @foreach ($users as $user)
              <tr>
                <td class="py-4">
                  <div class="flex items-center gap-4">
                    <img src="{{ $user->avatar_url ?: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=240&q=80' }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-full object-cover">
                    <div class="min-w-0">
                      <div class="truncate font-medium">{{ $user->name }}</div>
                      <div class="truncate text-white/45">{{ $user->email }}</div>
                      @if ($user->headline)
                        <div class="truncate text-xs text-white/35">{{ $user->headline }}</div>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="py-4">
                  <span class="rounded-full border px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $roleMeta[$user->role] ?? 'border-white/10 bg-white/5 text-white/70' }}">
                    {{ $user->role }}
                  </span>
                </td>
                <td class="py-4 text-white/60">{{ number_format($user->artists_count) }}</td>
                <td class="py-4 text-white/60">{{ $user->created_at?->format('M d, Y') }}</td>
                <td class="py-4">
                  <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center justify-end gap-3">
                    @csrf
                    @method('PUT')
                    <select name="role" class="rounded-full border border-white/10 bg-slate-950/70 px-3 py-2 text-white">
                      @foreach (['listener', 'creator', 'admin', 'super_admin'] as $role)
                        <option value="{{ $role }}" @selected($user->role === $role)>{{ ucfirst($role) }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-xs font-semibold text-neon-cyan">Save</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-8">{{ $users->links() }}</div>
    </div>
  </section>
@endsection
