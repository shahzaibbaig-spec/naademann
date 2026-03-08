@props(['queue' => []])

<div data-mini-player class="pointer-events-none fixed bottom-6 left-1/2 z-50 hidden w-[min(95vw,980px)] -translate-x-1/2">
  <div class="pointer-events-auto glass-card rounded-[1.75rem] border border-white/10 p-4 shadow-[0_0_45px_rgba(59,242,255,0.12)]">
    <script type="application/json" data-default-queue>@json($queue)</script>
    <audio data-player-audio preload="metadata"></audio>
    <div class="grid gap-4 lg:grid-cols-[auto_1fr_auto] lg:items-center">
      <div class="flex items-center gap-4">
        <img data-player-cover src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=400&q=80" alt="Now playing cover" class="h-16 w-16 rounded-[1.25rem] object-cover">
        <div class="min-w-0">
          <p data-player-title class="truncate font-semibold">Ready to play</p>
          <a data-player-artist href="{{ route('artists.index') }}" class="mt-1 block truncate text-sm text-white/55 transition hover:text-neon-cyan">Naad-e-Maan</a>
          <p data-player-copy class="mt-1 truncate text-xs text-white/40">Build a queue from tracks, artists, and playlists.</p>
        </div>
      </div>

      <div>
        <div class="flex items-center justify-between text-xs text-white/50">
          <span data-player-current>0:00</span>
          <span data-player-duration>0:00</span>
        </div>
        <input data-player-progress type="range" min="0" max="100" value="0" class="range-slider mt-3">
        <div class="mt-3 flex items-center justify-between gap-4">
          <div data-player-waveform class="mini-player-waveform player-waveform is-paused min-h-0 justify-start gap-1">
            <span class="player-wave" style="height: 18px"></span>
            <span class="player-wave" style="height: 26px"></span>
            <span class="player-wave" style="height: 22px"></span>
            <span class="player-wave" style="height: 30px"></span>
            <span class="player-wave" style="height: 24px"></span>
          </div>
          <div class="flex items-center gap-3">
            <p class="text-xs text-white/45"><span data-player-queue-count>0</span> tracks in queue</p>
            <button type="button" data-player-queue-toggle class="player-control flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-2 text-xs font-medium text-white/70 transition hover:text-white" aria-expanded="false" aria-controls="mini-player-queue" aria-label="Show queue">
              <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h10" />
              </svg>
              <span>Queue</span>
            </button>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="button" data-player-prev class="player-control glass-card flex h-12 w-12 items-center justify-center rounded-full border border-white/10 text-white/80" aria-label="Previous track">
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M15.5 6.5v11l-8-5.5 8-5.5Z" /><path d="M18 6.5v11" /></svg>
        </button>
        <button type="button" data-player-toggle class="flex h-14 w-14 items-center justify-center rounded-full bg-white text-slate-950" aria-label="Play queue">
          <span data-player-icon><svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor" aria-hidden="true"><path d="M8 6.5v11l9-5.5-9-5.5Z" /></svg></span>
        </button>
        <button type="button" data-player-next class="player-control glass-card flex h-12 w-12 items-center justify-center rounded-full border border-white/10 text-white/80" aria-label="Next track">
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M8.5 6.5v11l8-5.5-8-5.5Z" /><path d="M6 6.5v11" /></svg>
        </button>
        <div class="hidden min-w-[160px] items-center gap-3 md:flex">
          <svg viewBox="0 0 24 24" class="h-4 w-4 text-white/50" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path d="M5 9v6h4l5 4V5L9 9H5Z" />
            <path d="M18 9.5a3.5 3.5 0 0 1 0 5" />
          </svg>
          <input data-player-volume type="range" min="0" max="100" value="72" class="range-slider">
        </div>
      </div>
    </div>

    <div id="mini-player-queue" data-player-queue-drawer class="player-queue-drawer" aria-hidden="true">
      <div class="mt-4 border-t border-white/10 pt-4">
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-xs uppercase tracking-[0.35em] text-neon-cyan/70">Up Next</p>
            <p class="mt-2 text-sm text-white/55">Jump through your listening queue from anywhere on the site.</p>
          </div>
          <button type="button" data-player-queue-close class="rounded-full border border-white/10 px-4 py-2 text-xs font-medium text-white/55 transition hover:border-neon-cyan/30 hover:text-white">
            Close
          </button>
        </div>

        <div data-player-queue-empty class="mt-4 rounded-[1.5rem] border border-dashed border-white/10 bg-white/[0.03] px-4 py-5 text-sm text-white/45">
          Queue tracks from album cards, artist pages, and playlists to build your listening session.
        </div>

        <div data-player-queue-list class="player-queue-list mt-4 grid gap-3" role="list"></div>
      </div>
    </div>
  </div>
</div>

<div data-playlist-modal class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-950/80 px-6 backdrop-blur-xl">
  <div class="glass-card w-full max-w-md rounded-[2rem] border border-white/10 p-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <p class="section-kicker">Add To Playlist</p>
        <h3 class="mt-3 font-display text-2xl font-semibold">Save This Track</h3>
      </div>
      <button type="button" data-playlist-modal-close class="text-white/55 transition hover:text-white">Close</button>
    </div>
    <form data-playlist-form class="mt-6 grid gap-4">
      <input type="hidden" name="song_id" value="">
      <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
        <span class="mb-2 block text-sm text-white/50">Choose an existing playlist</span>
        <select name="playlist_id" class="w-full bg-transparent text-white outline-none">
          <option value="" class="bg-slate-950">Select a playlist</option>
        </select>
      </label>
      <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
        <span class="mb-2 block text-sm text-white/50">Or create a new playlist</span>
        <input name="playlist_name" type="text" placeholder="Midnight Signals" class="w-full bg-transparent text-white outline-none placeholder:text-white/30">
      </label>
      <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Save Track</button>
      <div data-playlist-feedback class="hidden rounded-[1.25rem] px-4 py-3 text-sm"></div>
    </form>
  </div>
</div>
