const NAAD_UPLOAD_DB = "naad-e-maan-db";
const NAAD_UPLOAD_STORE = "uploads";
const NAAD_CREATOR_SESSION = "naad-e-maan-creator-session";
const NAAD_RELEASE_PING = "naad-e-maan-release-ping";
const NAAD_AUTH_SESSION = "naad-e-maan-auth-session";
const NAAD_SELECTED_SONGS = "naad-e-maan-selected-songs";
const NAAD_FOLLOWED_ARTISTS = "naad-e-maan-followed-artists";
const DEFAULT_API_BASE = "http://127.0.0.1:8001/api";

const platformState = {
  apiBase: DEFAULT_API_BASE,
  auth: loadJson(NAAD_AUTH_SESSION, null),
  selectedSongIds: loadJson(NAAD_SELECTED_SONGS, []),
  followedArtists: loadJson(NAAD_FOLLOWED_ARTISTS, []),
  songCache: new Map(),
  refreshHomeCatalog: null,
  preview: {
    mount: null,
    audio: null,
    song: null,
    recorded: new Set(),
  },
};

window.NaadPlatform = {
  state: platformState,
  apiRequest,
  loadSongs,
  loadArtists,
  loadArtistProfile,
  loadPlaylists,
  loadPlaylist,
  refreshSession,
  setSession,
  clearSession,
  formatTime,
  updateRangeFill,
  playPreviewSong,
  pausePreview,
  recordSongStream,
  escapeHtml,
  getSongById,
};

document.addEventListener("DOMContentLoaded", () => {
  platformState.apiBase = document.body.dataset.apiBase || DEFAULT_API_BASE;
  renderChrome();
  setupNavigation();
  setupCounters();
  setupProgressBars();
  setupVolumeDisplay();
  setupDashboardBars();
  setupMiniPlayer();
  refreshSession(true)
    .catch(() => null)
    .finally(async () => {
      setupCreatorPortal();
      await Promise.allSettled([setupHomePage(), setupArtistPage(), setupGenrePage()]);
    });

  window.addEventListener("storage", (event) => {
    if (event.key === NAAD_RELEASE_PING && typeof platformState.refreshHomeCatalog === "function") {
      platformState.refreshHomeCatalog().catch(() => {
        // Ignore cross-tab refresh failures.
      });
    }
  });
});

function renderChrome() {
  const headerMount = document.querySelector("[data-site-header]");
  if (headerMount) {
    headerMount.innerHTML = buildHeader();
  }

  const footerMount = document.querySelector("[data-site-footer]");
  if (footerMount) {
    footerMount.innerHTML = buildFooter();
  }
}

function buildHeader() {
  return `
    <header class="sticky top-0 z-50 border-b border-neon-cyan/25 bg-[rgba(10,15,11,0.9)] backdrop-blur-2xl shadow-[0_14px_34px_rgba(6,10,7,0.42)]">
      <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
        <a href="index.html" class="flex items-center gap-3">
          <span class="glass-card neon-border flex h-11 w-11 items-center justify-center rounded-2xl">
            ${brandIcon("h-6 w-6 text-neon-cyan")}
          </span>
          <span>
            <span class="brand-title block text-lg font-semibold text-neon-cyan">Naad-e-Maan</span>
            <span class="block text-[0.65rem] uppercase tracking-[0.35em] text-neon-cyan/70">Sound Resonance</span>
          </span>
        </a>

        <nav class="hidden items-center gap-2 md:flex">
          ${navLink("index.html", "home", "Home")}
          ${navLink("artist.html", "artists", "Artists")}
          ${navLink("genres.html", "genres", "Genres")}
          <a href="player.html" class="pill-nav rounded-full border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-2 text-sm text-neon-cyan/90">Videos</a>
          ${navLink("creators.html", "creators", "Creators")}
          <a href="#contact" class="pill-nav rounded-full border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-2 text-sm text-neon-cyan/90">Contact</a>
        </nav>

        <button
          type="button"
          data-menu-toggle
          aria-expanded="false"
          aria-label="Toggle navigation"
          class="glass-card flex h-11 w-11 items-center justify-center rounded-2xl border border-neon-cyan/25 bg-neon-cyan/5 text-neon-cyan md:hidden"
        >
          <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8">
            <path d="M4 7h16M4 12h16M4 17h16" />
          </svg>
        </button>
      </div>

      <div data-mobile-menu class="mobile-menu border-t border-neon-cyan/20 md:hidden">
        <nav class="mx-auto flex max-w-7xl flex-col gap-2 px-6 py-4">
          <a href="index.html" class="rounded-2xl border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-3 text-sm text-neon-cyan/90">Home</a>
          <a href="artist.html" class="rounded-2xl border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-3 text-sm text-neon-cyan/90">Artists</a>
          <a href="genres.html" class="rounded-2xl border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-3 text-sm text-neon-cyan/90">Genres</a>
          <a href="player.html" class="rounded-2xl border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-3 text-sm text-neon-cyan/90">Videos</a>
          <a href="creators.html" class="rounded-2xl border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-3 text-sm text-neon-cyan/90">Creators</a>
          <a href="#contact" class="rounded-2xl border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-3 text-sm text-neon-cyan/90">Contact</a>
        </nav>
      </div>
    </header>
  `;
}

