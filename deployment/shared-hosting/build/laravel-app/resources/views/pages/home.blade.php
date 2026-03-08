@extends('layouts.app')

@section('title', 'Naad-e-Maan | Home')

@section('content')
  @php
    $featuredQueue = ($featuredSongs->isNotEmpty() ? $featuredSongs : $latestSongs)->take(4);
    $featuredTrack = $featuredQueue->first();
    $releaseSongs = $latestSongs->take(8);
    $heroArtists = $artists->take(3)->values();
    $categoryPanels = collect([
        [
            'name' => 'Rock',
            'image_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=900&q=80',
            'description' => 'Arena guitars, midnight drums, and stage-light energy.',
        ],
        [
            'name' => 'Pop',
            'image_url' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=900&q=80',
            'description' => 'Hooks, synth shimmer, and luminous vocal melodies.',
        ],
        [
            'name' => 'Hip Hop',
            'image_url' => 'https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=900&q=80',
            'description' => 'Neon bars, hard beats, and street-night storytelling.',
        ],
        [
            'name' => 'Classical',
            'image_url' => 'https://images.unsplash.com/photo-1507838153414-b4b713384a76?auto=format&fit=crop&w=900&q=80',
            'description' => 'Orchestral motion, velvet halls, and emotional resonance.',
        ],
        [
            'name' => 'Jazz',
            'image_url' => 'https://images.unsplash.com/photo-1511192336575-5a79af67a629?auto=format&fit=crop&w=900&q=80',
            'description' => 'Late-night brass, smoky rooms, and improvised soul.',
        ],
    ])->map(function (array $panel) use ($genres) {
        $match = $genres->first(fn ($genre) => strcasecmp($genre->name, $panel['name']) === 0);

        return (object) [
            'name' => $panel['name'],
            'description' => $match?->description ?: $panel['description'],
            'image_url' => $match?->image_url ?: $panel['image_url'],
            'color' => $match?->color ?: '#3bf2ff',
        ];
    });

    $heroPanelClasses = [
        'left-0 top-8 sm:left-4',
        'right-0 top-20 sm:right-6',
        'left-8 bottom-0 sm:left-16',
    ];
  @endphp

  <section id="home" class="relative overflow-hidden">
    <div class="orb orb-cyan left-[8%] top-20 h-32 w-32"></div>
    <div class="orb orb-blue right-[18%] top-16 h-40 w-40"></div>
    <div class="orb orb-pink right-[10%] bottom-10 h-32 w-32"></div>

    <svg class="hero-wave-svg" viewBox="0 0 1440 600" fill="none" aria-hidden="true">
      <path class="hero-wave-path hero-wave-path-a" d="M-40 282C92 229 226 207 347 249C468 291 574 397 707 405C840 413 970 322 1094 274C1218 226 1336 221 1484 277" />
      <path class="hero-wave-path hero-wave-path-b" d="M-60 345C75 399 196 428 331 393C466 358 602 258 724 256C846 254 946 349 1070 389C1194 429 1341 413 1486 356" />
      <path class="hero-wave-path hero-wave-path-c" d="M-40 414C91 354 224 303 353 320C482 337 606 421 734 428C862 435 994 366 1123 335C1252 304 1381 311 1480 350" />
    </svg>

    <div class="mx-auto grid max-w-7xl gap-16 px-6 py-14 lg:grid-cols-[0.92fr_1.08fr] lg:px-8 lg:py-24">
      <div class="relative z-10 flex flex-col justify-center">
        <p class="section-kicker">Premium Music Streaming</p>
        <h1 class="hero-title mt-6 text-5xl font-bold leading-none sm:text-6xl lg:text-7xl">
          <span class="headline-gradient">Naad-e-Maan</span>
        </h1>
        <p class="neon-text mt-6 text-2xl font-semibold sm:text-3xl">The Sound of the Soul</p>
        <p class="mt-6 max-w-2xl text-base leading-8 text-white/68 sm:text-lg">
          A premium neon-lit music destination where sound becomes atmosphere, artists become constellations, and every track moves through the room like resonance.
        </p>
        <div class="mt-10 flex flex-col gap-4 sm:flex-row">
          <a href="{{ route('listen') }}" class="inline-flex items-center justify-center gap-3 rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950 shadow-[0_0_30px_rgba(255,255,255,0.14)]">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-950 text-white">
              <svg viewBox="0 0 24 24" class="ml-0.5 h-4 w-4" fill="currentColor" aria-hidden="true"><path d="M8 6.5v11l9-5.5-9-5.5Z" /></svg>
            </span>
            Start Listening
          </a>
          <a href="{{ route('artists.index') }}" class="glass-card inline-flex items-center justify-center rounded-full border border-white/10 px-6 py-3 text-sm font-medium text-white/85">Explore Artists</a>
        </div>
      </div>

      <div class="relative min-h-[540px]">
        <div class="hero-stage-shell glass-card relative flex h-full items-center justify-center overflow-hidden rounded-[2.5rem] border border-white/10 p-6 sm:p-10">
          <div class="hero-stage-ring"></div>
          <div class="hero-stage-ring hero-stage-ring-secondary"></div>
          @if ($heroArtists->isNotEmpty())
            <div class="hero-stage-portrait relative z-10 w-[clamp(240px,48vw,380px)] overflow-hidden rounded-[2.25rem] border border-white/10 bg-slate-950/40 p-3 shadow-[0_0_48px_rgba(59,242,255,0.16)]">
              <img src="{{ $heroArtists->first()->image_url }}" alt="{{ $heroArtists->first()->name }}" class="aspect-[4/5] w-full rounded-[1.8rem] object-cover">
              <div class="absolute inset-x-7 bottom-7 rounded-[1.3rem] border border-white/10 bg-slate-950/65 px-5 py-4 backdrop-blur-xl">
                <p class="text-xs uppercase tracking-[0.32em] text-neon-cyan">Live Resonance</p>
                <p class="mt-2 text-xl font-semibold">{{ $heroArtists->first()->name }}</p>
              </div>
            </div>
          @endif

          @foreach ($heroArtists as $artist)
            <article class="floating-artist-panel absolute {{ $heroPanelClasses[$loop->index] ?? 'right-4 bottom-4' }} {{ $loop->index === 1 ? 'float-card-delayed' : 'float-card' }}">
              <img src="{{ $artist->image_url }}" alt="{{ $artist->name }}" class="h-16 w-16 rounded-2xl object-cover sm:h-20 sm:w-20">
              <div class="min-w-0">
                <p class="truncate text-sm font-semibold">{{ $artist->name }}</p>
                <p class="mt-1 truncate text-xs uppercase tracking-[0.28em] text-white/45">{{ $artist->genre }}</p>
              </div>
            </article>
          @endforeach

          <div class="hero-stage-wave">
            <span class="wave-bar" style="height: 28px"></span>
            <span class="wave-bar" style="height: 64px"></span>
            <span class="wave-bar" style="height: 42px"></span>
            <span class="wave-bar" style="height: 88px"></span>
            <span class="wave-bar" style="height: 54px"></span>
            <span class="wave-bar" style="height: 76px"></span>
            <span class="wave-bar" style="height: 34px"></span>
          </div>
        </div>
      </div>
    </div>
  </section>

  @if ($featuredTrack)
    <section class="mx-auto max-w-7xl px-6 py-4 lg:px-8">
      <x-featured-track-panel :track="$featuredTrack" :queue="$featuredQueue" />
    </section>
  @endif

  <section class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="mb-8 flex items-end justify-between gap-6">
      <div>
        <p class="section-kicker">Latest Releases</p>
        <h2 class="mt-4 font-display text-3xl font-semibold">Fresh Drops Across the Neon Grid</h2>
      </div>
      <a href="{{ route('listen') }}" class="text-sm text-white/55 transition hover:text-neon-cyan">Open full library</a>
    </div>
    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-4">
      @foreach ($releaseSongs as $song)
        <x-track-card :song="$song" :favorite-song-ids="$favoriteSongIds" />
      @endforeach
    </div>
  </section>

  <section id="genres" class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="mb-8 flex items-end justify-between gap-6">
      <div>
        <p class="section-kicker">Categories</p>
        <h2 class="mt-4 font-display text-3xl font-semibold">Vertical Panels of Sound</h2>
      </div>
      <a href="{{ route('listen') }}" class="text-sm text-white/55 transition hover:text-neon-cyan">Browse all genres</a>
    </div>
    <div class="grid gap-5 lg:grid-cols-5">
      @foreach ($categoryPanels as $genre)
        <x-genre-panel :genre="$genre" />
      @endforeach
    </div>
  </section>

  <section id="videos" class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="grid gap-8 xl:grid-cols-[0.8fr_1.2fr] xl:items-center">
      <div>
        <p class="section-kicker">Video Spotlight</p>
        <h2 class="mt-4 font-display text-4xl font-semibold">Sound You Can See</h2>
        <p class="mt-6 max-w-2xl text-base leading-8 text-white/65">
          Dive into a live visual moment from the platform and move from audio-only playback into the full performance atmosphere.
        </p>
        <div class="mt-8 flex flex-wrap gap-3 text-sm text-white/55">
          <span class="rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-neon-cyan">Live Session</span>
          <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">Studio Lighting</span>
          <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">Performance Cut</span>
        </div>
      </div>
      <div class="video-shell shadow-[0_0_42px_rgba(59,242,255,0.08)]">
        <iframe src="https://www.youtube.com/embed/{{ $homepageVideoId }}?rel=0" title="Naad-e-Maan Video Spotlight" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
      </div>
    </div>
  </section>

  <section class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="mb-8 flex items-end justify-between gap-6">
      <div>
        <p class="section-kicker">Artists</p>
        <h2 class="mt-4 font-display text-3xl font-semibold">Avatar Scroller From the Night Shift</h2>
      </div>
      <a href="{{ route('artists.index') }}" class="text-sm text-white/55 transition hover:text-neon-cyan">Meet every artist</a>
    </div>
    <div class="artist-strip">
      @foreach ($artists as $artist)
        <div class="min-w-[220px]">
          <x-artist-card :artist="$artist" :followed-artist-ids="$followedArtistIds" />
        </div>
      @endforeach
    </div>
  </section>

  <section id="creators" class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="creator-banner grid gap-8 overflow-hidden rounded-[2.25rem] border border-white/10 p-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-center lg:p-10">
      <div class="relative z-10">
        <p class="section-kicker">Calling All Creators</p>
        <h2 class="mt-4 font-display text-4xl font-semibold">Bring Your Next Signal to Naad-e-Maan</h2>
        <p class="mt-5 max-w-2xl text-base leading-8 text-white/65">
          Upload new releases, build albums, monitor track performance, and shape your artist profile from a premium creator workspace designed for modern streaming.
        </p>
        <div class="mt-8 flex flex-col gap-4 sm:flex-row">
          <a href="{{ auth()->check() && auth()->user()->isCreator() ? route('creator.dashboard') : route('register') }}" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950">
            {{ auth()->check() && auth()->user()->isCreator() ? 'Open Creator Dashboard' : 'Become a Creator' }}
          </a>
          <a href="{{ route('artists.index') }}" class="glass-card rounded-full border border-white/10 px-6 py-3 text-sm font-semibold text-white">Explore the Artist Scene</a>
        </div>
      </div>

      <div class="relative min-h-[260px]">
        <div class="creator-visual-shell absolute inset-0 mx-auto max-w-[340px] rounded-[2rem] border border-white/10 bg-slate-950/40">
          <div class="creator-headband"></div>
          <div class="creator-ear creator-ear-left"></div>
          <div class="creator-ear creator-ear-right"></div>
          <div class="creator-console">
            <span></span><span></span><span></span><span></span><span></span>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
