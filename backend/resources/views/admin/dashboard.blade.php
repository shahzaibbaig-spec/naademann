@extends('layouts.app')

@section('title', 'Naad-e-Maan | Admin Dashboard')

@php
    $statusMeta = [
        'draft' => ['label' => 'Drafts', 'classes' => 'border-white/10 bg-white/5 text-white/70'],
        'pending' => ['label' => 'Pending', 'classes' => 'border-neon-blue/20 bg-neon-blue/10 text-neon-blue'],
        'approved' => ['label' => 'Published', 'classes' => 'border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan'],
        'rejected' => ['label' => 'Rejected', 'classes' => 'border-neon-pink/20 bg-neon-pink/10 text-neon-pink'],
    ];
@endphp

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    @include('admin._nav')

    <div class="glass-card relative overflow-hidden rounded-[2rem] border border-white/10 p-8">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(59,242,255,0.14),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(79,124,255,0.14),transparent_28%)]"></div>
      <div class="relative">
        <p class="section-kicker">Admin Dashboard</p>
        <h1 class="mt-4 font-display text-5xl font-semibold">Platform control center</h1>
        <p class="mt-5 max-w-3xl text-base leading-8 text-white/65">Monitor growth, moderate releases, manage creators and artist profiles, curate homepage content, and keep the Naad-e-Maan brand system aligned across the product.</p>
      </div>
    </div>

    <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-4">
      <x-stats-card label="Users" :value="number_format($metrics['users'])" description="Total registered accounts across listeners, creators, and admins." />
      <x-stats-card label="Creators" :value="number_format($metrics['creators'])" accent="pink" description="Creator accounts currently able to publish artist content." />
      <x-stats-card label="Admins" :value="number_format($metrics['admins'])" accent="blue" description="Accounts with full platform-control permissions." />
      <x-stats-card label="Artists" :value="number_format($metrics['artists'])" description="Artist profiles visible in the platform catalog." />
      <x-stats-card label="Tracks" :value="number_format($metrics['tracks'])" accent="pink" description="Songs stored across draft, moderation, and live states." />
      <x-stats-card label="Pending Tracks" :value="number_format($metrics['pending_tracks'])" accent="blue" description="Releases currently waiting for moderation." />
      <x-stats-card label="Published Tracks" :value="number_format($metrics['published_tracks'])" description="Tracks currently approved for public discovery." />
      <x-stats-card label="Active Genres / Banners" :value="number_format($metrics['active_genres']).' / '.number_format($metrics['active_banners'])" accent="pink" description="Live discovery genres and homepage banners." />
    </div>

    <div class="mt-10 grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
      <section class="glass-card rounded-[2rem] border border-white/10 p-6">
        <div class="flex items-end justify-between gap-4">
          <div>
            <p class="section-kicker">Moderation Snapshot</p>
            <h2 class="mt-3 font-display text-3xl font-semibold">Track status overview</h2>
          </div>
          <a href="{{ route('admin.moderation.index') }}" class="text-sm text-white/55 transition hover:text-neon-cyan">Open full table</a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
          @foreach ($statusMeta as $key => $meta)
            <article class="rounded-[1.5rem] border p-5 {{ $meta['classes'] }}">
              <p class="text-xs uppercase tracking-[0.28em]">{{ $meta['label'] }}</p>
              <p class="mt-3 font-display text-3xl font-semibold text-white">{{ number_format($statusCounts[$key] ?? 0) }}</p>
            </article>
          @endforeach
        </div>

        <div class="mt-8 space-y-4">
          @forelse ($pendingTracks as $track)
            <article class="flex flex-col gap-4 rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-4 sm:flex-row sm:items-center">
              <img src="{{ $track->cover_image_url }}" alt="{{ $track->title }}" class="h-16 w-16 rounded-[1rem] object-cover">
              <div class="min-w-0 flex-1">
                <p class="truncate font-semibold">{{ $track->title }}</p>
                <p class="mt-1 truncate text-sm text-white/50">{{ $track->artist?->name ?? 'Unknown artist' }} / {{ $track->album?->title ?? 'Single' }}</p>
              </div>
              <div class="flex items-center gap-3">
                <form method="POST" action="{{ route('admin.moderation.update', $track) }}">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="moderation_status" value="approved">
                  <button type="submit" class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-950">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.moderation.update', $track) }}">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="moderation_status" value="rejected">
                  <button type="submit" class="rounded-full border border-neon-pink/20 bg-neon-pink/10 px-4 py-2 text-xs font-semibold text-neon-pink">Reject</button>
                </form>
              </div>
            </article>
          @empty
            <div class="rounded-[1.5rem] border border-dashed border-white/10 bg-slate-950/45 px-4 py-8 text-center text-sm text-white/45">
              No pending tracks right now.
            </div>
          @endforelse
        </div>
      </section>

      <div class="space-y-8">
        <section class="glass-card rounded-[2rem] border border-white/10 p-6">
          <p class="section-kicker">Recent Users</p>
          <div class="mt-6 space-y-4">
            @foreach ($recentUsers as $user)
              <article class="flex items-center gap-4 rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-4">
                <img src="{{ $user->avatar_url ?: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=240&q=80' }}" alt="{{ $user->name }}" class="h-14 w-14 rounded-full object-cover">
                <div class="min-w-0 flex-1">
                  <p class="truncate font-semibold">{{ $user->name }}</p>
                  <p class="truncate text-sm text-white/50">{{ $user->email }}</p>
                </div>
                <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.2em] text-white/65">{{ $user->role }}</span>
              </article>
            @endforeach
          </div>
        </section>

        <section class="glass-card rounded-[2rem] border border-white/10 p-6">
          <p class="section-kicker">Recent Artist Profiles</p>
          <div class="mt-6 space-y-4">
            @foreach ($recentArtists as $artist)
              <article class="flex items-center gap-4 rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-4">
                <img src="{{ $artist->image_url }}" alt="{{ $artist->name }}" class="h-14 w-14 rounded-[1rem] object-cover">
                <div class="min-w-0 flex-1">
                  <p class="truncate font-semibold">{{ $artist->name }}</p>
                  <p class="truncate text-sm text-white/50">{{ $artist->user?->email ?? 'Guest artist profile' }}</p>
                </div>
                <a href="{{ route('admin.artists.index') }}" class="text-sm text-neon-cyan">Manage</a>
              </article>
            @endforeach
          </div>
        </section>

        <section class="glass-card rounded-[2rem] border border-white/10 p-6">
          <p class="section-kicker">Quick Links</p>
          <div class="mt-6 grid gap-4">
            <a href="{{ route('admin.users.index') }}" class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5 transition hover:-translate-y-1 hover:border-neon-cyan/30"><p class="font-semibold">Manage users</p><p class="mt-2 text-sm text-white/50">Edit roles, inspect creator ownership, and keep account access aligned.</p></a>
            <a href="{{ route('admin.uploads.index') }}" class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5 transition hover:-translate-y-1 hover:border-neon-cyan/30"><p class="font-semibold">Admin uploads</p><p class="mt-2 text-sm text-white/50">Publish tracks on behalf of artists or save them as drafts from the admin panel.</p></a>
            <a href="{{ route('admin.genres.index') }}" class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5 transition hover:-translate-y-1 hover:border-neon-cyan/30"><p class="font-semibold">Genre system</p><p class="mt-2 text-sm text-white/50">Curate genre imagery, accent colors, and discovery ordering.</p></a>
            <a href="{{ route('admin.settings.index') }}" class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5 transition hover:-translate-y-1 hover:border-neon-cyan/30"><p class="font-semibold">Brand settings</p><p class="mt-2 text-sm text-white/50">Control logo text, tagline, footer copy, and social links.</p></a>
          </div>
        </section>
      </div>
    </div>
  </section>
@endsection