function buildFooter() {
  return `
    <footer id="contact" class="border-t border-white/10 bg-slate-950/50">
      <div class="mx-auto max-w-7xl px-6 py-10 lg:px-8">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
          <a href="index.html" class="flex items-center gap-3">
            <span class="glass-card neon-border flex h-11 w-11 items-center justify-center rounded-2xl">
              ${brandIcon("h-6 w-6 text-neon-cyan")}
            </span>
            <div>
              <p class="brand-title text-lg font-semibold">Naad-e-Maan</p>
              <p class="text-sm text-white/50">The Sound of the Soul</p>
            </div>
          </a>

          <div class="flex items-center gap-3">
            ${socialLink(
              `<svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5" /><circle cx="12" cy="12" r="3.5" /><circle cx="17.4" cy="6.6" r="1" fill="currentColor" stroke="none" /></svg>`
            )}
            ${socialLink(
              `<svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M18.7 3H21l-5.03 5.75L21.88 21h-4.62l-3.62-4.76L9.47 21H7.12l5.38-6.16L2.12 3h4.75l3.28 4.35L13.96 3h.01Z" /></svg>`
            )}
            ${socialLink(
              `<svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M21 8.6a2.8 2.8 0 0 0-2-2c-1.8-.5-7-.5-7-.5s-5.2 0-7 .5a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2.5 12 29 29 0 0 0 3 15.4a2.8 2.8 0 0 0 2 2c1.8.5 7 .5 7 .5s5.2 0 7-.5a2.8 2.8 0 0 0 2-2 29 29 0 0 0 .5-3.4 29 29 0 0 0-.5-3.4ZM10 15.5v-7l6 3.5-6 3.5Z" /></svg>`
            )}
            ${socialLink(
              `<svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true"><path d="M3 13.2v2.1c0 1.2 1 2.1 2.1 2.1h.7v2.1l3-2.1h1.5c2.2 0 3.7-1.2 3.7-3.1V8.7c0-1.9-1.5-3.1-3.7-3.1H5.1C4 5.6 3 6.6 3 7.7v2.1" /><path d="M15 9.8h4.2c1.1 0 1.8.8 1.8 1.9v4.4c0 1-.7 1.8-1.8 1.8H18l-3 2.1v-2.1h-1" /></svg>`
            )}
          </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-white/10 pt-6 text-sm text-white/50">
          <div class="flex flex-wrap items-center gap-5">
            <a href="#" class="transition hover:text-white">Terms</a>
            <a href="#" class="transition hover:text-white">Privacy</a>
          </div>
          <p>Copyright (c) 2026 Naad-e-Maan. All rights reserved.</p>
        </div>
      </div>
    </footer>
  `;
}

function navLink(href, page, label) {
  return `<a href="${href}" data-nav-link="${page}" class="pill-nav rounded-full border border-neon-cyan/25 bg-neon-cyan/5 px-4 py-2 text-sm text-neon-cyan/90">${label}</a>`;
}

function socialLink(icon) {
  return `<a href="#" class="glass-card flex h-11 w-11 items-center justify-center rounded-full border border-white/10 text-white/75 transition hover:text-white">${icon}</a>`;
}

function brandIcon(className) {
  return `
    <svg viewBox="0 0 48 48" class="${className}" fill="none" aria-hidden="true">
      <path d="M8 26h4l3-10 5 18 4-11 4 7h4l3-12 5 8h4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
    </svg>
  `;
}

function setupNavigation() {
  const currentPage = document.body.dataset.page;
  if (currentPage) {
    document.querySelectorAll("[data-nav-link]").forEach((link) => {
      if (link.dataset.navLink === currentPage) {
        link.classList.add("is-active");
      }
    });
  }

  const menuToggle = document.querySelector("[data-menu-toggle]");
  const mobileMenu = document.querySelector("[data-mobile-menu]");
  if (!menuToggle || !mobileMenu) {
    return;
  }

  menuToggle.addEventListener("click", () => {
    const expanded = menuToggle.getAttribute("aria-expanded") === "true";
    menuToggle.setAttribute("aria-expanded", String(!expanded));
    mobileMenu.classList.toggle("is-open", !expanded);
  });
}

function setupCounters(scope = document) {
  const counters = Array.from(scope.querySelectorAll("[data-counter]")).filter((counter) => !counter.dataset.counterBound);
  if (!counters.length) {
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        const el = entry.target;
        const target = Number(el.dataset.counter || 0);
        const suffix = el.dataset.suffix || "";
        const duration = 1400;
        const start = performance.now();

        const tick = (now) => {
          const progress = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          const value = Math.round(target * eased);
          el.textContent = `${formatCounter(value)}${suffix}`;
          if (progress < 1) {
            requestAnimationFrame(tick);
          }
        };

        requestAnimationFrame(tick);
        observer.unobserve(el);
      });
    },
    { threshold: 0.35 }
  );

  counters.forEach((counter) => {
    counter.dataset.counterBound = "true";
    observer.observe(counter);
  });
}

function formatCounter(value) {
  if (value >= 1000000) {
    const short = value / 1000000;
    return Number.isInteger(short) ? `${short}M` : `${short.toFixed(1)}M`;
  }

  if (value >= 1000) {
    return value.toLocaleString();
  }

  return String(value);
}

function setupProgressBars(scope = document) {
  scope.querySelectorAll("[data-progress]").forEach((bar) => {
    const fill = bar.querySelector(".progress-fill");
    if (!fill) {
      return;
    }

    const width = `${bar.dataset.progress || 0}%`;
    requestAnimationFrame(() => {
      fill.style.width = width;
    });
  });
}

function setupVolumeDisplay(scope = document) {
  scope.querySelectorAll("[data-volume-control]").forEach((wrapper) => {
    if (wrapper.dataset.volumeBound === "true") {
      return;
    }

    const slider = wrapper.querySelector("input[type='range']");
    const label = wrapper.querySelector("[data-volume-label]");
    if (!slider || !label) {
      return;
    }

    const updateLabel = () => {
      label.textContent = `${slider.value}%`;
      updateRangeFill(slider);
    };

    slider.addEventListener("input", updateLabel);
    wrapper.dataset.volumeBound = "true";
    updateLabel();
  });
}

function setupDashboardBars() {
  document.querySelectorAll("[data-bar-height]").forEach((bar) => {
    const height = bar.dataset.barHeight || "0%";
    requestAnimationFrame(() => {
      bar.style.height = height;
    });
  });
}

function updateRangeFill(slider) {
  const min = Number(slider.min || 0);
  const max = Number(slider.max || 100);
  const value = Number(slider.value || min);
  const ratio = max > min ? ((value - min) / (max - min)) * 100 : 0;
  slider.style.background = `linear-gradient(90deg, rgba(255,79,216,0.95) 0%, rgba(59,242,255,0.95) ${ratio}%, rgba(255,255,255,0.12) ${ratio}%, rgba(255,255,255,0.12) 100%)`;
}

function formatTime(totalSeconds) {
  const safeSeconds = Math.max(0, Math.floor(totalSeconds));
  const minutes = Math.floor(safeSeconds / 60);
  const seconds = safeSeconds % 60;
  return `${minutes}:${String(seconds).padStart(2, "0")}`;
}

async function apiRequest(path, options = {}) {
  const { method = "GET", body, headers = {}, noAuth = false, token } = options;
  const base = platformState.apiBase || DEFAULT_API_BASE;
  const url = path.startsWith("http") ? path : `${base}${path.startsWith("/") ? path : `/${path}`}`;
  const requestHeaders = { Accept: "application/json", ...headers };
  const authToken = token ?? platformState.auth?.token;

  if (!noAuth && authToken) {
    requestHeaders.Authorization = `Bearer ${authToken}`;
  }

  const requestOptions = { method, headers: requestHeaders };
  if (body !== undefined) {
    requestHeaders["Content-Type"] = "application/json";
    requestOptions.body = JSON.stringify(body);
  }

  const response = await fetch(url, requestOptions);
  const contentType = response.headers.get("content-type") || "";
  const payload = contentType.includes("application/json") ? await response.json().catch(() => ({})) : await response.text();

  if (!response.ok) {
    const message = payload?.message || firstValidationMessage(payload?.errors) || `Request failed with status ${response.status}.`;
    const error = new Error(message);
    error.status = response.status;
    error.payload = payload;
    throw error;
  }

  return payload;
}

async function loadSongs(filters = {}) {
  const params = new URLSearchParams();
  if (filters.search) {
    params.set("search", filters.search);
  }
  if (filters.genre) {
    params.set("genre", filters.genre);
  }
  if (filters.artist) {
    params.set("artist", filters.artist);
  }

  const query = params.toString();
  const payload = await apiRequest(`/songs${query ? `?${query}` : ""}`);
  rememberSongs(payload.data || []);
  return payload;
}

function loadArtists() {
  return apiRequest("/artists");
}

function loadArtistProfile(slug) {
  return apiRequest(`/artists/${encodeURIComponent(slug)}`);
}

function loadPlaylists() {
  return apiRequest("/playlists");
}

async function loadPlaylist(slug) {
  const payload = await apiRequest(`/playlists/${encodeURIComponent(slug)}`);
  rememberSongs(payload?.data?.songs || []);
  return payload;
}

async function refreshSession(silent = false) {
  if (!platformState.auth?.token) {
    return null;
  }

  try {
    const payload = await apiRequest("/auth/me");
    platformState.auth = { token: platformState.auth.token, user: payload.user };
    localStorage.setItem(NAAD_AUTH_SESSION, JSON.stringify(platformState.auth));
    return payload.user;
  } catch (error) {
    clearSession();
    if (!silent) {
      throw error;
    }
    return null;
  }
}

function setSession(token, user) {
  platformState.auth = { token, user };
  localStorage.setItem(NAAD_AUTH_SESSION, JSON.stringify(platformState.auth));
}

function clearSession() {
  platformState.auth = null;
  localStorage.removeItem(NAAD_AUTH_SESSION);
}

function rememberSongs(songs) {
  songs.forEach((song) => {
    platformState.songCache.set(`id:${song.id}`, song);
    platformState.songCache.set(`slug:${song.slug}`, song);
  });
}

function getSongById(id) {
  return platformState.songCache.get(`id:${id}`) || null;
}

function getSongBySlug(slug) {
  return platformState.songCache.get(`slug:${slug}`) || null;
}

function setupMiniPlayer() {
  const mount = document.querySelector("[data-mini-player]");
  if (!mount) {
    return;
  }

  mount.innerHTML = `
    <div class="pointer-events-auto glass-card rounded-[1.75rem] border border-white/10 p-4 shadow-[0_0_45px_rgba(59,242,255,0.12)]">
      <div class="grid gap-4 md:grid-cols-[auto_1fr_auto] md:items-center">
        <div class="flex items-center gap-4">
          <img data-mini-cover src="https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=400&q=80" alt="Now playing cover" class="h-16 w-16 rounded-[1.25rem] object-cover" />
          <div>
            <p data-mini-title class="font-semibold">Ready to play</p>
            <a data-mini-artist href="artist.html" class="mt-1 block text-sm text-white/55 transition hover:text-neon-cyan">Naad-e-Maan</a>
          </div>
        </div>
        <div>
          <div class="flex items-center justify-between text-xs text-white/50">
            <span data-mini-current>0:00</span>
            <span data-mini-duration>0:00</span>
          </div>
          <input data-mini-progress type="range" min="0" max="100" value="0" class="range-slider mt-3" />
        </div>
        <div class="flex items-center gap-3">
          <button type="button" data-mini-play class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-950" aria-label="Play preview">${iconPlay()}</button>
          <button type="button" data-mini-close class="glass-card flex h-12 w-12 items-center justify-center rounded-full border border-white/10 text-white/75" aria-label="Close preview">
            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8" aria-hidden="true">
              <path d="M6 6l12 12M18 6L6 18" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  `;

  platformState.preview.mount = mount;
  platformState.preview.audio = new Audio();

  const audio = platformState.preview.audio;
  const progress = mount.querySelector("[data-mini-progress]");
  const playButton = mount.querySelector("[data-mini-play]");
  const closeButton = mount.querySelector("[data-mini-close]");

  audio.addEventListener("timeupdate", syncMiniPlayer);
  audio.addEventListener("loadedmetadata", syncMiniPlayer);
  audio.addEventListener("pause", syncMiniPlayer);
  audio.addEventListener("ended", syncMiniPlayer);
  audio.addEventListener("play", () => {
    syncMiniPlayer();
    if (platformState.preview.song) {
      recordSongStream(platformState.preview.song).catch(() => {
        // Ignore preview stream failures.
      });
    }
  });

  progress.addEventListener("input", () => {
    audio.currentTime = Number(progress.value);
    syncMiniPlayer();
  });

  playButton.addEventListener("click", () => {
    if (!platformState.preview.song) {
      return;
    }

    if (audio.paused) {
      audio.play().catch(() => {
        // Ignore gesture failures.
      });
    } else {
      audio.pause();
    }
  });

  closeButton.addEventListener("click", () => {
    pausePreview();
    audio.removeAttribute("src");
    platformState.preview.song = null;
    syncMiniPlayer();
  });
}

function syncMiniPlayer() {
  const mount = platformState.preview.mount;
  const audio = platformState.preview.audio;
  if (!mount || !audio) {
    return;
  }

  const song = platformState.preview.song;
  mount.classList.toggle("hidden", !song);

  if (!song) {
    return;
  }

  mount.querySelector("[data-mini-cover]").src = song.cover_image_url;
  mount.querySelector("[data-mini-cover]").alt = `Cover for ${song.title}`;
  mount.querySelector("[data-mini-title]").textContent = song.title;
  mount.querySelector("[data-mini-artist]").textContent = song.artist?.name || "Naad-e-Maan";
  mount.querySelector("[data-mini-artist]").href = song.artist?.slug ? `artist.html?artist=${encodeURIComponent(song.artist.slug)}` : "artist.html";
  mount.querySelector("[data-mini-current]").textContent = formatTime(audio.currentTime || 0);
  mount.querySelector("[data-mini-duration]").textContent = formatTime(audio.duration || song.duration || 0);

  const progress = mount.querySelector("[data-mini-progress]");
  progress.max = String(audio.duration || song.duration || 0);
  progress.value = String(audio.currentTime || 0);
  updateRangeFill(progress);

  mount.querySelector("[data-mini-play]").innerHTML = audio.paused ? iconPlay() : iconPause();
}

function playPreviewSong(song) {
  if (!platformState.preview.audio || !song) {
    return;
  }

  const audio = platformState.preview.audio;
  const isSameSong = platformState.preview.song?.slug === song.slug;
  platformState.preview.song = song;

  if (!isSameSong) {
    audio.src = song.audio_url;
    audio.currentTime = 0;
  }

  syncMiniPlayer();
  audio.play().catch(() => {
    // Ignore gesture failures.
  });
}

function pausePreview() {
  if (platformState.preview.audio) {
    platformState.preview.audio.pause();
  }
}

async function recordSongStream(song) {
  if (!song?.slug || platformState.preview.recorded.has(song.slug)) {
    return;
  }

  platformState.preview.recorded.add(song.slug);
  try {
    await apiRequest(`/songs/${encodeURIComponent(song.slug)}/stream`, { method: "POST", body: {} });
  } catch (error) {
    platformState.preview.recorded.delete(song.slug);
    throw error;
  }
}

async function setupHomePage() {
  const root = document.querySelector("[data-home-app]");
  if (!root) {
    return;
  }

  const elements = {
    featuredTitle: root.querySelector("[data-home-featured-title]"),
    featuredArtist: root.querySelector("[data-home-featured-artist]"),
    featuredCover: root.querySelector("[data-home-featured-cover]"),
    featuredGenre: root.querySelector("[data-home-featured-genre]"),
    featuredCopy: root.querySelector("[data-home-featured-copy]"),
    featuredPlay: root.querySelector("[data-home-featured-play]"),
    featuredLink: root.querySelector("[data-home-featured-link]"),
    search: root.querySelector("[data-song-search]"),
    genre: root.querySelector("[data-song-genre]"),
    count: root.querySelector("[data-song-results-count]"),
    grid: root.querySelector("[data-latest-releases-grid]"),
    artists: root.querySelector("[data-home-artists]"),
    playlists: root.querySelector("[data-playlist-grid]"),
    selectedSongs: root.querySelector("[data-selected-songs]"),
    selectedEmpty: root.querySelector("[data-selected-empty]"),
    playlistForm: root.querySelector("[data-playlist-form]"),
    authGuest: root.querySelector("[data-auth-guest]"),
    authUser: root.querySelector("[data-auth-user]"),
    authMessage: root.querySelector("[data-auth-message]"),
    loginForm: root.querySelector("[data-login-form]"),
    registerForm: root.querySelector("[data-register-form]"),
    authAvatar: root.querySelector("[data-auth-avatar]"),
    authName: root.querySelector("[data-auth-name]"),
    authEmail: root.querySelector("[data-auth-email]"),
    authArtists: root.querySelector("[data-auth-artists]"),
    authLogout: root.querySelector("[data-auth-logout]"),
  };

  const playlistMessage = document.createElement("div");
  playlistMessage.className = "hidden mt-6 rounded-[1.5rem] px-4 py-3 text-sm";
  elements.playlistForm.before(playlistMessage);

  let currentSongs = [];

  const refreshCatalog = async () => {
    try {
      elements.grid.innerHTML = buildLoadingGrid();
      elements.count.textContent = "Loading catalog...";

      const filters = {
        search: elements.search.value.trim(),
        genre: elements.genre.value.trim(),
      };

      const [songsPayload, artistsPayload, playlistsPayload] = await Promise.all([loadSongs(filters), loadArtists(), loadPlaylists()]);
      currentSongs = songsPayload.data || [];

      renderGenreOptions(elements.genre, songsPayload.meta?.genres || [], filters.genre);
      renderHomeSongs(elements.grid, currentSongs);
      await renderLatestReleases();
      renderFeaturedSong(elements, currentSongs);
      renderArtistStrip(elements.artists, artistsPayload.data || []);
      renderPlaylistGrid(elements.playlists, playlistsPayload.data || []);
      renderSelectedSongs(elements.selectedSongs, elements.selectedEmpty);
      elements.count.textContent = `${currentSongs.length} songs loaded from the API`;
    } catch (error) {
      elements.count.textContent = error.message;
      elements.grid.innerHTML = emptyStateCard("Catalog offline", "The Laravel API is not reachable yet. Start the backend server and reload.");
    }
  };

  platformState.refreshHomeCatalog = refreshCatalog;

  elements.grid.addEventListener("click", (event) => {
    const playButton = event.target.closest("[data-song-play]");
    if (playButton) {
      const song = getSongById(Number(playButton.dataset.songId));
      if (song) {
        playPreviewSong(song);
      }
      return;
    }

    const selectButton = event.target.closest("[data-song-select]");
    if (selectButton) {
      const song = getSongById(Number(selectButton.dataset.songId));
      if (song) {
        toggleSelectedSong(song.id);
        renderHomeSongs(elements.grid, currentSongs);
        renderLatestReleases().catch(() => {
          // Ignore local upload refresh failures.
        });
        renderSelectedSongs(elements.selectedSongs, elements.selectedEmpty);
      }
    }
  });

  elements.featuredPlay.addEventListener("click", () => {
    const featuredSong = currentSongs.find((song) => song.is_featured) || currentSongs[0];
    if (featuredSong) {
      playPreviewSong(featuredSong);
    }
  });

  elements.search.addEventListener(
    "input",
    debounce(() => {
      refreshCatalog().catch(() => null);
    }, 300)
  );

  elements.genre.addEventListener("change", () => {
    refreshCatalog().catch(() => null);
  });

  elements.loginForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    const formData = new FormData(elements.loginForm);

    try {
      const payload = await apiRequest("/auth/login", {
        method: "POST",
        body: {
          email: String(formData.get("email") || "").trim(),
          password: String(formData.get("password") || "").trim(),
        },
        noAuth: true,
      });

      setSession(payload.token, payload.user);
      renderAuthState(elements);
      showMessage(elements.authMessage, "Logged in successfully. Your playlists are now available.", "success");
      await refreshCatalog();
    } catch (error) {
      showMessage(elements.authMessage, error.message, "error");
    }
  });

  elements.registerForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    const formData = new FormData(elements.registerForm);

    try {
      const payload = await apiRequest("/auth/register", {
        method: "POST",
        body: {
          name: String(formData.get("name") || "").trim(),
          email: String(formData.get("email") || "").trim(),
          password: String(formData.get("password") || "").trim(),
        },
        noAuth: true,
      });

      setSession(payload.token, payload.user);
      renderAuthState(elements);
      elements.registerForm.reset();
      showMessage(elements.authMessage, "Account created. You can start building playlists immediately.", "success");
      await refreshCatalog();
    } catch (error) {
      showMessage(elements.authMessage, error.message, "error");
    }
  });

  elements.authLogout.addEventListener("click", async () => {
    try {
      await apiRequest("/auth/logout", { method: "POST", body: {} });
    } catch (error) {
      // Ignore logout failures.
    }

    clearSession();
    renderAuthState(elements);
    showMessage(elements.authMessage, "You have been logged out.", "success");
    await refreshCatalog();
  });

  elements.playlistForm.addEventListener("submit", async (event) => {
    event.preventDefault();

    if (!platformState.auth?.token) {
      showMessage(playlistMessage, "Log in before creating a playlist.", "error");
      return;
    }

    if (!platformState.selectedSongIds.length) {
      showMessage(playlistMessage, "Select at least one song from Latest Releases first.", "error");
      return;
    }

    const formData = new FormData(elements.playlistForm);

    try {
      await apiRequest("/playlists", {
        method: "POST",
        body: {
          name: String(formData.get("name") || "").trim(),
          description: String(formData.get("description") || "").trim(),
          song_ids: [...platformState.selectedSongIds],
          is_public: formData.get("is_public") === "on",
        },
      });

      elements.playlistForm.reset();
      platformState.selectedSongIds = [];
      localStorage.setItem(NAAD_SELECTED_SONGS, JSON.stringify(platformState.selectedSongIds));
      renderSelectedSongs(elements.selectedSongs, elements.selectedEmpty);
      renderHomeSongs(elements.grid, currentSongs);
      await renderLatestReleases();
      showMessage(playlistMessage, "Playlist created successfully.", "success");

      const playlistsPayload = await loadPlaylists();
      renderPlaylistGrid(elements.playlists, playlistsPayload.data || []);
    } catch (error) {
      showMessage(playlistMessage, error.message, "error");
    }
  });

  renderAuthState(elements);
  await refreshCatalog();
}

function renderHomeSongs(container, songs) {
  if (!songs.length) {
    container.innerHTML = emptyStateCard("No songs found", "Try clearing the search or switching to a different genre.");
    return;
  }

  container.innerHTML = songs.map((song) => buildSongCard(song, { showSelect: true })).join("");
}

function renderFeaturedSong(elements, songs) {
  const song = songs.find((entry) => entry.is_featured) || songs[0];
  if (!song) {
    return;
  }

  elements.featuredTitle.textContent = song.title;
  elements.featuredArtist.textContent = song.artist?.name || "Naad-e-Maan";
  elements.featuredCover.src = song.cover_image_url;
  elements.featuredCover.alt = `Album cover for ${song.title}`;
  elements.featuredGenre.textContent = song.genre;
  elements.featuredCopy.textContent = `${song.artist?.name || "This artist"} is live in the catalog with ${formatCounter(song.streams_count)} streams and direct MP3 playback.`;
  elements.featuredLink.href = `player.html?track=${encodeURIComponent(song.slug)}`;
}

function renderArtistStrip(container, artists) {
  if (!artists.length) {
    container.innerHTML = emptyStateCard("No artists yet", "Seed the catalog to populate artist profiles.");
    return;
  }

  container.innerHTML = artists
    .map(
      (artist) => `
        <a href="artist.html?artist=${encodeURIComponent(artist.slug)}" class="artist-card glass-card min-w-[190px] rounded-[1.75rem] border border-white/10 p-5">
          <div class="artist-ring mx-auto w-fit">
            <img src="${escapeHtml(artist.image_url)}" alt="${escapeHtml(artist.name)} avatar" class="h-24 w-24 object-cover" />
          </div>
          <h3 class="mt-4 text-center font-semibold">${escapeHtml(artist.name)}</h3>
          <p class="mt-1 text-center text-sm text-white/50">${escapeHtml(artist.genre)}</p>
          <p class="mt-3 text-center text-xs uppercase tracking-[0.28em] text-neon-cyan/75">${formatCounter(artist.monthly_listeners)} listeners</p>
        </a>
      `
    )
    .join("");
}

function renderPlaylistGrid(container, playlists) {
  if (!playlists.length) {
    container.innerHTML = emptyStateCard("No playlists yet", "Create a playlist from the selected songs panel.");
    return;
  }

  container.innerHTML = playlists
    .map(
      (playlist) => `
        <article class="glass-card overflow-hidden rounded-[1.75rem] border border-white/10">
          <div class="relative">
            <img src="${escapeHtml(playlist.cover_image_url || "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=80")}" alt="${escapeHtml(playlist.name)} cover" class="h-56 w-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>
            <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between gap-4">
              <div>
                <p class="text-xs uppercase tracking-[0.28em] text-white/45">${playlist.is_public ? "Public" : "Private"}</p>
                <h3 class="mt-2 text-xl font-semibold">${escapeHtml(playlist.name)}</h3>
              </div>
              <a href="player.html?playlist=${encodeURIComponent(playlist.slug)}" class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-950">${iconPlay("ml-0.5 h-5 w-5")}</a>
            </div>
          </div>
          <div class="p-5">
            <p class="text-sm leading-7 text-white/55">${escapeHtml(playlist.description || "Curated for immersive Naad-e-Maan playback.")}</p>
            <div class="mt-4 flex items-center justify-between text-xs uppercase tracking-[0.28em] text-white/40">
              <span>${playlist.songs_count} songs</span>
              <span>${escapeHtml(playlist.owner?.name || "Naad-e-Maan")}</span>
            </div>
          </div>
        </article>
      `
    )
    .join("");
}

function renderSelectedSongs(container, emptyState) {
  const songs = platformState.selectedSongIds.map((id) => getSongById(id)).filter(Boolean);
  emptyState.classList.toggle("hidden", songs.length > 0);

  container.innerHTML = songs
    .map(
      (song) => `
        <article class="flex items-center gap-3 rounded-[1.25rem] border border-white/10 bg-slate-950/45 p-3">
          <img src="${escapeHtml(song.cover_image_url)}" alt="${escapeHtml(song.title)} cover" class="h-14 w-14 rounded-xl object-cover" />
          <div class="min-w-0 flex-1">
            <p class="truncate font-medium">${escapeHtml(song.title)}</p>
            <p class="truncate text-sm text-white/50">${escapeHtml(song.artist?.name || "Naad-e-Maan")}</p>
          </div>
          <button type="button" data-selected-remove="${song.id}" class="text-sm text-white/45 transition hover:text-neon-pink">Remove</button>
        </article>
      `
    )
    .join("");

  container.querySelectorAll("[data-selected-remove]").forEach((button) => {
    button.addEventListener("click", () => {
      const songId = Number(button.dataset.selectedRemove);
      toggleSelectedSong(songId);
      document.querySelectorAll(`[data-song-select][data-song-id="${songId}"]`).forEach((control) => {
        control.innerHTML = iconPlus("h-5 w-5");
      });
      renderSelectedSongs(container, emptyState);
    });
  });
}

function renderGenreOptions(select, genres, selectedValue) {
  const currentValues = Array.from(select.options).map((option) => option.value);
  const nextValues = ["", ...genres];
  if (JSON.stringify(currentValues) === JSON.stringify(nextValues)) {
    select.value = selectedValue || "";
    return;
  }

  select.innerHTML = `<option value="" class="bg-slate-950">All genres</option>${genres
    .map((genre) => `<option value="${escapeHtml(genre)}" class="bg-slate-950">${escapeHtml(genre)}</option>`)
    .join("")}`;
  select.value = selectedValue || "";
}

function renderAuthState(elements) {
  const user = platformState.auth?.user || null;
  const isLoggedIn = Boolean(user);

  elements.authGuest.classList.toggle("hidden", isLoggedIn);
  elements.authUser.classList.toggle("hidden", !isLoggedIn);
  if (!isLoggedIn) {
    return;
  }

  elements.authAvatar.src = user.avatar_url || "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=300&q=80";
  elements.authName.textContent = user.name;
  elements.authEmail.textContent = user.email;
  elements.authArtists.innerHTML = (user.artists || [])
    .map(
      (artist) =>
        `<a href="artist.html?artist=${encodeURIComponent(artist.slug)}" class="rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-sm text-neon-cyan">${escapeHtml(artist.name)}</a>`
    )
    .join("");
}

function toggleSelectedSong(songId) {
  if (platformState.selectedSongIds.includes(songId)) {
    platformState.selectedSongIds = platformState.selectedSongIds.filter((id) => id !== songId);
  } else {
    platformState.selectedSongIds = [...platformState.selectedSongIds, songId];
  }

  localStorage.setItem(NAAD_SELECTED_SONGS, JSON.stringify(platformState.selectedSongIds));
}

async function setupArtistPage() {
  const root = document.querySelector("[data-artist-app]");
  if (!root) {
    return;
  }

  const params = new URLSearchParams(window.location.search);
  let artistSlug = params.get("artist");

  try {
    const artistsPayload = await loadArtists();
    const artists = artistsPayload.data || [];
    if (!artistSlug) {
      artistSlug = artists[0]?.slug || null;
    }

    if (!artistSlug) {
      root.innerHTML = emptyStateCard("No artist data", "Seed at least one artist in the API to populate this page.");
      return;
    }

    const profilePayload = await loadArtistProfile(artistSlug);
    const artist = profilePayload.data;
    rememberSongs(artist.songs || []);
    renderArtistProfile(root, artist, artists.filter((entry) => entry.slug !== artist.slug));

    if (!params.get("artist")) {
      const url = new URL(window.location.href);
      url.searchParams.set("artist", artistSlug);
      window.history.replaceState({}, "", url);
    }
  } catch (error) {
    const releases = root.querySelector("[data-artist-releases]");
    if (releases) {
      releases.innerHTML = emptyStateCard("Artist profile unavailable", error.message);
    }
  }
}

function renderArtistProfile(root, artist, collaborations) {
  const image = root.querySelector("[data-artist-image]");
  const name = root.querySelector("[data-artist-name]");
  const bio = root.querySelector("[data-artist-bio]");
  const tags = root.querySelector("[data-artist-tags]");
  const playButton = root.querySelector("[data-artist-play]");
  const followButton = root.querySelector("[data-artist-follow]");
  const summary = root.querySelector("[data-artist-summary]");
  const playerLink = root.querySelector("[data-artist-player-link]");
  const featuredCover = root.querySelector("[data-featured-track-cover]");
  const featuredTitle = root.querySelector("[data-featured-track-title]");
  const featuredCopy = root.querySelector("[data-featured-track-copy]");
  const featuredDuration = root.querySelector("[data-featured-track-duration]");
  const featuredPlay = root.querySelector("[data-featured-track-play]");
  const featuredOpen = root.querySelector("[data-featured-track-open]");
  const releases = root.querySelector("[data-artist-releases]");
  const collaborationStrip = root.querySelector("[data-artist-collaborations]");
  const stats = root.querySelector("[data-artist-stats]");
  const songs = artist.songs || [];
  const featuredSong = songs.find((song) => song.is_featured) || songs[0];
  const streamTotal = songs.reduce((total, song) => total + Number(song.streams_count || 0), 0);
  const tagValues = Array.from(new Set([artist.genre, ...songs.map((song) => song.genre)])).filter(Boolean).slice(0, 4);

  image.src = artist.image_url;
  image.alt = `Portrait of ${artist.name}`;
  name.textContent = artist.name;
  bio.textContent = artist.bio;
  tags.innerHTML = tagValues
    .map((tag, index) => {
      const palette =
        index % 3 === 0
          ? "border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan"
          : index % 3 === 1
          ? "border-neon-pink/20 bg-neon-pink/10 text-neon-pink"
          : "border-white/10 bg-white/5 text-white/70";
      return `<span class="rounded-full border px-4 py-2 text-sm ${palette}">${escapeHtml(tag)}</span>`;
    })
    .join("");

  summary.innerHTML = `
    <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5">
      <p class="text-sm text-white/50">Top Release</p>
      <p class="mt-2 font-display text-2xl font-semibold">${escapeHtml(featuredSong?.title || "No songs yet")}</p>
    </article>
    <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5">
      <p class="text-sm text-white/50">Monthly Listeners</p>
      <p class="mt-2 font-display text-2xl font-semibold">${formatCounter(artist.monthly_listeners)}</p>
    </article>
    <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5">
      <p class="text-sm text-white/50">Followers</p>
      <p class="mt-2 font-display text-2xl font-semibold">${formatCounter(artist.followers)}</p>
    </article>
  `;

  playerLink.href = featuredSong ? `player.html?artist=${encodeURIComponent(artist.slug)}&track=${encodeURIComponent(featuredSong.slug)}` : `player.html?artist=${encodeURIComponent(artist.slug)}`;

  if (featuredSong) {
    featuredCover.src = featuredSong.cover_image_url;
    featuredCover.alt = `Cover for ${featuredSong.title}`;
    featuredTitle.textContent = featuredSong.title;
    featuredCopy.textContent = `${artist.name} • ${featuredSong.genre.toLowerCase()} • ${featuredSong.album?.title || "Single release"}`;
    featuredDuration.textContent = formatTime(featuredSong.duration);
    featuredOpen.href = `player.html?artist=${encodeURIComponent(artist.slug)}&track=${encodeURIComponent(featuredSong.slug)}`;
    featuredPlay.addEventListener("click", () => {
      playPreviewSong(featuredSong);
    });
    playButton.addEventListener("click", () => {
      playPreviewSong(featuredSong);
    });
  }

  releases.innerHTML = songs.length
    ? songs.slice(0, 6).map((song) => buildSongCard(song, { showSelect: false })).join("")
    : emptyStateCard("No releases yet", "This artist does not have any songs in the catalog yet.");

  releases.addEventListener("click", (event) => {
    const playTrigger = event.target.closest("[data-song-play]");
    if (!playTrigger) {
      return;
    }

    const song = getSongById(Number(playTrigger.dataset.songId));
    if (song) {
      playPreviewSong(song);
    }
  });

  collaborationStrip.innerHTML = collaborations.length
    ? collaborations
        .map(
          (entry) => `
            <a href="artist.html?artist=${encodeURIComponent(entry.slug)}" class="artist-card glass-card min-w-[190px] rounded-[1.75rem] border border-white/10 p-5 text-center">
              <div class="artist-ring mx-auto w-fit">
                <img src="${escapeHtml(entry.image_url)}" alt="${escapeHtml(entry.name)} avatar" class="h-20 w-20 object-cover" />
              </div>
              <h3 class="mt-4 font-semibold">${escapeHtml(entry.name)}</h3>
              <p class="mt-1 text-sm text-white/50">${escapeHtml(entry.genre)}</p>
            </a>
          `
        )
        .join("")
    : emptyStateCard("No collaborations", "Add more artists to the catalog to populate this carousel.");

  stats.innerHTML = `
    <article class="glass-card rounded-[1.75rem] border border-white/10 p-6">
      <p class="text-sm uppercase tracking-[0.28em] text-white/45">Listeners</p>
      <p class="stat-number mt-4 text-4xl font-semibold text-white" data-counter="${artist.monthly_listeners}">0</p>
      <p class="mt-2 text-sm text-white/55">Monthly listeners tuned into ${escapeHtml(artist.name)}.</p>
    </article>
    <article class="glass-card rounded-[1.75rem] border border-white/10 p-6">
      <p class="text-sm uppercase tracking-[0.28em] text-white/45">Streams</p>
      <p class="stat-number mt-4 text-4xl font-semibold text-white" data-counter="${streamTotal}">0</p>
      <p class="mt-2 text-sm text-white/55">Total streams recorded across the artist catalog.</p>
    </article>
    <article class="glass-card rounded-[1.75rem] border border-white/10 p-6">
      <p class="text-sm uppercase tracking-[0.28em] text-white/45">Followers</p>
      <p class="stat-number mt-4 text-4xl font-semibold text-white" data-counter="${artist.followers}">0</p>
      <p class="mt-2 text-sm text-white/55">Followers receiving profile and release updates.</p>
    </article>
  `;
  setupCounters(stats);

  syncFollowButton(followButton, artist.slug);
  followButton.addEventListener("click", () => {
    toggleFollowedArtist(artist.slug);
    syncFollowButton(followButton, artist.slug);
  });
}

function toggleFollowedArtist(slug) {
  if (platformState.followedArtists.includes(slug)) {
    platformState.followedArtists = platformState.followedArtists.filter((entry) => entry !== slug);
  } else {
    platformState.followedArtists = [...platformState.followedArtists, slug];
  }

  localStorage.setItem(NAAD_FOLLOWED_ARTISTS, JSON.stringify(platformState.followedArtists));
}

function syncFollowButton(button, slug) {
  const isFollowing = platformState.followedArtists.includes(slug);
  button.innerHTML = isFollowing
    ? `<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12.5 9 16l10-10" /></svg><span>Following</span>`
    : `<svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 4v16M4 12h16" /></svg><span>Follow Artist</span>`;
}

