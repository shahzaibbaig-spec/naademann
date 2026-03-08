document.addEventListener("DOMContentLoaded", () => {
  setupCustomPlayer().catch((error) => {
    console.error(error);
  });
});

async function setupCustomPlayer() {
  const platform = window.NaadPlatform;
  const root = document.querySelector("[data-player-root]");
  if (!platform || !root) {
    return;
  }

  const elements = {
    audio: root.querySelector("[data-player-audio]"),
    cover: root.querySelector("[data-player-cover]"),
    title: root.querySelector("[data-player-title]"),
    artist: root.querySelector("[data-player-artist]"),
    copy: root.querySelector("[data-player-copy]"),
    badge: root.querySelector("[data-player-badge]"),
    status: root.querySelector("[data-player-status]"),
    current: root.querySelector("[data-player-current]"),
    duration: root.querySelector("[data-player-duration]"),
    progress: root.querySelector("[data-player-progress]"),
    play: root.querySelector("[data-player-play]"),
    playIcon: root.querySelector("[data-player-play-icon]"),
    prev: root.querySelector("[data-player-prev]"),
    next: root.querySelector("[data-player-next]"),
    equalizer: root.querySelector("[data-player-equalizer]"),
    volume: root.querySelector("[data-player-volume]"),
    volumeLabel: root.querySelector("[data-volume-label]"),
    playlists: root.querySelector("[data-player-playlists]"),
    libraryAll: root.querySelector("[data-player-library-all]"),
    queue: root.querySelector("[data-player-queue]"),
    queueCount: root.querySelector("[data-player-queue-count]"),
    search: root.querySelector("[data-player-search]"),
  };

  if (Object.values(elements).some((entry) => !entry)) {
    return;
  }

  const params = new URLSearchParams(window.location.search);
  const state = {
    library: [],
    playlists: [],
    queue: [],
    queueLabel: "All Songs",
    queueOwner: "",
    currentIndex: 0,
    activePlaylistSlug: params.get("playlist") || "",
    activeArtistSlug: params.get("artist") || "",
    search: "",
    recorded: new Set(),
  };

  elements.audio.volume = Number(elements.volume.value) / 100;
  syncVolume();

  elements.play.addEventListener("click", () => {
    if (!state.queue.length) {
      return;
    }

    if (elements.audio.paused) {
      elements.audio.play().catch(() => {
        // Ignore gesture failures.
      });
    } else {
      elements.audio.pause();
    }
  });

  elements.prev.addEventListener("click", () => {
    shiftTrack(-1);
  });

  elements.next.addEventListener("click", () => {
    shiftTrack(1);
  });

  elements.progress.addEventListener("input", () => {
    elements.audio.currentTime = Number(elements.progress.value);
    syncProgress();
  });

  elements.volume.addEventListener("input", () => {
    elements.audio.volume = Number(elements.volume.value) / 100;
    syncVolume();
  });

  elements.search.addEventListener("input", () => {
    state.search = elements.search.value.trim().toLowerCase();
    renderQueue();
  });

  elements.libraryAll.addEventListener("click", async () => {
    await loadLibraryQueue();
  });

  elements.playlists.addEventListener("click", async (event) => {
    const trigger = event.target.closest("[data-player-playlist]");
    if (!trigger) {
      return;
    }

    await loadPlaylistQueue(trigger.dataset.playerPlaylist);
  });

  elements.queue.addEventListener("click", (event) => {
    const trigger = event.target.closest("[data-player-queue-index]");
    if (!trigger) {
      return;
    }

    state.currentIndex = Number(trigger.dataset.playerQueueIndex);
    loadCurrentTrack(true);
  });

  elements.audio.addEventListener("loadedmetadata", syncProgress);
  elements.audio.addEventListener("timeupdate", syncProgress);
  elements.audio.addEventListener("ended", () => {
    shiftTrack(1);
  });
  elements.audio.addEventListener("play", async () => {
    syncPlaybackState();
    const currentSong = getCurrentSong();
    if (!currentSong || state.recorded.has(currentSong.slug)) {
      return;
    }

    state.recorded.add(currentSong.slug);
    try {
      await platform.recordSongStream(currentSong);
    } catch (error) {
      state.recorded.delete(currentSong.slug);
    }
  });
  elements.audio.addEventListener("pause", syncPlaybackState);

  try {
    const [songsPayload, playlistsPayload] = await Promise.all([platform.loadSongs(), platform.loadPlaylists()]);
    state.library = songsPayload.data || [];
    state.playlists = playlistsPayload.data || [];
    renderPlaylists();

    if (params.get("playlist")) {
      await loadPlaylistQueue(params.get("playlist"), params.get("track") || "");
    } else {
      await loadLibraryQueue(params.get("track") || "");
    }
  } catch (error) {
    elements.status.textContent = "Offline";
    elements.queue.innerHTML = `
      <article class="rounded-[1.5rem] border border-dashed border-white/10 p-6 text-center text-white/60">
        ${platform.escapeHtml(error.message)}
      </article>
    `;
  }

  async function loadLibraryQueue(trackSlug = "") {
    let songs = state.library;

    if (state.activeArtistSlug) {
      const payload = await platform.loadSongs({ artist: state.activeArtistSlug });
      songs = payload.data || [];
    }

    state.activePlaylistSlug = "";
    state.queueLabel = state.activeArtistSlug ? "Artist Catalog" : "All Songs";
    state.queueOwner = state.activeArtistSlug ? state.activeArtistSlug : "Naad-e-Maan";
    setQueue(songs, trackSlug);
    renderPlaylists();
  }

  async function loadPlaylistQueue(slug, trackSlug = "") {
    const payload = await platform.loadPlaylist(slug);
    const playlist = payload.data;
    state.activePlaylistSlug = slug;
    state.activeArtistSlug = "";
    state.queueLabel = playlist.name;
    state.queueOwner = playlist.owner?.name || "Naad-e-Maan";
    setQueue(playlist.songs || [], trackSlug);
    renderPlaylists();
  }

  function setQueue(songs, trackSlug = "") {
    state.queue = songs;
    const explicitIndex = trackSlug ? songs.findIndex((song) => song.slug === trackSlug) : -1;
    state.currentIndex = explicitIndex >= 0 ? explicitIndex : 0;
    renderQueue();
    loadCurrentTrack(false);
  }

  function getCurrentSong() {
    return state.queue[state.currentIndex] || null;
  }

  function shiftTrack(direction) {
    if (!state.queue.length) {
      return;
    }

    state.currentIndex = (state.currentIndex + direction + state.queue.length) % state.queue.length;
    loadCurrentTrack(true);
  }

  function loadCurrentTrack(autoplay) {
    const song = getCurrentSong();
    if (!song) {
      elements.title.textContent = "No tracks available";
      elements.artist.textContent = "Queue is empty";
      elements.queue.innerHTML = `
        <article class="rounded-[1.5rem] border border-dashed border-white/10 p-6 text-center text-white/60">
          Add songs to the library or open a playlist to begin playback.
        </article>
      `;
      return;
    }

    elements.cover.src = song.cover_image_url;
    elements.cover.alt = `Cover for ${song.title}`;
    elements.title.textContent = song.title;
    elements.artist.textContent = song.artist?.name || "Naad-e-Maan";
    elements.artist.href = song.artist?.slug ? `artist.html?artist=${encodeURIComponent(song.artist.slug)}` : "artist.html";
    elements.copy.textContent = `${song.genre} • ${song.album?.title || "Single release"} • ${platform.formatTime(song.duration)} • ${formatStreams(song.streams_count)} streams`;
    elements.badge.textContent = `${state.queueLabel} • ${state.queueOwner}`;

    if (elements.audio.src !== song.audio_url) {
      elements.audio.src = song.audio_url;
      elements.audio.load();
    }

    elements.progress.max = String(song.duration || 0);
    syncProgress();
    renderQueue();

    if (autoplay) {
      elements.audio.play().catch(() => {
        // Ignore gesture failures.
      });
    } else {
      syncPlaybackState();
    }
  }

  function renderPlaylists() {
    elements.libraryAll.classList.toggle("border-neon-cyan/40", !state.activePlaylistSlug);
    elements.libraryAll.classList.toggle("bg-neon-cyan/10", !state.activePlaylistSlug);

    elements.playlists.innerHTML = state.playlists.length
      ? state.playlists
          .map(
            (playlist) => `
              <button
                type="button"
                data-player-playlist="${platform.escapeHtml(playlist.slug)}"
                class="glass-card flex w-full items-center gap-4 rounded-[1.5rem] border px-4 py-4 text-left transition ${
                  playlist.slug === state.activePlaylistSlug ? "border-neon-cyan/40 bg-neon-cyan/10" : "border-white/10"
                }"
              >
                <img src="${platform.escapeHtml(
                  playlist.cover_image_url || "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=320&q=80"
                )}" alt="${platform.escapeHtml(playlist.name)} cover" class="h-16 w-16 rounded-[1rem] object-cover" />
                <div class="min-w-0 flex-1">
                  <p class="truncate font-semibold">${platform.escapeHtml(playlist.name)}</p>
                  <p class="mt-1 truncate text-sm text-white/50">${playlist.songs_count} songs • ${platform.escapeHtml(playlist.owner?.name || "Naad-e-Maan")}</p>
                </div>
              </button>
            `
          )
          .join("")
      : `
          <article class="rounded-[1.5rem] border border-dashed border-white/10 p-6 text-center text-white/60">
            No playlists found yet.
          </article>
        `;
  }

  function renderQueue() {
    const rows = state.queue
      .map((song, index) => ({ song, index }))
      .filter(({ song }) => {
        if (!state.search) {
          return true;
        }

        return [song.title, song.genre, song.artist?.name, song.album?.title]
          .filter(Boolean)
          .some((value) => String(value).toLowerCase().includes(state.search));
      });

    elements.queueCount.textContent = `${rows.length} tracks`;
    elements.queue.innerHTML = rows.length
      ? rows
          .map(
            ({ song, index }) => `
              <button
                type="button"
                data-player-queue-index="${index}"
                class="glass-card flex w-full items-center gap-4 rounded-[1.5rem] border px-4 py-4 text-left transition ${
                  index === state.currentIndex ? "border-neon-cyan/40 bg-neon-cyan/10" : "border-white/10"
                }"
              >
                <img src="${platform.escapeHtml(song.cover_image_url)}" alt="${platform.escapeHtml(song.title)} cover" class="h-16 w-16 rounded-[1rem] object-cover" />
                <div class="min-w-0 flex-1">
                  <p class="truncate font-semibold">${platform.escapeHtml(song.title)}</p>
                  <p class="mt-1 truncate text-sm text-white/50">${platform.escapeHtml(song.artist?.name || "Naad-e-Maan")} • ${platform.escapeHtml(song.genre)}</p>
                </div>
                <div class="text-right text-xs uppercase tracking-[0.28em] text-white/40">
                  <p>${platform.formatTime(song.duration)}</p>
                  <p class="mt-2">${index === state.currentIndex ? "Now" : "Queue"}</p>
                </div>
              </button>
            `
          )
          .join("")
      : `
          <article class="rounded-[1.5rem] border border-dashed border-white/10 p-6 text-center text-white/60">
            No tracks match the current search.
          </article>
        `;
  }

  function syncProgress() {
    const duration = Number(elements.audio.duration || getCurrentSong()?.duration || 0);
    const currentTime = Number(elements.audio.currentTime || 0);

    elements.current.textContent = platform.formatTime(currentTime);
    elements.duration.textContent = platform.formatTime(duration);
    elements.progress.max = String(duration || 0);
    elements.progress.value = String(currentTime);
    platform.updateRangeFill(elements.progress);
  }

  function syncVolume() {
    elements.volume.value = String(Math.round(elements.audio.volume * 100));
    elements.volumeLabel.textContent = `${elements.volume.value}%`;
    platform.updateRangeFill(elements.volume);
  }

  function syncPlaybackState() {
    const isPlaying = !elements.audio.paused;
    elements.playIcon.innerHTML = isPlaying
      ? '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor" aria-hidden="true"><path d="M8 6h3v12H8zM13 6h3v12h-3z" /></svg>'
      : '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor" aria-hidden="true"><path d="M8 6.5v11l9-5.5-9-5.5Z" /></svg>';
    elements.play.setAttribute("aria-pressed", String(isPlaying));
    elements.play.setAttribute("aria-label", isPlaying ? "Pause music" : "Play music");
    elements.equalizer.classList.toggle("is-paused", !isPlaying);
    elements.status.textContent = state.queue.length ? (isPlaying ? "Playing" : "Paused") : "Ready";
  }
}

function formatStreams(value) {
  if (value >= 1000000) {
    return `${(value / 1000000).toFixed(value >= 10000000 ? 0 : 1)}M`;
  }

  if (value >= 1000) {
    return `${Math.round(value / 1000)}K`;
  }

  return String(value || 0);
}
