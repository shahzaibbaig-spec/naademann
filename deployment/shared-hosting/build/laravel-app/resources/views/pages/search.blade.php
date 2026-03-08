@extends('layouts.app')

@section('title', $query !== '' ? 'Naad-e-Maan | Search: '.$query : 'Naad-e-Maan | Search')

@php
    $resultCount = $tracks->count() + $artists->count() + $albums->count() + $genres->count();
@endphp

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    <div class="glass-card relative overflow-hidden rounded-[2.25rem] border border-white/10 p-8 lg:p-10">
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(59,242,255,0.14),transparent_30%),radial-gradient(circle_at_bottom_left,rgba(255,79,216,0.12),transparent_28%)]"></div>
      <div class="relative">
        <p class="section-kicker">Global Search</p>
        <h1 class="mt-4 font-display text-4xl font-semibold sm:text-5xl">
          @if ($query !== '')
            Results for "{{ $query }}"
          @else
            Search the Naad-e-Maan catalog
          @endif
        </h1>
        <p class="mt-5 max-w-3xl text-base leading-8 text-white/65">
          Explore artists, tracks, albums, and genres from one search flow, then jump directly into listening or artist discovery.
        </p>

        @if ($query !== '')
          <div class="mt-8 flex flex-wrap gap-3">
            <span class="rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-sm text-neon-cyan">{{ number_format($tracks->count()) }} tracks</span>
            <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70">{{ number_format($artists->count()) }} artists</span>
            <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70">{{ number_format($albums->count()) }} albums</span>
            <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70">{{ number_format($genres->count()) }} genres</span>
          </div>
        @endif
      </div>
    </div>

    @if ($query === '')
      <div class="mt-10 glass-card rounded-[2rem] border border-white/10 p-8 text-center">
        <p class="text-lg font-semibold">Start with an artist, track, album, or genre.</p>
        <p class="mt-3 text-white/55">Use the navbar search input to search live, or submit a term to view grouped results here.</p>
      </div>
    @elseif ($resultCount === 0)
      <div class="mt-10 glass-card rounded-[2rem] border border-white/10 p-8 text-center">
        <p class="text-lg font-semibold">No results matched "{{ $query }}".</p>
        <p class="mt-3 text-white/55">Try a broader phrase or search by genre, artist stage name, or album title.</p>
        <div class="mt-6">
          <a href="{{ route('home') }}" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Back to Home</a>
        </div>
      </div>
    @else
      @if ($tracks->isNotEmpty())
        <section class="mt-12">
          <div class="mb-8 flex items-end justify-between gap-6">
            <div>
              <p class="section-kicker">Tracks</p>
              <h2 class="mt-4 font-display text-3xl font-semibold">Playable matches</h2>
            </div>
            <a href="{{ route('listen', ['track' => $tracks->first()->slug]) }}" class="text-sm text-white/55 transition hover:text-neon-cyan">Open in listen view</a>
          </div>
          <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($tracks as $song)
              <x-track-card :song="$song" :favorite-song-ids="$favoriteSongIds" />
            @endforeach
          </div>
        </section>
      @endif

      @if ($artists->isNotEmpty())
        <section class="mt-14">
          <div class="mb-8">
            <p class="section-kicker">Artists</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Profiles worth following</h2>
          </div>
          <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($artists as $artist)
              <x-artist-card :artist="$artist" :followed-artist-ids="$followedArtistIds" />
            @endforeach
          </div>
        </section>
      @endif

      @if ($albums->isNotEmpty())
        <section class="mt-14">
          <div class="mb-8">
            <p class="section-kicker">Albums</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Release collections</h2>
          </div>
          <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($albums as $album)
              <a href="{{ route('listen', ['album' => $album->slug]) }}" class="glass-card group overflow-hidden rounded-[1.75rem] border border-white/10 transition duration-300 hover:-translate-y-1 hover:border-neon-cyan/30 hover:shadow-[0_0_28px_rgba(59,242,255,0.12)]">
                <div class="relative">
                  <img src="{{ $album->cover_image_url }}" alt="{{ $album->title }}" class="h-64 w-full object-cover transition duration-500 group-hover:scale-105">
                  <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/10 to-transparent"></div>
                  <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between gap-3">
                    <div>
                      <p class="text-xs uppercase tracking-[0.28em] text-neon-cyan">{{ $album->genre ?: 'Open Format' }}</p>
                      <p class="mt-2 text-lg font-semibold text-white">{{ $album->title }}</p>
                    </div>
                    <span class="rounded-full border border-white/15 bg-slate-950/70 px-3 py-2 text-xs text-white/75">Album</span>
                  </div>
                </div>
                <div class="p-5">
                  <p class="text-sm text-white/55">{{ $album->artist?->name ?? 'Naad-e-Maan' }}</p>
                  @if ($album->description)
                    <p class="mt-3 line-clamp-3 text-sm text-white/45">{{ $album->description }}</p>
                  @endif
                </div>
              </a>
            @endforeach
          </div>
        </section>
      @endif

      @if ($genres->isNotEmpty())
        <section class="mt-14">
          <div class="mb-8">
            <p class="section-kicker">Genres</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Sound lanes to explore</h2>
          </div>
          <div class="grid auto-rows-[18rem] gap-6 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($genres as $genre)
              <x-genre-panel :genre="$genre" />
            @endforeach
          </div>
        </section>
      @endif
    @endif
  </section>
@endsection