async function setupGenrePage() {
  const root = document.querySelector("[data-genre-app]");
  if (!root) {
    return;
  }

  const elements = {
    copy: root.querySelector("[data-genre-copy]"),
    panels: root.querySelector("[data-genre-panels]"),
    title: root.querySelector("[data-genre-title]"),
    resultsCount: root.querySelector("[data-genre-results-count]"),
    search: root.querySelector("[data-genre-search]"),
    songs: root.querySelector("[data-genre-songs]"),
    playlists: root.querySelector("[data-genre-playlists]"),
    moods: root.querySelector("[data-genre-moods]"),
  };

  const params = new URLSearchParams(window.location.search);
  let currentGenre = params.get("genre");
  const genrePayload = await apiRequest("/songs/genres").catch((error) => {
    elements.songs.innerHTML = emptyStateCard("Genres unavailable", error.message);
    return null;
  });

  if (!genrePayload) {
    return;
  }

  const genres = genrePayload.data || [];
  if (!currentGenre) {
    currentGenre = genres[0] || "";
  }

  renderGenrePanels(elements.panels, genres, currentGenre);

  const refreshGenre = async () => {
    if (!currentGenre) {
      return;
    }

    try {
      const [songsPayload, playlistsPayload] = await Promise.all([loadSongs({ genre: currentGenre, search: elements.search.value.trim() }), loadPlaylists()]);
      elements.title.textContent = `${currentGenre} Frequency`;
      elements.copy.textContent = genreDescription(currentGenre);
      elements.resultsCount.textContent = `${songsPayload.data.length} songs in ${currentGenre}`;
      elements.songs.innerHTML = songsPayload.data.length
        ? songsPayload.data.map((song) => buildGenreSongRow(song)).join("")
        : emptyStateCard("No songs in this genre", "Try another genre or clear the search field.");
      elements.playlists.innerHTML = (playlistsPayload.data || []).length
        ? playlistsPayload.data
            .map(
              (playlist) => `
                <a href="player.html?playlist=${encodeURIComponent(playlist.slug)}" class="flex items-center gap-4 rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-4 transition hover:-translate-y-1 hover:border-neon-cyan/30">
                  <img src="${escapeHtml(playlist.cover_image_url || "https://images.unsplash.com/photo-1459749411175-04bf5292ceea?auto=format&fit=crop&w=320&q=80")}" alt="${escapeHtml(playlist.name)} cover" class="h-20 w-20 rounded-2xl object-cover" />
                  <div>
                    <h3 class="font-semibold">${escapeHtml(playlist.name)}</h3>
                    <p class="mt-1 text-sm text-white/50">${playlist.songs_count} songs • ${escapeHtml(playlist.owner?.name || "Naad-e-Maan")}</p>
                  </div>
                </a>
              `
            )
            .join("")
        : emptyStateCard("No playlists yet", "Create a playlist from the home page.");
      elements.moods.innerHTML = genreMoods(currentGenre)
        .map(
          (mood) => `
            <article class="rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-5">
              <p class="text-sm text-white/50">${escapeHtml(mood.label)}</p>
              <p class="mt-2 font-semibold">${escapeHtml(mood.value)}</p>
            </article>
          `
        )
        .join("");
    } catch (error) {
      elements.songs.innerHTML = emptyStateCard("Genre load failed", error.message);
    }
  };

  elements.panels.addEventListener("click", (event) => {
    const panel = event.target.closest("[data-genre-panel]");
    if (!panel) {
      return;
    }

    currentGenre = panel.dataset.genrePanel;
    renderGenrePanels(elements.panels, genres, currentGenre);
    const url = new URL(window.location.href);
    url.searchParams.set("genre", currentGenre);
    window.history.replaceState({}, "", url);
    refreshGenre().catch(() => null);
  });

  elements.songs.addEventListener("click", (event) => {
    const button = event.target.closest("[data-song-play]");
    if (!button) {
      return;
    }

    const song = getSongById(Number(button.dataset.songId));
    if (song) {
      playPreviewSong(song);
    }
  });

  elements.search.addEventListener(
    "input",
    debounce(() => {
      refreshGenre().catch(() => null);
    }, 300)
  );

  await refreshGenre();
}

