@extends('layouts.app')

@section('title', 'Naad-e-Maan | Listen')

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    <div class="grid gap-8 xl:grid-cols-[1.15fr_0.85fr]">
      <div>
        <div class="glass-card rounded-[2rem] border border-white/10 p-8">
          <p class="section-kicker">HTML5 Audio Playback</p>
          <h1 class="mt-4 font-display text-5xl font-semibold">Queue-Driven Listening</h1>
          <p class="mt-5 max-w-3xl text-base leading-8 text-white/65">
            Build a queue, use play and pause, jump next and previous, control progress and volume, and keep the floating mini-player running as you move through the platform.
          </p>
          @if ($activeGenre)
            <div class="mt-6 inline-flex rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-sm text-neon-cyan">
              Filtered to {{ $activeGenre }}
            </div>
          @endif
          @if (!empty($activeAlbum))
            <div class="mt-3 inline-flex rounded-full border border-neon-pink/20 bg-neon-pink/10 px-4 py-2 text-sm text-neon-pink">
              Album focus: {{ $activeAlbum->title }}
            </div>
          @endif
          @if (!empty($activeTrack))
            <div class="mt-3 inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/75">
              Track focus: {{ $activeTrack->title }}
            </div>
          @endif
          @if ($activePlaylistSlug)
            <div class="mt-3 inline-flex rounded-full border border-neon-pink/20 bg-neon-pink/10 px-4 py-2 text-sm text-neon-pink">
              Playlist queue loaded
            </div>
          @endif
          <div class="mt-8 flex flex-wrap gap-3">
            <a href="{{ route('listen') }}" class="pill-nav {{ $activeGenre === '' ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">All Genres</a>
            @foreach ($genres as $genre)
              <a href="{{ route('listen', ['genre' => $genre->name]) }}" class="pill-nav {{ $activeGenre === $genre->name ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">{{ $genre->name }}</a>
            @endforeach
          </div>
        </div>

        <div class="mt-10">
          <div class="mb-8 flex items-end justify-between gap-6">
            <div>
              <p class="section-kicker">Track Library</p>
              <h2 class="mt-4 font-display text-3xl font-semibold">Approved Songs Ready to Stream</h2>
            </div>
            <button type="button" data-play-all class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Play All</button>
          </div>
          <div class="listen-library grid gap-6 sm:grid-cols-2">
            @forelse ($songs as $song)
              <x-track-card :song="$song" :favorite-song-ids="$favoriteSongIds" />
            @empty
              <div class="glass-card rounded-[1.75rem] border border-white/10 p-6 sm:col-span-2">
                <p class="text-lg font-semibold">No approved tracks matched this view.</p>
                <p class="mt-3 text-white/55">Try another genre, album, playlist, or search term.</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>

      <aside class="space-y-8">
        <div class="glass-card rounded-[2rem] border border-white/10 p-6">
          <p class="section-kicker">Public + Personal Playlists</p>
          <div class="mt-6 space-y-4">
            @foreach ($playlists as $playlist)
              <button type="button" data-playlist-queue='@json($playlist->queue_payload ?? [])' class="glass-card flex w-full items-center gap-4 rounded-[1.5rem] border {{ $activePlaylistSlug === $playlist->slug ? 'border-neon-cyan/30 bg-neon-cyan/10' : 'border-white/10' }} px-4 py-4 text-left transition hover:-translate-y-1 hover:border-neon-cyan/30">
                <img src="{{ $playlist->cover_image_url }}" alt="{{ $playlist->name }}" class="h-16 w-16 rounded-[1rem] object-cover">
                <div>
                  <h3 class="font-semibold">{{ $playlist->name }}</h3>
                  <p class="mt-1 text-sm text-white/50">{{ count($playlist->song_ids ?? []) }} songs - {{ $playlist->user?->name ?? 'Naad-e-Maan' }}</p>
                </div>
              </button>
            @endforeach
          </div>
        </div>

        <div class="glass-card rounded-[2rem] border border-white/10 p-6">
          <p class="section-kicker">Playback Features</p>
          <div class="mt-6 grid gap-4">
            <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5"><p class="text-sm text-white/50">Queue Support</p><p class="mt-2 font-semibold">Track cards build a live queue for next and previous navigation.</p></article>
            <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5"><p class="text-sm text-white/50">Mini Player</p><p class="mt-2 font-semibold">Playback tools stay pinned across every Blade page.</p></article>
            <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5"><p class="text-sm text-white/50">AJAX Actions</p><p class="mt-2 font-semibold">Favorites, follows, and playlist saves update without full page reloads.</p></article>
          </div>
        </div>
      </aside>
    </div>
  </section>
@endsection
