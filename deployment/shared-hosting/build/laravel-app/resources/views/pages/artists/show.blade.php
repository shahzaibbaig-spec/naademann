@extends('layouts.app')

@section('title', 'Naad-e-Maan | '.$artist->name)

@section('content')
  @php
    $serializeTrack = static function ($song): array {
        return [
            'id' => $song->id,
            'title' => $song->title,
            'slug' => $song->slug,
            'genre' => $song->genre,
            'duration' => $song->duration,
            'audio_url' => $song->audio_url,
            'cover_image_url' => $song->cover_image_url,
            'streams_count' => $song->streams_count,
            'artist' => $song->artist ? [
                'id' => $song->artist->id,
                'name' => $song->artist->name,
                'slug' => $song->artist->slug,
            ] : null,
            'album' => $song->album ? [
                'id' => $song->album->id,
                'title' => $song->album->title,
                'slug' => $song->album->slug,
            ] : null,
        ];
    };

    $socials = [
        ['label' => 'Instagram', 'href' => '#', 'icon' => '<path d="M16.5 3h-9A4.5 4.5 0 0 0 3 7.5v9A4.5 4.5 0 0 0 7.5 21h9a4.5 4.5 0 0 0 4.5-4.5v-9A4.5 4.5 0 0 0 16.5 3Z" /><path d="M12 8.25A3.75 3.75 0 1 0 15.75 12 3.75 3.75 0 0 0 12 8.25Z" /><path d="M17.625 7.125h.008v.008h-.008Z" />'],
        ['label' => 'YouTube', 'href' => '#videos', 'icon' => '<path d="M21.6 8.2a2.86 2.86 0 0 0-2-2C17.84 5.75 12 5.75 12 5.75s-5.84 0-7.6.45a2.86 2.86 0 0 0-2 2A29.4 29.4 0 0 0 2 12a29.4 29.4 0 0 0 .4 3.8 2.86 2.86 0 0 0 2 2c1.76.45 7.6.45 7.6.45s5.84 0 7.6-.45a2.86 2.86 0 0 0 2-2A29.4 29.4 0 0 0 22 12a29.4 29.4 0 0 0-.4-3.8Z" /><path d="m10 15.5 5-3.5-5-3.5Z" />'],
        ['label' => 'SoundCloud', 'href' => '#', 'icon' => '<path d="M7.5 18.5h9.25a3.75 3.75 0 0 0 .6-7.45A5.5 5.5 0 0 0 7.47 9.5" /><path d="M4.75 10.5v8" /><path d="M7 9.25v9.25" /><path d="M2.5 12v6.5" />'],
    ];
  @endphp

  <section class="relative overflow-hidden">
    <div class="artist-cover-banner">
      <img src="{{ $coverImage }}" alt="{{ $artist->name }} cover" class="h-full w-full object-cover">
      <div class="artist-cover-grid"></div>
      <div class="artist-cover-gradient"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-6 pb-14 lg:px-8">
      <div class="artist-profile-shell -mt-24 rounded-[2.25rem] border border-white/10 bg-slate-950/62 p-6 backdrop-blur-2xl sm:p-8 lg:-mt-28 lg:p-10">
        <div class="grid gap-10 xl:grid-cols-[auto_1fr_auto] xl:items-end">
          <div class="artist-profile-overlap">
            <div class="artist-ring p-[10px] shadow-[0_0_60px_rgba(59,242,255,0.18)]">
              <img src="{{ $artist->image_url }}" alt="{{ $artist->name }}" class="h-40 w-40 object-cover sm:h-48 sm:w-48">
            </div>
          </div>

          <div class="min-w-0">
            <p class="section-kicker">Artist Profile</p>
            <h1 class="mt-4 font-display text-4xl font-semibold sm:text-5xl lg:text-6xl">{{ $artist->name }}</h1>
            <p class="mt-5 max-w-3xl text-base leading-8 text-white/65">{{ $artist->bio }}</p>

            <div class="mt-6 flex flex-wrap gap-3">
              <span class="rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-sm text-neon-cyan">{{ $artist->genre }}</span>
              <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/70">Neon Performance</span>
              <span class="rounded-full border border-neon-pink/20 bg-neon-pink/10 px-4 py-2 text-sm text-neon-pink">Live Resonance</span>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
              @foreach ($socials as $social)
                <a href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}" class="artist-social-chip">
                  <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">{!! $social['icon'] !!}</svg>
                  <span>{{ $social['label'] }}</span>
                </a>
              @endforeach
            </div>
          </div>

          <div class="flex flex-col gap-4 xl:min-w-[220px]">
            @if ($topTracks->isNotEmpty())
              <button type="button" data-track-play-context=".artist-top-track-list" class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950">Play All</button>
            @endif
            @auth
              <button type="button" data-follow-toggle data-follow-url="{{ route('ajax.artists.follow', $artist) }}" data-artist-id="{{ $artist->id }}" aria-pressed="{{ in_array($artist->id, $followedArtistIds, true) ? 'true' : 'false' }}" class="glass-card inline-flex items-center justify-center rounded-full border border-white/10 px-6 py-3 text-sm font-medium text-white/85">
                <span class="follow-label">{{ in_array($artist->id, $followedArtistIds, true) ? 'Following' : 'Follow Artist' }}</span>
              </button>
            @else
              <a href="{{ route('login') }}" class="glass-card inline-flex items-center justify-center rounded-full border border-white/10 px-6 py-3 text-sm font-medium text-white/85">Login to Follow</a>
            @endauth
          </div>
        </div>

        <div class="mt-10 grid gap-5 md:grid-cols-3">
          <article class="artist-stat-card">
            <p class="text-sm uppercase tracking-[0.28em] text-white/45">Listeners</p>
            <p class="mt-3 stat-number text-4xl font-semibold text-neon-cyan" data-counter="{{ $stats['listeners'] }}">0</p>
            <p class="mt-3 text-sm text-white/55">Monthly listeners resonating with this profile.</p>
          </article>
          <article class="artist-stat-card">
            <p class="text-sm uppercase tracking-[0.28em] text-white/45">Followers</p>
            <p class="mt-3 stat-number text-4xl font-semibold text-neon-pink" data-counter="{{ $stats['followers'] }}">0</p>
            <p class="mt-3 text-sm text-white/55">Fans tracking every drop and live signal.</p>
          </article>
          <article class="artist-stat-card">
            <p class="text-sm uppercase tracking-[0.28em] text-white/45">Streams</p>
            <p class="mt-3 stat-number text-4xl font-semibold text-neon-blue" data-counter="{{ $stats['streams'] }}">0</p>
            <p class="mt-3 text-sm text-white/55">Total plays across the approved Naad-e-Maan catalog.</p>
          </article>
        </div>
      </div>
    </div>
  </section>

  <section class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="grid gap-8 xl:grid-cols-[1.02fr_0.98fr]">
      <section class="glass-card rounded-[2rem] border border-white/10 p-6 sm:p-8">
        <div class="mb-8 flex items-end justify-between gap-4">
          <div>
            <p class="section-kicker">Top Tracks</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Most Played Right Now</h2>
          </div>
          <span class="rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-xs uppercase tracking-[0.28em] text-neon-cyan">{{ $topTracks->count() }} Tracks</span>
        </div>

        <div class="artist-top-track-list space-y-4">
          @foreach ($topTracks as $song)
            @php
              $trackPayload = $serializeTrack($song);
            @endphp
            <article class="artist-track-row" data-track-card='@json($trackPayload)'>
              <div class="flex min-w-0 items-center gap-4">
                <button type="button" data-track-play class="artist-track-play" aria-label="Play {{ $song->title }}">
                  <svg viewBox="0 0 24 24" class="ml-0.5 h-4 w-4" fill="currentColor" aria-hidden="true"><path d="M8 6.5v11l9-5.5-9-5.5Z" /></svg>
                </button>
                <img src="{{ $song->cover_image_url }}" alt="{{ $song->title }}" class="h-16 w-16 rounded-[1.15rem] object-cover">
                <div class="min-w-0">
                  <p class="truncate font-semibold">{{ $song->title }}</p>
                  <p class="mt-1 truncate text-sm text-white/50">{{ $song->album?->title ?? 'Single Release' }} - {{ $song->genre }}</p>
                </div>
              </div>
              <div class="flex items-center gap-4 text-sm text-white/50">
                <span>{{ number_format($song->streams_count) }} streams</span>
                <span>{{ gmdate('i:s', $song->duration) }}</span>
              </div>
            </article>
          @endforeach
        </div>
      </section>

      <section id="videos" class="glass-card rounded-[2rem] border border-white/10 p-6 sm:p-8">
        <p class="section-kicker">Featured Video</p>
        <h2 class="mt-4 font-display text-3xl font-semibold">Visual Spotlight</h2>
        <p class="mt-4 max-w-2xl text-base leading-8 text-white/65">
          A highlighted live moment that extends the artist atmosphere beyond the player and into a full-screen visual performance.
        </p>
        <div class="video-shell mt-8">
          <iframe src="https://www.youtube.com/embed/{{ $featuredVideoId }}?rel=0" title="{{ $artist->name }} featured video" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
        </div>
      </section>
    </div>
  </section>

  <section class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="mb-8 flex items-end justify-between gap-6">
      <div>
        <p class="section-kicker">Releases</p>
        <h2 class="mt-4 font-display text-3xl font-semibold">Catalog From {{ $artist->name }}</h2>
      </div>
    </div>
    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
      @forelse ($songs as $song)
        <x-track-card :song="$song" :favorite-song-ids="$favoriteSongIds" :show-artist="false" />
      @empty
        <article class="glass-card rounded-[1.75rem] border border-dashed border-white/10 p-8 text-white/55">No approved releases are available yet.</article>
      @endforelse
    </div>
  </section>

  <section class="mx-auto max-w-7xl px-6 py-8 lg:px-8">
    <div class="grid gap-8 xl:grid-cols-[1.05fr_0.95fr]">
      <section class="glass-card rounded-[2rem] border border-white/10 p-6 sm:p-8">
        <div class="mb-8 flex items-end justify-between gap-6">
          <div>
            <p class="section-kicker">Collaborators</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Circular Avatar Slider</h2>
          </div>
          <span class="text-sm text-white/50">Shared genre energy</span>
        </div>
        <div class="artist-strip collaborator-strip">
          @foreach ($collaborators as $collaborator)
            <a href="{{ route('artists.show', $collaborator) }}" class="collaborator-avatar-card">
              <div class="artist-ring mx-auto w-fit">
                <img src="{{ $collaborator->image_url }}" alt="{{ $collaborator->name }}" class="h-24 w-24 object-cover">
              </div>
              <p class="mt-4 text-center font-semibold">{{ $collaborator->name }}</p>
              <p class="mt-1 text-center text-sm text-white/50">{{ $collaborator->genre }}</p>
            </a>
          @endforeach
        </div>
      </section>

      <section class="glass-card rounded-[2rem] border border-white/10 p-6 sm:p-8">
        <div class="mb-8 flex items-end justify-between gap-6">
          <div>
            <p class="section-kicker">Related Artists</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Keep the Resonance Going</h2>
          </div>
        </div>
        <div class="grid gap-5 sm:grid-cols-2">
          @foreach ($relatedArtists as $relatedArtist)
            <x-artist-card :artist="$relatedArtist" :followed-artist-ids="$followedArtistIds" />
          @endforeach
        </div>
      </section>
    </div>
  </section>
@endsection