function renderGenrePanels(container, genres, activeGenre) {
  container.innerHTML = genres
    .map(
      (genre) => `
        <button type="button" data-genre-panel="${escapeHtml(genre)}" class="genre-panel relative overflow-hidden rounded-[1.75rem] text-left transition ${
          genre === activeGenre ? "ring-2 ring-neon-cyan/60" : ""
        }">
          <img src="${escapeHtml(genreArtwork(genre))}" alt="${escapeHtml(genre)} genre panel" class="absolute inset-0 h-full w-full object-cover" />
          <div class="relative z-10 flex h-full items-end p-6">
            <div>
              <p class="text-xs uppercase tracking-[0.28em] text-white/50">Pulse</p>
              <h3 class="mt-2 font-display text-2xl font-semibold">${escapeHtml(genre)}</h3>
            </div>
          </div>
        </button>
      `
    )
    .join("");
}

function buildGenreSongRow(song) {
  return `
    <article class="glass-card flex flex-col gap-4 rounded-[1.5rem] border border-white/10 p-4 sm:flex-row sm:items-center">
      <img src="${escapeHtml(song.cover_image_url)}" alt="${escapeHtml(song.title)} cover" class="h-24 w-full rounded-[1.25rem] object-cover sm:w-24" />
      <div class="min-w-0 flex-1">
        <p class="text-xs uppercase tracking-[0.28em] text-neon-cyan/75">${escapeHtml(song.genre)}</p>
        <h3 class="mt-2 truncate text-xl font-semibold">${escapeHtml(song.title)}</h3>
        <a href="artist.html?artist=${encodeURIComponent(song.artist?.slug || "")}" class="mt-1 block text-sm text-white/55 transition hover:text-neon-cyan">${escapeHtml(song.artist?.name || "Naad-e-Maan")}</a>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-white/45">${formatTime(song.duration)}</span>
        <button type="button" data-song-play data-song-id="${song.id}" class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-950">${iconPlay("ml-0.5 h-5 w-5")}</button>
      </div>
    </article>
  `;
}

