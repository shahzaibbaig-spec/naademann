@props([
    'track',
    'queue' => collect(),
])

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
    };

    $featuredQueue = collect($queue)->map($serializeTrack)->values()->all();
    $featuredTrack = $serializeTrack($track);
@endphp

<section data-featured-player class="featured-player-card glass-card overflow-hidden rounded-[2rem] border border-white/10 p-6 sm:p-8">
  <script type="application/json" data-featured-queue>@json($featuredQueue)</script>
  <div class="grid gap-8 lg:grid-cols-[auto_1fr_auto] lg:items-center">
    <div class="player-cover-frame music-pulse relative mx-auto w-full max-w-[280px] overflow-hidden rounded-[2rem] border border-white/10">
      <img data-featured-cover src="{{ $featuredTrack['cover_image_url'] }}" alt="{{ $featuredTrack['title'] }}" class="aspect-square w-full object-cover">
      <div class="featured-player-sheen absolute inset-0"></div>
    </div>

    <div class="min-w-0">
      <p class="section-kicker">Featured Track</p>
      <h2 data-featured-title class="mt-4 font-display text-3xl font-semibold sm:text-4xl">{{ $featuredTrack['title'] }}</h2>
      <a data-featured-artist href="{{ $featuredTrack['artist'] ? route('artists.show', $featuredTrack['artist']['slug']) : route('artists.index') }}" class="mt-3 inline-flex items-center gap-2 text-base text-white/60 transition hover:text-neon-cyan">
        <span class="h-2 w-2 rounded-full bg-neon-cyan"></span>
        {{ $featuredTrack['artist']['name'] ?? 'Naad-e-Maan' }}
      </a>
      <div class="mt-5 flex flex-wrap gap-3 text-xs uppercase tracking-[0.3em] text-white/45">
        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2">{{ $featuredTrack['genre'] }}</span>
        <span class="rounded-full border border-neon-pink/20 bg-neon-pink/10 px-4 py-2 text-neon-pink">{{ $featuredTrack['album']['title'] ?? 'Single Release' }}</span>
      </div>
      <div data-featured-waveform class="player-waveform is-paused mt-8 justify-start gap-2">
        <span class="player-wave" style="height: 32px"></span>
        <span class="player-wave" style="height: 68px"></span>
        <span class="player-wave" style="height: 44px"></span>
        <span class="player-wave" style="height: 86px"></span>
        <span class="player-wave" style="height: 56px"></span>
        <span class="player-wave" style="height: 92px"></span>
        <span class="player-wave" style="height: 48px"></span>
        <span class="player-wave" style="height: 72px"></span>
      </div>
      <div class="mt-6">
        <div class="mb-3 flex items-center justify-between text-xs text-white/50">
          <span data-featured-current>0:00</span>
          <span data-featured-duration>{{ gmdate('i:s', $featuredTrack['duration']) }}</span>
        </div>
        <input data-featured-progress type="range" min="0" max="{{ max($featuredTrack['duration'], 1) }}" value="0" class="player-slider">
      </div>
    </div>

    <div class="mx-auto flex w-full max-w-[240px] flex-col gap-5">
      <div class="grid grid-cols-3 gap-3">
        <button type="button" data-featured-prev class="player-control glass-card flex h-14 items-center justify-center rounded-full border border-white/10 text-white/80" aria-label="Previous featured track">
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M15.5 6.5v11l-8-5.5 8-5.5Z" /><path d="M18 6.5v11" /></svg>
        </button>
        <button type="button" data-featured-toggle class="player-control player-control-primary flex h-14 items-center justify-center rounded-full bg-white text-slate-950" aria-label="Play featured queue">
          <span data-featured-icon><svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor" aria-hidden="true"><path d="M8 6.5v11l9-5.5-9-5.5Z" /></svg></span>
        </button>
        <button type="button" data-featured-next class="player-control glass-card flex h-14 items-center justify-center rounded-full border border-white/10 text-white/80" aria-label="Next featured track">
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M8.5 6.5v11l8-5.5-8-5.5Z" /><path d="M6 6.5v11" /></svg>
        </button>
      </div>

      <div class="glass-card rounded-[1.5rem] border border-white/10 p-4">
        <div class="flex items-center justify-between text-sm text-white/60">
          <span>Volume</span>
          <span data-featured-volume-label>72%</span>
        </div>
        <input data-featured-volume type="range" min="0" max="100" value="72" class="player-slider mt-4">
      </div>

      <div class="rounded-[1.5rem] border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-4 text-sm leading-7 text-white/65">
        Press play to send this featured queue into the floating player and keep listening while you explore the platform.
      </div>
    </div>
  </div>
</section>
