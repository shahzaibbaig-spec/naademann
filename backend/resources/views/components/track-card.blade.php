@props([
    'song',
    'favoriteSongIds' => [],
    'showArtist' => true,
])

@php
    $trackPayload = [
        'id' => $song->id,
        'title' => $song->title,
        'slug' => $song->slug,
        'genre' => $song->genre,
        'duration' => $song->duration,
        'audio_url' => $song->audio_url,
        'cover_image_url' => $song->cover_image_url,
        'is_featured' => (bool) $song->is_featured,
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
    $isFavorite = in_array($song->id, $favoriteSongIds, true);
    $favoriteIcon = $isFavorite ? '&#9829;' : '&#9825;';
    $isQueueable = (bool) $song->is_featured;
@endphp

<article class="album-card glass-card overflow-hidden rounded-[1.75rem] border border-white/10" @if ($isQueueable) data-track-card='@json($trackPayload)' @endif>
  <div class="relative">
    <img src="{{ $song->cover_image_url }}" alt="{{ $song->title }}" class="h-64 w-full object-cover">
    <div class="album-overlay absolute inset-0 flex items-end justify-between p-5">
      <div>
        <p class="text-sm text-white/60">{{ $song->album?->title ?? $song->title }}</p>
        <p class="text-xs uppercase tracking-[0.28em] text-neon-cyan">{{ $song->genre }}</p>
      </div>
      <div class="flex items-center gap-3">
        @auth
          <button type="button" data-favorite-toggle data-favorite-url="{{ route('ajax.songs.favorite', $song) }}" data-song-id="{{ $song->id }}" aria-pressed="{{ $isFavorite ? 'true' : 'false' }}" class="flex h-12 w-12 items-center justify-center rounded-full border border-white/20 bg-slate-950/70 text-xl leading-none text-white">
            <span class="favorite-label">{!! $favoriteIcon !!}</span>
          </button>
          <button type="button" data-playlist-open data-song-id="{{ $song->id }}" data-playlist-url="{{ route('ajax.songs.playlist', $song) }}" class="flex h-12 w-12 items-center justify-center rounded-full border border-white/20 bg-slate-950/70 text-white">+</button>
        @else
          <a href="{{ route('login') }}" class="flex h-12 w-12 items-center justify-center rounded-full border border-white/20 bg-slate-950/70 text-xl leading-none text-white">&#9825;</a>
        @endauth
        @if ($isQueueable)
          <button type="button" data-track-play class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-950" aria-label="Play {{ $song->title }}">
            <svg viewBox="0 0 24 24" class="ml-0.5 h-5 w-5" fill="currentColor" aria-hidden="true">
              <path d="M8 6.5v11l9-5.5-9-5.5Z" />
            </svg>
          </button>
        @else
          <button type="button" disabled title="Only featured songs can be queued" class="flex h-12 w-12 cursor-not-allowed items-center justify-center rounded-full bg-white/30 text-slate-950/60">
            <svg viewBox="0 0 24 24" class="ml-0.5 h-5 w-5" fill="currentColor" aria-hidden="true">
              <path d="M8 6.5v11l9-5.5-9-5.5Z" />
            </svg>
          </button>
        @endif
      </div>
    </div>
  </div>
  <div class="p-5">
    <div class="flex items-start justify-between gap-4">
      <div class="min-w-0">
        <h3 class="truncate text-lg font-semibold">{{ $song->title }}</h3>
        @if ($showArtist && $song->artist)
          <a href="{{ route('artists.show', $song->artist) }}" class="mt-1 block truncate text-sm text-white/55 transition hover:text-neon-cyan">{{ $song->artist->name }}</a>
        @endif
        @unless ($isQueueable)
          <p class="mt-2 text-xs uppercase tracking-[0.24em] text-white/35">Not in queue</p>
        @endunless
      </div>
      <span class="text-xs uppercase tracking-[0.28em] text-white/40">{{ gmdate('i:s', $song->duration) }}</span>
    </div>
  </div>
</article>