function buildSongCard(song, options = {}) {
  const { showSelect = false } = options;
  const isSelected = platformState.selectedSongIds.includes(song.id);

  return `
    <article class="album-card glass-card overflow-hidden rounded-[1.75rem] border border-white/10">
      <div class="relative">
        <img src="${escapeHtml(song.cover_image_url)}" alt="${escapeHtml(song.title)} cover" class="h-64 w-full object-cover" />
        <div class="album-overlay absolute inset-0 flex items-end justify-between p-5">
          <div>
            <p class="text-sm text-white/60">${escapeHtml(song.album?.title || song.title)}</p>
            <p class="text-xs uppercase tracking-[0.28em] text-neon-cyan">${escapeHtml(song.genre)}</p>
          </div>
          <div class="flex items-center gap-3">
            ${
              showSelect
                ? `<button type="button" data-song-select data-song-id="${song.id}" class="flex h-12 w-12 items-center justify-center rounded-full border border-white/20 bg-slate-950/70 text-white">${isSelected ? iconCheck("h-5 w-5") : iconPlus("h-5 w-5")}</button>`
                : ""
            }
            <button type="button" data-song-play data-song-id="${song.id}" class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-950">${iconPlay("ml-0.5 h-5 w-5")}</button>
          </div>
        </div>
      </div>
      <div class="p-5">
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <h3 class="truncate text-lg font-semibold">${escapeHtml(song.title)}</h3>
            <a href="artist.html?artist=${encodeURIComponent(song.artist?.slug || "")}" class="mt-1 block truncate text-sm text-white/55 transition hover:text-neon-cyan">${escapeHtml(song.artist?.name || "Naad-e-Maan")}</a>
          </div>
          <a href="player.html?track=${encodeURIComponent(song.slug)}" class="text-xs uppercase tracking-[0.28em] text-white/40 transition hover:text-neon-cyan">Player</a>
        </div>
      </div>
    </article>
  `;
}

