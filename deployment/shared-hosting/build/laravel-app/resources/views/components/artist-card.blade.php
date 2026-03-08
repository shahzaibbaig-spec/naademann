@props([
    'artist',
    'followedArtistIds' => [],
    'showFollow' => true,
])

@php
    $following = in_array($artist->id, $followedArtistIds, true);
@endphp

<article class="artist-card glass-card rounded-[1.75rem] border border-white/10 p-5">
  <a href="{{ route('artists.show', $artist) }}" class="block">
    <div class="artist-ring mx-auto w-fit">
      <img src="{{ $artist->image_url }}" alt="{{ $artist->name }}" class="h-24 w-24 object-cover">
    </div>
    <h3 class="mt-4 text-center font-semibold">{{ $artist->name }}</h3>
    <p class="mt-1 text-center text-sm text-white/50">{{ $artist->genre }}</p>
    <p class="mt-3 text-center text-xs uppercase tracking-[0.28em] text-neon-cyan/75">{{ number_format($artist->monthly_listeners) }} listeners</p>
  </a>

  @if ($showFollow)
    <div class="mt-4">
      @auth
        <button
          type="button"
          data-follow-toggle
          data-follow-url="{{ route('ajax.artists.follow', $artist) }}"
          data-artist-id="{{ $artist->id }}"
          aria-pressed="{{ $following ? 'true' : 'false' }}"
          class="glass-card inline-flex w-full items-center justify-center gap-2 rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-white/80"
        >
          <span class="follow-label">{{ $following ? 'Following' : 'Follow Artist' }}</span>
        </button>
      @else
        <a href="{{ route('login') }}" class="glass-card inline-flex w-full items-center justify-center rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-white/80">Login to Follow</a>
      @endauth
    </div>
  @endif
</article>