function buildLoadingGrid() {
  return `
    <article class="glass-card animate-pulse rounded-[1.75rem] border border-white/10 p-5"><div class="h-64 rounded-[1.5rem] bg-white/5"></div></article>
    <article class="glass-card animate-pulse rounded-[1.75rem] border border-white/10 p-5"><div class="h-64 rounded-[1.5rem] bg-white/5"></div></article>
    <article class="glass-card animate-pulse rounded-[1.75rem] border border-white/10 p-5"><div class="h-64 rounded-[1.5rem] bg-white/5"></div></article>
  `;
}

function emptyStateCard(title, description) {
  return `
    <article class="glass-card rounded-[1.75rem] border border-dashed border-white/10 p-8 text-center">
      <h3 class="font-display text-2xl font-semibold">${escapeHtml(title)}</h3>
      <p class="mt-3 text-sm leading-7 text-white/55">${escapeHtml(description)}</p>
    </article>
  `;
}

function showMessage(element, message, type) {
  const palette =
    type === "success"
      ? "border border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan"
      : "border border-neon-pink/20 bg-neon-pink/10 text-neon-pink";
  element.textContent = message;
  element.className = `mt-6 rounded-[1.5rem] px-4 py-3 text-sm ${palette}`;
  element.classList.remove("hidden");
}

function genreArtwork(genre) {
  const artwork = {
    "Neo-Soul": "https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=900&q=80",
    Electronic: "https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=900&q=80",
    "Hip Hop": "https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=900&q=80",
    Jazz: "https://images.unsplash.com/photo-1511192336575-5a79af67a629?auto=format&fit=crop&w=900&q=80",
    Classical: "https://images.unsplash.com/photo-1507838153414-b4b713384a76?auto=format&fit=crop&w=900&q=80",
    Rock: "https://images.unsplash.com/photo-1501612780327-45045538702b?auto=format&fit=crop&w=900&q=80",
    Pop: "https://images.unsplash.com/photo-1499364615650-ec38552f4f34?auto=format&fit=crop&w=900&q=80",
  };

  return artwork[genre] || "https://images.unsplash.com/photo-1470229722913-7c0e2dbbafd3?auto=format&fit=crop&w=900&q=80";
}

function genreDescription(genre) {
  const descriptions = {
    "Neo-Soul": "Warm vocals, cinematic keys, and intimate rhythm design that sits between soul glow and ambient motion.",
    Electronic: "Luminous synths, club pressure, and pulse-driven drops built for neon-floor movement.",
    "Hip Hop": "Low-end weight, sharp verses, and detail-focused production with late-night atmosphere.",
    Jazz: "Smoky progressions, improvisation, and elegant nighttime swing.",
    Classical: "Expansive strings, piano detail, and quiet-room focus.",
    Rock: "Distortion, stage lights, and high-energy guitar release.",
    Pop: "Hooks, polish, and bright melodic lift.",
  };

  return descriptions[genre] || "A curated listening route through the Naad-e-Maan catalog.";
}

function genreMoods(genre) {
  const moods = {
    "Neo-Soul": [
      { label: "Late Night", value: "Velvet vocals and ambient glow" },
      { label: "Focus Room", value: "Soft groove with wide stereo space" },
      { label: "After Hours", value: "Intimate bass and slow pulse" },
      { label: "Sunrise", value: "Warm keys and lifted harmonies" },
    ],
    Electronic: [
      { label: "Drive Time", value: "Wide roads, bass pressure, and motion" },
      { label: "Club Fade", value: "Neon floor energy with synth air" },
      { label: "Night Run", value: "Fast pulse and luminous texture" },
      { label: "Afterglow", value: "Melodic decay with low-end warmth" },
    ],
    "Hip Hop": [
      { label: "Street Heat", value: "Punchy drums and direct verses" },
      { label: "Night Ride", value: "Dark textures and crisp hooks" },
      { label: "Concrete Focus", value: "Headphones on, details forward" },
      { label: "Low-End Drift", value: "Heavy bass and spacey atmosphere" },
    ],
  };

  return (
    moods[genre] || [
      { label: "Scene One", value: "Curated mood routing" },
      { label: "Scene Two", value: "Focused listening lane" },
      { label: "Scene Three", value: "Late-night playback" },
      { label: "Scene Four", value: "Wide-room ambience" },
    ]
  );
}

function debounce(callback, wait) {
  let timeoutId = null;
  return (...args) => {
    window.clearTimeout(timeoutId);
    timeoutId = window.setTimeout(() => {
      callback(...args);
    }, wait);
  };
}

function firstValidationMessage(errors) {
  if (!errors || typeof errors !== "object") {
    return "";
  }

  const firstKey = Object.keys(errors)[0];
  if (!firstKey || !Array.isArray(errors[firstKey])) {
    return "";
  }

  return errors[firstKey][0] || "";
}

function loadJson(key, fallback) {
  try {
    const raw = localStorage.getItem(key);
    return raw ? JSON.parse(raw) : fallback;
  } catch (error) {
    return fallback;
  }
}

function iconPlay(className = "ml-0.5 h-5 w-5") {
  return `<svg viewBox="0 0 24 24" class="${className}" fill="currentColor" aria-hidden="true"><path d="M8 6.5v11l9-5.5-9-5.5Z" /></svg>`;
}

function iconPause(className = "h-5 w-5") {
  return `<svg viewBox="0 0 24 24" class="${className}" fill="currentColor" aria-hidden="true"><path d="M8 6h3v12H8zM13 6h3v12h-3z" /></svg>`;
}

function iconPlus(className = "h-5 w-5") {
  return `<svg viewBox="0 0 24 24" class="${className}" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>`;
}

function iconCheck(className = "h-5 w-5") {
  return `<svg viewBox="0 0 24 24" class="${className}" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M5 12.5 9 16l10-10" /></svg>`;
}

function setupCreatorPortal() {
  const portal = document.querySelector("[data-creator-portal]");
  if (!portal) {
    return;
  }

  const authPanel = portal.querySelector("[data-creator-auth]");
  const appPanel = portal.querySelector("[data-creator-app]");
  const loginForm = portal.querySelector("[data-creator-login-form]");
  const uploadForm = portal.querySelector("[data-creator-upload-form]");
  const logoutButton = portal.querySelector("[data-creator-logout]");
  const sessionName = portal.querySelector("[data-creator-session-name]");
  const artistField = portal.querySelector("[data-creator-artist-field]");
  const status = portal.querySelector("[data-creator-status]");
  const previewCover = portal.querySelector("[data-preview-cover]");
  const previewTitle = portal.querySelector("[data-preview-title]");
  const previewArtist = portal.querySelector("[data-preview-artist]");
  const previewGenre = portal.querySelector("[data-preview-genre]");
  const previewAudio = portal.querySelector("[data-preview-audio]");
  const placeholderCover = "https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=900&q=80";
  const previewUrls = { cover: null, audio: null };

  if (!authPanel || !appPanel || !loginForm || !uploadForm || !logoutButton || !sessionName || !artistField || !status || !previewCover || !previewTitle || !previewArtist || !previewGenre || !previewAudio) {
    return;
  }

  loginForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const formData = new FormData(loginForm);
    const stageName = String(formData.get("stageName") || "").trim();
    const password = String(formData.get("password") || "").trim();

    if (!stageName || !password) {
      return;
    }

    setCreatorSession({ stageName });
    loginForm.reset();
    syncSession();
    showStatus(`Logged in as ${stageName}. You can upload a song now.`, "success");
  });

  logoutButton.addEventListener("click", () => {
    clearCreatorSession();
    uploadForm.reset();
    clearStatus();
    syncSession();
    syncPreview();
  });

  uploadForm.querySelectorAll("input, select").forEach((field) => {
    const eventName = field.type === "file" || field.tagName === "SELECT" ? "change" : "input";
    field.addEventListener(eventName, syncPreview);
  });

  uploadForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    const submitButton = uploadForm.querySelector("button[type='submit']");
    const formData = new FormData(uploadForm);
    const songTitle = String(formData.get("songTitle") || "").trim();
    const artistName = String(formData.get("artistName") || "").trim();
    const genre = String(formData.get("genre") || "").trim();
    const albumArtFile = uploadForm.elements.albumArt?.files?.[0];
    const audioFile = uploadForm.elements.audioFile?.files?.[0];

    if (!songTitle || !artistName || !genre || !albumArtFile || !audioFile) {
      showStatus("Complete all fields and select both files before publishing.", "error");
      return;
    }

    if (!audioFile.name.toLowerCase().endsWith(".mp3")) {
      showStatus("Please upload an MP3 file for the track preview.", "error");
      return;
    }

    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = "Publishing...";
    }

    try {
      await saveCreatorUpload({
        id: `upload-${Date.now()}`,
        title: songTitle,
        artist: artistName,
        genre,
        albumArtBlob: albumArtFile,
        audioBlob: audioFile,
        publishedAt: new Date().toISOString(),
      });

      localStorage.setItem(NAAD_RELEASE_PING, String(Date.now()));
      showStatus(`"${songTitle}" is now live in Latest Releases.`, "success");
      uploadForm.reset();
      artistField.value = getCreatorSession()?.stageName || artistName;
      syncPreview();
      await renderLatestReleases();
    } catch (error) {
      showStatus("Upload failed in this browser session. Please try again.", "error");
    } finally {
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.textContent = "Publish to Latest Releases";
      }
    }
  });

  syncSession();
  syncPreview();

  function syncSession() {
    const session = getCreatorSession();
    const isLoggedIn = Boolean(session?.stageName);
    authPanel.classList.toggle("hidden", isLoggedIn);
    appPanel.classList.toggle("hidden", !isLoggedIn);

    if (isLoggedIn) {
      sessionName.textContent = session.stageName;
      if (!artistField.value.trim()) {
        artistField.value = session.stageName;
      }
    }
  }

  function syncPreview() {
    const titleValue = uploadForm.elements.songTitle?.value?.trim() || "Your Song Title";
    const artistValue = uploadForm.elements.artistName?.value?.trim() || getCreatorSession()?.stageName || "Artist Name";
    const genreValue = uploadForm.elements.genre?.value?.trim() || "Awaiting Genre";
    const coverFile = uploadForm.elements.albumArt?.files?.[0];
    const audioFile = uploadForm.elements.audioFile?.files?.[0];

    previewTitle.textContent = titleValue;
    previewArtist.textContent = artistValue;
    previewGenre.textContent = genreValue;

    if (previewUrls.cover) {
      URL.revokeObjectURL(previewUrls.cover);
      previewUrls.cover = null;
    }

    if (coverFile) {
      previewUrls.cover = URL.createObjectURL(coverFile);
      previewCover.src = previewUrls.cover;
    } else {
      previewCover.src = placeholderCover;
    }

    if (previewUrls.audio) {
      URL.revokeObjectURL(previewUrls.audio);
      previewUrls.audio = null;
    }

    if (audioFile) {
      previewUrls.audio = URL.createObjectURL(audioFile);
      previewAudio.src = previewUrls.audio;
      previewAudio.load();
    } else {
      previewAudio.removeAttribute("src");
      previewAudio.load();
    }
  }

  function showStatus(message, type) {
    const palette =
      type === "success"
        ? "border border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan"
        : "border border-neon-pink/20 bg-neon-pink/10 text-neon-pink";
    status.className = `mt-6 rounded-[1.5rem] px-4 py-3 text-sm ${palette}`;
    status.textContent = message;
    status.classList.remove("hidden");
  }

  function clearStatus() {
    status.textContent = "";
    status.className = "hidden mt-6 rounded-[1.5rem] px-4 py-3 text-sm";
  }
}

async function renderLatestReleases() {
  const grid = document.querySelector("[data-latest-releases-grid]");
  if (!grid) {
    return;
  }

  grid.querySelectorAll("[data-uploaded-release]").forEach((node) => node.remove());

  try {
    const uploads = await getCreatorUploads();
    if (!uploads.length) {
      return;
    }

    uploads.sort((left, right) => new Date(right.publishedAt) - new Date(left.publishedAt));
    const fragment = document.createDocumentFragment();
    uploads.forEach((upload) => {
      fragment.appendChild(createUploadedReleaseCard(upload));
    });
    grid.prepend(fragment);
  } catch (error) {
    // Ignore local persistence failures and keep the static releases visible.
  }
}

function createUploadedReleaseCard(upload) {
  const article = document.createElement("article");
  article.dataset.uploadedRelease = "true";
  article.className = "album-card glass-card overflow-hidden rounded-[1.75rem] border border-white/10";

  const artUrl = upload.albumArtBlob instanceof Blob ? URL.createObjectURL(upload.albumArtBlob) : "https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=900&q=80";
  article.innerHTML = `
    <div class="relative">
      <img src="${escapeHtml(artUrl)}" alt="${escapeHtml(upload.title)} cover" class="h-64 w-full object-cover" />
      <div class="album-overlay absolute inset-0 flex items-end justify-between p-5">
        <div>
          <p class="text-sm text-white/60">${escapeHtml(upload.title)}</p>
          <p class="text-xs uppercase tracking-[0.28em] text-neon-cyan">${escapeHtml(upload.genre)}</p>
        </div>
        <button type="button" class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-slate-950">
          <svg viewBox="0 0 24 24" class="ml-0.5 h-5 w-5" fill="currentColor" aria-hidden="true">
            <path d="M8 6.5v11l9-5.5-9-5.5Z" />
          </svg>
        </button>
      </div>
    </div>
    <div class="p-5">
      <h3 class="text-lg font-semibold">${escapeHtml(upload.title)}</h3>
      <p class="mt-1 text-sm text-white/55">${escapeHtml(upload.artist)}</p>
    </div>
  `;

  const image = article.querySelector("img");
  if (image && upload.albumArtBlob instanceof Blob) {
    image.addEventListener(
      "load",
      () => {
        URL.revokeObjectURL(artUrl);
      },
      { once: true }
    );
  }

  return article;
}

function getCreatorSession() {
  try {
    const raw = localStorage.getItem(NAAD_CREATOR_SESSION);
    return raw ? JSON.parse(raw) : null;
  } catch (error) {
    return null;
  }
}

function setCreatorSession(session) {
  localStorage.setItem(NAAD_CREATOR_SESSION, JSON.stringify(session));
}

function clearCreatorSession() {
  localStorage.removeItem(NAAD_CREATOR_SESSION);
}

function openUploadDatabase() {
  return new Promise((resolve, reject) => {
    if (typeof indexedDB === "undefined") {
      reject(new Error("IndexedDB is unavailable"));
      return;
    }

    const request = indexedDB.open(NAAD_UPLOAD_DB, 1);

    request.onupgradeneeded = () => {
      const db = request.result;
      if (!db.objectStoreNames.contains(NAAD_UPLOAD_STORE)) {
        db.createObjectStore(NAAD_UPLOAD_STORE, { keyPath: "id" });
      }
    };

    request.onsuccess = () => resolve(request.result);
    request.onerror = () => reject(request.error || new Error("Could not open upload database"));
  });
}

async function getCreatorUploads() {
  const db = await openUploadDatabase();

  return new Promise((resolve, reject) => {
    const transaction = db.transaction(NAAD_UPLOAD_STORE, "readonly");
    const store = transaction.objectStore(NAAD_UPLOAD_STORE);
    const request = store.getAll();

    request.onsuccess = () => resolve(Array.isArray(request.result) ? request.result : []);
    request.onerror = () => reject(request.error || new Error("Could not read uploads"));
    transaction.oncomplete = () => db.close();
    transaction.onabort = () => db.close();
  });
}

async function saveCreatorUpload(record) {
  const db = await openUploadDatabase();

  return new Promise((resolve, reject) => {
    const transaction = db.transaction(NAAD_UPLOAD_STORE, "readwrite");
    const store = transaction.objectStore(NAAD_UPLOAD_STORE);
    const request = store.put(record);

    request.onsuccess = () => resolve(record);
    request.onerror = () => reject(request.error || new Error("Could not save upload"));
    transaction.oncomplete = () => db.close();
    transaction.onabort = () => db.close();
  });
}

function escapeHtml(value) {
  return String(value)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}
