(() => {
  const STORAGE_KEY = "naad-player-state-v1";
  const PLAY_ICON = '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor" aria-hidden="true"><path d="M8 6.5v11l9-5.5-9-5.5Z" /></svg>';
  const PAUSE_ICON = '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="currentColor" aria-hidden="true"><path d="M9 6.5h2.75v11H9zM12.25 6.5H15v11h-2.75z" /></svg>';
  let playlistModal = null;

  document.addEventListener("DOMContentLoaded", () => {
    const bootstrap = json(document.getElementById("naad-bootstrap")?.textContent, {});
    const state = {
      bootstrap,
      playlists: Array.isArray(bootstrap.playlists) ? bootstrap.playlists.map(normalizePlaylist) : [],
      playlistsLoaded: Array.isArray(bootstrap.playlists),
    };

    initMenu();
    const player = initPlayer(bootstrap);
    window.NaadPlayer = player;
    window.naadPlayer = player;
    playlistModal = initPlaylistModal(state, bootstrap);
    initFeaturedPlayer(player, bootstrap);
    initSearch(bootstrap, player);
    initCounters();

    document.addEventListener("click", async (event) => {
      const contextualPlay = event.target.closest("[data-track-play-context]");
      if (contextualPlay && !event.target.closest("[data-track-card]")) {
        event.preventDefault();
        player.fromContainer(document.querySelector(contextualPlay.dataset.trackPlayContext), null, true);
        return;
      }

      const trackPlay = event.target.closest("[data-track-play]");
      if (trackPlay) {
        event.preventDefault();
        const card = trackPlay.closest("[data-track-card]");
        player.fromContainer(card?.parentElement || document, card, true);
        return;
      }

      const playAll = event.target.closest("[data-play-all]");
      if (playAll) {
        event.preventDefault();
        player.fromContainer(document.querySelector(".listen-library") || document, null, true);
        return;
      }

      const playlistQueue = event.target.closest("[data-playlist-queue]");
      if (playlistQueue) {
        event.preventDefault();
        player.setQueue(json(playlistQueue.getAttribute("data-playlist-queue"), []), 0, true);
        return;
      }

      const searchTrack = event.target.closest("[data-search-track]");
      if (searchTrack) {
        event.preventDefault();
        player.setQueue([json(searchTrack.getAttribute("data-search-track"), null)], 0, true);
        closeSearchPanels();
        return;
      }

      const favorite = event.target.closest("[data-favorite-toggle]");
      if (favorite) {
        event.preventDefault();
        if (favorite.disabled) {
          return;
        }
        favorite.disabled = true;
        try {
          const payload = await request(favorite.dataset.favoriteUrl, { method: "POST" }, bootstrap);
          syncFavorites(payload.song_id, payload.favorited);
        } catch (error) {
          favorite.title = error.message;
        } finally {
          favorite.disabled = false;
        }
        return;
      }

      const follow = event.target.closest("[data-follow-toggle]");
      if (follow) {
        event.preventDefault();
        if (follow.disabled) {
          return;
        }
        follow.disabled = true;
        try {
          const payload = await request(follow.dataset.followUrl, { method: "POST" }, bootstrap);
          syncFollows(payload.artist_id, payload.following);
        } catch (error) {
          follow.title = error.message;
        } finally {
          follow.disabled = false;
        }
        return;
      }

      const playlistOpen = event.target.closest("[data-playlist-open]");
      if (playlistOpen) {
        event.preventDefault();
        try {
          await playlistModal?.open(playlistOpen);
        } catch (error) {
          playlistOpen.title = error.message;
        }
        return;
      }

      if (event.target.closest("[data-playlist-modal-close]") || event.target === playlistModal?.root) {
        playlistModal?.close();
      }

      if (!event.target.closest("[data-search-root]")) {
        closeSearchPanels();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeSearchPanels();
        playlistModal?.close();
        player.closeQueue?.();
      }
    });
  });

  function initPlayer(bootstrap) {
    const root = document.querySelector("[data-mini-player]");
    if (!root) {
      return { setQueue: () => false, fromContainer: () => false };
    }

    const audio = root.querySelector("[data-player-audio]");
    const el = {
      root,
      cover: root.querySelector("[data-player-cover]"),
      title: root.querySelector("[data-player-title]"),
      artist: root.querySelector("[data-player-artist]"),
      copy: root.querySelector("[data-player-copy]"),
      current: root.querySelector("[data-player-current]"),
      duration: root.querySelector("[data-player-duration]"),
      progress: root.querySelector("[data-player-progress]"),
      volume: root.querySelector("[data-player-volume]"),
      prev: root.querySelector("[data-player-prev]"),
      next: root.querySelector("[data-player-next]"),
      toggle: root.querySelector("[data-player-toggle]"),
      icon: root.querySelector("[data-player-icon]"),
      waveform: root.querySelector("[data-player-waveform]"),
      queueCount: root.querySelector("[data-player-queue-count]"),
      queueToggle: root.querySelector("[data-player-queue-toggle]"),
      queueDrawer: root.querySelector("[data-player-queue-drawer]"),
      queueClose: root.querySelector("[data-player-queue-close]"),
      queueList: root.querySelector("[data-player-queue-list]"),
      queueEmpty: root.querySelector("[data-player-queue-empty]"),
    };

    const saved = json(localStorage.getItem(STORAGE_KEY), {});
    const state = {
      queue: normalizeQueue(saved.queue).length ? normalizeQueue(saved.queue) : normalizeQueue(json(root.querySelector("[data-default-queue]")?.textContent, [])),
      index: clamp(saved.index ?? 0, 0, 999),
      pendingSeek: Number(saved.currentTime) || 0,
      lastSavedSecond: -1,
    };
    const ui = {
      queueOpen: false,
    };

    const initialVolume = clamp(saved.volume ?? el.volume?.value ?? 72, 0, 100);
    if (el.volume) {
      el.volume.value = String(initialVolume);
      paintRange(el.volume);
    }
    audio.volume = initialVolume / 100;

    el.prev?.addEventListener("click", () => shift(-1));
    el.next?.addEventListener("click", () => shift(1));
    el.queueToggle?.addEventListener("click", () => setQueueOpen(!ui.queueOpen));
    el.queueClose?.addEventListener("click", () => setQueueOpen(false));
    el.queueList?.addEventListener("click", (event) => {
      const item = event.target.closest("[data-player-queue-index]");
      if (!item) {
        return;
      }
      const index = Number(item.dataset.playerQueueIndex);
      if (!Number.isInteger(index) || !state.queue[index]) {
        return;
      }
      state.index = index;
      load(true, 0);
    });
    el.toggle?.addEventListener("click", () => {
      if (!state.queue.length) {
        return;
      }
      if (!audio.src) {
        load(true, 0);
        return;
      }
      if (audio.paused) {
        audio.play().catch(() => {});
      } else {
        audio.pause();
      }
    });
    el.progress?.addEventListener("input", () => {
      audio.currentTime = Number(el.progress.value || 0);
      syncProgress();
      persist(true);
    });
    el.volume?.addEventListener("input", () => {
      audio.volume = clamp(el.volume.value, 0, 100) / 100;
      paintRange(el.volume);
      persist(true);
    });

    audio.addEventListener("loadedmetadata", () => {
      if (state.pendingSeek > 0) {
        audio.currentTime = Math.min(state.pendingSeek, audio.duration || state.pendingSeek);
      }
      state.pendingSeek = 0;
      syncProgress();
      persist(true);
    });
    audio.addEventListener("timeupdate", () => {
      syncProgress();
      const currentSecond = Math.floor(audio.currentTime || 0);
      if (currentSecond !== state.lastSavedSecond) {
        state.lastSavedSecond = currentSecond;
        persist(false);
      }
    });
    audio.addEventListener("play", syncPlayback);
    audio.addEventListener("pause", syncPlayback);
    audio.addEventListener("ended", () => shift(1));

    if (state.queue.length) {
      state.index = clamp(state.index, 0, state.queue.length - 1);
      load(false, state.pendingSeek);
    } else {
      renderQueue();
      setQueueOpen(false);
      syncPlayback();
      emitState();
    }

    return {
      setQueue(queue, index = 0, autoplay = false) {
        const normalized = normalizeQueue(queue);
        if (!normalized.length) {
          return false;
        }
        state.queue = normalized;
        state.index = clamp(index, 0, normalized.length - 1);
        load(autoplay, 0);
        return true;
      },
      fromContainer(container, card, autoplay = false) {
        const queue = Array.from((container || document).querySelectorAll("[data-track-card]"))
          .map((item) => normalizeTrack(json(item.getAttribute("data-track-card"), null)))
          .filter(Boolean);
        if (!queue.length) {
          return false;
        }
        const selected = card ? normalizeTrack(json(card.getAttribute("data-track-card"), null)) : null;
        const index = selected ? Math.max(queue.findIndex((track) => String(track.id) === String(selected.id)), 0) : 0;
        return this.setQueue(queue, index, autoplay);
      },
      toggle() {
        if (!state.queue.length) {
          return false;
        }
        if (!audio.src) {
          load(true, 0);
          return true;
        }
        if (audio.paused) {
          audio.play().catch(() => {});
        } else {
          audio.pause();
        }
        return true;
      },
      seek(value) {
        audio.currentTime = clamp(value, 0, audio.duration || state.queue[state.index]?.duration || 0);
        syncProgress();
        persist(true);
        emitState();
      },
      setVolume(value) {
        audio.volume = clamp(value, 0, 100) / 100;
        if (el.volume) {
          el.volume.value = String(Math.round(audio.volume * 100));
          paintRange(el.volume);
        }
        persist(true);
        emitState();
      },
      snapshot() {
        return playerState();
      },
      openQueue() {
        setQueueOpen(true);
        return true;
      },
      closeQueue() {
        setQueueOpen(false);
        return true;
      },
      toggleQueue() {
        setQueueOpen(!ui.queueOpen);
        return ui.queueOpen;
      },
    };

    function shift(step) {
      if (!state.queue.length) {
        return;
      }
      state.index = (state.index + step + state.queue.length) % state.queue.length;
      load(true, 0);
    }

    function load(autoplay, seekTime) {
      const track = state.queue[state.index];
      if (!track) {
        return;
      }

      el.root.classList.remove("hidden");
      el.cover.src = track.cover_image_url || defaultCover();
      el.cover.alt = "Cover for " + track.title;
      el.title.textContent = track.title;
      el.artist.textContent = track.artist?.name || "Naad-e-Maan";
      el.artist.href = track.artist?.slug ? artistUrl(bootstrap, track.artist.slug) : (bootstrap.ajax?.artists || "/artists");
      el.copy.textContent = [track.genre, track.album?.title || "Single release", formatTime(track.duration)].join(" - ");
      el.progress.max = String(Math.max(track.duration || 0, 1));

      if (audio.dataset.trackUrl !== track.audio_url) {
        audio.dataset.trackUrl = track.audio_url;
        state.pendingSeek = seekTime;
        audio.src = track.audio_url;
        audio.load();
      } else if (seekTime) {
        audio.currentTime = seekTime;
      }

      highlightTrack(track);
      renderQueue();
      syncProgress();
      syncPlayback();
      persist(true);

      if (autoplay) {
        audio.play().catch(() => {});
      }

      emitState();
    }

    function syncProgress() {
      const duration = Number.isFinite(audio.duration) ? audio.duration : Number(state.queue[state.index]?.duration || 0);
      el.current.textContent = formatTime(audio.currentTime || 0);
      el.duration.textContent = formatTime(duration);
      el.progress.max = String(Math.max(duration || 0, 1));
      el.progress.value = String(Math.min(audio.currentTime || 0, duration || audio.currentTime || 0));
      paintRange(el.progress);
      emitState();
    }

    function syncPlayback() {
      const playing = !audio.paused && Boolean(audio.src);
      el.icon.innerHTML = playing ? PAUSE_ICON : PLAY_ICON;
      el.waveform?.classList.toggle("is-paused", !playing);
      el.toggle?.setAttribute("aria-label", playing ? "Pause queue" : "Play queue");
      renderQueue();
      emitState();
    }

    function persist(force) {
      const track = state.queue[state.index];
      if (!track) {
        return;
      }
      if (!force && !Number.isInteger(audio.currentTime || 0)) {
        return;
      }
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
          queue: state.queue,
          index: state.index,
          currentTime: audio.currentTime || 0,
          volume: Math.round(audio.volume * 100),
        }));
      } catch (error) {
      }
    }

    function playerState() {
      const track = state.queue[state.index] || null;
      const duration = Number.isFinite(audio.duration) ? audio.duration : Number(track?.duration || 0);
      return {
        currentTrack: track,
        queue: state.queue.slice(),
        index: state.index,
        currentTime: audio.currentTime || 0,
        duration,
        volume: Math.round(audio.volume * 100),
        isPlaying: !audio.paused && Boolean(audio.src),
        queueOpen: ui.queueOpen,
      };
    }

    function emitState() {
      document.dispatchEvent(new CustomEvent("naad:player-state", {
        detail: playerState(),
      }));
    }

    function setQueueOpen(open) {
      ui.queueOpen = Boolean(open) && state.queue.length > 0;
      el.queueDrawer?.classList.toggle("is-open", ui.queueOpen);
      el.queueDrawer?.setAttribute("aria-hidden", ui.queueOpen ? "false" : "true");
      el.queueToggle?.setAttribute("aria-expanded", ui.queueOpen ? "true" : "false");
      el.queueToggle?.setAttribute("aria-label", ui.queueOpen ? "Hide queue" : "Show queue");
      el.queueToggle?.classList.toggle("is-active", ui.queueOpen);
      emitState();
    }

    function renderQueue() {
      if (!el.queueList) {
        return;
      }

      const queue = state.queue;
      const current = queue[state.index] || null;
      const playing = !audio.paused && Boolean(audio.src);

      el.queueCount.textContent = String(queue.length);
      el.queueToggle?.toggleAttribute("disabled", !queue.length);
      el.queueToggle?.classList.toggle("is-disabled", !queue.length);
      el.queueEmpty?.classList.toggle("hidden", Boolean(queue.length));
      el.queueList.classList.toggle("hidden", !queue.length);

      if (!queue.length) {
        el.queueList.replaceChildren();
        setQueueOpen(false);
        return;
      }

      const fragment = document.createDocumentFragment();

      queue.forEach((track, index) => {
        const item = document.createElement("button");
        item.type = "button";
        item.className = "player-queue-item" + (current && String(current.id) === String(track.id) ? " is-active" : "");
        item.dataset.playerQueueIndex = String(index);
        item.setAttribute("aria-label", "Play " + track.title + " by " + (track.artist?.name || "Naad-e-Maan"));
        item.setAttribute("role", "listitem");

        const position = document.createElement("span");
        position.className = "player-queue-position";
        position.textContent = String(index + 1).padStart(2, "0");

        const thumb = document.createElement("img");
        thumb.className = "player-queue-thumb";
        thumb.src = track.cover_image_url || defaultCover();
        thumb.alt = "";

        const body = document.createElement("span");
        body.className = "player-queue-copy";

        const title = document.createElement("span");
        title.className = "player-queue-title";
        title.textContent = track.title;

        const meta = document.createElement("span");
        meta.className = "player-queue-meta";
        meta.textContent = [track.artist?.name || "Naad-e-Maan", track.genre].filter(Boolean).join(" / ");

        body.append(title, meta);

        const status = document.createElement("span");
        status.className = "player-queue-status";

        if (current && String(current.id) === String(track.id)) {
          const pill = document.createElement("span");
          pill.className = "player-queue-pill";
          pill.textContent = playing ? "Playing" : "Paused";
          status.appendChild(pill);
        } else {
          status.textContent = formatTime(track.duration);
        }

        item.append(position, thumb, body, status);
        fragment.appendChild(item);
      });

      el.queueList.replaceChildren(fragment);
    }
  }

  function initFeaturedPlayer(player, bootstrap) {
    const root = document.querySelector("[data-featured-player]");
    if (!root) {
      return;
    }

    const queue = normalizeQueue(json(root.querySelector("[data-featured-queue]")?.textContent, []));
    if (!queue.length) {
      return;
    }

    const el = {
      cover: root.querySelector("[data-featured-cover]"),
      title: root.querySelector("[data-featured-title]"),
      artist: root.querySelector("[data-featured-artist]"),
      current: root.querySelector("[data-featured-current]"),
      duration: root.querySelector("[data-featured-duration]"),
      progress: root.querySelector("[data-featured-progress]"),
      volume: root.querySelector("[data-featured-volume]"),
      volumeLabel: root.querySelector("[data-featured-volume-label]"),
      prev: root.querySelector("[data-featured-prev]"),
      next: root.querySelector("[data-featured-next]"),
      toggle: root.querySelector("[data-featured-toggle]"),
      icon: root.querySelector("[data-featured-icon]"),
      waveform: root.querySelector("[data-featured-waveform]"),
    };

    let activeIndex = 0;

    paintRange(el.progress);
    paintRange(el.volume);
    render(queue[0], false, 0, queue[0].duration || 0, Number(el.volume?.value || 72));

    el.toggle?.addEventListener("click", () => {
      const snapshot = player.snapshot?.();
      const snapshotIndex = queueIndex(snapshot?.currentTrack);
      if (snapshotIndex < 0) {
        player.setQueue(queue, activeIndex, true);
        return;
      }
      player.toggle?.();
    });

    el.prev?.addEventListener("click", () => {
      const snapshotIndex = queueIndex(player.snapshot?.().currentTrack);
      activeIndex = snapshotIndex >= 0 ? (snapshotIndex - 1 + queue.length) % queue.length : Math.max(queue.length - 1, 0);
      player.setQueue(queue, activeIndex, true);
    });

    el.next?.addEventListener("click", () => {
      const snapshotIndex = queueIndex(player.snapshot?.().currentTrack);
      activeIndex = snapshotIndex >= 0 ? (snapshotIndex + 1) % queue.length : Math.min(1, queue.length - 1);
      player.setQueue(queue, activeIndex, true);
    });

    el.progress?.addEventListener("input", () => {
      player.seek?.(Number(el.progress.value || 0));
    });

    el.volume?.addEventListener("input", () => {
      const value = Number(el.volume.value || 0);
      player.setVolume?.(value);
      updateVolumeLabel(value);
    });

    document.addEventListener("naad:player-state", (event) => {
      const detail = event.detail || {};
      const currentTrack = detail.currentTrack || null;
      const index = queueIndex(currentTrack);
      if (index >= 0) {
        activeIndex = index;
        render(queue[index], detail.isPlaying, detail.currentTime, detail.duration, detail.volume);
      } else {
        render(queue[activeIndex], false, 0, queue[activeIndex]?.duration || 0, detail.volume ?? Number(el.volume?.value || 72));
      }
    });

    const snapshot = player.snapshot?.();
    if (snapshot) {
      const initialIndex = queueIndex(snapshot.currentTrack);
      if (initialIndex >= 0) {
        activeIndex = initialIndex;
        render(queue[activeIndex], snapshot.isPlaying, snapshot.currentTime, snapshot.duration, snapshot.volume);
      }
    }

    function render(track, playing, currentTime, duration, volume) {
      if (!track) {
        return;
      }

      el.cover.src = track.cover_image_url || defaultCover();
      el.cover.alt = "Cover for " + track.title;
      el.title.textContent = track.title;
      el.artist.textContent = track.artist?.name || "Naad-e-Maan";
      el.artist.href = track.artist?.slug ? artistUrl(bootstrap, track.artist.slug) : (bootstrap.ajax?.artists || "/artists");
      el.current.textContent = formatTime(currentTime || 0);
      el.duration.textContent = formatTime(duration || track.duration || 0);
      el.progress.max = String(Math.max(duration || track.duration || 0, 1));
      el.progress.value = String(Math.min(currentTime || 0, duration || track.duration || currentTime || 0));
      paintRange(el.progress);
      el.icon.innerHTML = playing ? PAUSE_ICON : PLAY_ICON;
      el.waveform?.classList.toggle("is-paused", !playing);

      const nextVolume = Number.isFinite(Number(volume)) ? Number(volume) : Number(el.volume?.value || 72);
      if (el.volume) {
        el.volume.value = String(nextVolume);
        paintRange(el.volume);
      }
      updateVolumeLabel(nextVolume);
    }

    function updateVolumeLabel(value) {
      if (el.volumeLabel) {
        el.volumeLabel.textContent = Math.round(Number(value) || 0) + "%";
      }
    }

    function queueIndex(track) {
      if (!track) {
        return -1;
      }

      return queue.findIndex((item) => String(item.id) === String(track.id));
    }
  }

  function initPlaylistModal(state, bootstrap) {
    const root = document.querySelector("[data-playlist-modal]");
    const form = root?.querySelector("[data-playlist-form]");
    if (!root || !form) {
      return null;
    }

    const songId = form.querySelector('input[name="song_id"]');
    const select = form.querySelector('select[name="playlist_id"]');
    const playlistName = form.querySelector('input[name="playlist_name"]');
    const feedback = form.querySelector("[data-playlist-feedback]");
    let actionUrl = "";

    renderOptions();

    form.addEventListener("submit", async (event) => {
      event.preventDefault();
      const payload = new URLSearchParams();
      if (select.value.trim()) {
        payload.set("playlist_id", select.value.trim());
      }
      if (playlistName.value.trim()) {
        payload.set("playlist_name", playlistName.value.trim());
      }

      try {
        const response = await request(actionUrl, { method: "POST", body: payload }, bootstrap);
        if (response.playlist) {
          mergePlaylist(state.playlists, response.playlist);
          renderOptions();
          select.value = String(response.playlist.id);
        }
        showFeedback(response.message || "Track added to playlist.", false);
        setTimeout(() => close(), 900);
      } catch (error) {
        showFeedback(error.message, true);
      }
    });

    return {
      root,
      async open(button) {
        actionUrl = button.dataset.playlistUrl || "";
        songId.value = button.dataset.songId || "";
        select.value = "";
        playlistName.value = "";
        showFeedback("", false);
        root.classList.remove("hidden");
        root.classList.add("flex");
        document.body.classList.add("overflow-hidden");
        if (!state.playlistsLoaded && bootstrap.ajax?.playlists) {
          try {
            const payload = await request(bootstrap.ajax.playlists, { method: "GET" }, bootstrap);
            state.playlists = Array.isArray(payload.playlists) ? payload.playlists.map(normalizePlaylist) : [];
            state.playlistsLoaded = true;
            renderOptions();
          } catch (error) {
            showFeedback(error.message, true);
          }
        }
      },
      close,
    };

    function close() {
      root.classList.add("hidden");
      root.classList.remove("flex");
      document.body.classList.remove("overflow-hidden");
      showFeedback("", false);
    }

    function renderOptions() {
      const current = select.value;
      const options = ['<option value="" class="bg-slate-950">Select a playlist</option>'];
      state.playlists.slice().sort((a, b) => a.name.localeCompare(b.name)).forEach((playlist) => {
        options.push('<option value="' + escape(playlist.id) + '" class="bg-slate-950">' + escape(playlist.name) + "</option>");
      });
      select.innerHTML = options.join("");
      if (current) {
        select.value = current;
      }
    }

    function showFeedback(message, isError) {
      feedback.className = "hidden rounded-[1.25rem] px-4 py-3 text-sm";
      feedback.textContent = message;
      if (!message) {
        return;
      }
      feedback.classList.remove("hidden");
      feedback.classList.add(isError ? "playlist-feedback-error" : "playlist-feedback-success");
    }
  }

  function initSearch(bootstrap, player) {
    if (!bootstrap.ajax?.search) {
      return;
    }

    document.querySelectorAll("[data-search-root]").forEach((root) => {
      const input = root.querySelector("[data-global-search]");
      const panel = root.querySelector("[data-search-results]");
      if (!input || !panel) {
        return;
      }

      let timer = 0;
      let token = 0;

      input.addEventListener("focus", () => {
        if (panel.innerHTML.trim()) {
          panel.classList.remove("hidden");
        }
      });

      input.addEventListener("input", () => {
        const query = input.value.trim();
        clearTimeout(timer);
        if (query.length < 1) {
          panel.innerHTML = "";
          panel.classList.add("hidden");
          return;
        }
        timer = window.setTimeout(async () => {
          const currentToken = ++token;
          panel.innerHTML = '<div class="search-empty-state">Searching the Naad-e-Maan catalog...</div>';
          panel.classList.remove("hidden");
          try {
            const payload = await request(bootstrap.ajax.search + "?q=" + encodeURIComponent(query), { method: "GET" }, bootstrap);
            if (currentToken !== token) {
              return;
            }
            panel.innerHTML = searchMarkup(payload, bootstrap, query);
            panel.classList.remove("hidden");
          } catch (error) {
            panel.innerHTML = '<div class="search-empty-state">' + escape(error.message) + "</div>";
            panel.classList.remove("hidden");
          }
        }, 300);
      });
    });
  }

  function initMenu() {
    const toggle = document.querySelector("[data-mobile-menu-toggle]");
    const menu = document.querySelector("[data-mobile-menu]");
    if (!toggle || !menu) {
      return;
    }
    toggle.setAttribute("aria-expanded", "false");
    toggle.addEventListener("click", () => {
      const open = menu.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
    });
    menu.addEventListener("click", (event) => {
      if (event.target.closest("a, button[type='submit']")) {
        menu.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
      }
    });
    window.addEventListener("resize", () => {
      if (window.innerWidth >= 1280) {
        menu.classList.remove("is-open");
        toggle.setAttribute("aria-expanded", "false");
      }
    });
  }

  async function request(url, options, bootstrap) {
    const headers = new Headers(options?.headers || {});
    headers.set("Accept", "application/json");
    headers.set("X-Requested-With", "XMLHttpRequest");
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
    if (csrf) {
      headers.set("X-CSRF-TOKEN", csrf);
    }
    if (options?.body instanceof URLSearchParams) {
      headers.set("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
    }
    const response = await fetch(url, { credentials: "same-origin", ...(options || {}), headers });
    if (response.redirected && bootstrap.ajax?.login && response.url.indexOf(bootstrap.ajax.login) === 0) {
      window.location.href = bootstrap.ajax.login;
      throw new Error("Redirecting to login.");
    }
    const payload = await response.json().catch(() => ({}));
    if (response.status === 401 && bootstrap.ajax?.login) {
      window.location.href = bootstrap.ajax.login;
      throw new Error("Redirecting to login.");
    }
    if (!response.ok) {
      throw new Error(errorMessage(payload));
    }
    return payload;
  }

  function searchMarkup(payload, bootstrap, query) {
    const tracks = Array.isArray(payload.tracks) ? payload.tracks : [];
    const artists = Array.isArray(payload.artists) ? payload.artists : [];
    const albums = Array.isArray(payload.albums) ? payload.albums : [];
    const genres = Array.isArray(payload.genres) ? payload.genres : [];
    const sections = [];

    if (tracks.length) {
      sections.push(section("Tracks", tracks.map((track) => {
        return '<a href="' + attr(track.href || ((bootstrap.ajax?.listen || "/listen") + "?track=" + encodeURIComponent(track.slug || ""))) + '" class="search-result-item">' +
          '<img src="' + attr(track.cover_image_url || defaultCover()) + '" alt="' + attr(track.title) + '" class="search-result-thumb">' +
          '<span class="min-w-0 flex-1"><span class="search-result-title">' + escape(track.title) + '</span><span class="search-result-meta">' + escape((track.artist || "Naad-e-Maan") + " / " + (track.album || track.genre || "Single")) + '</span></span>' +
          '<span class="search-result-tag">' + escape(formatTime(track.duration)) + "</span></a>";
      }).join("")));
    }

    if (artists.length) {
      sections.push(section("Artists", artists.map((artist) =>
        '<a href="' + attr(artist.href || artistUrl(bootstrap, artist.slug)) + '" class="search-result-item">' +
          '<img src="' + attr(artist.image_url || defaultCover()) + '" alt="' + attr(artist.name) + '" class="search-result-thumb search-result-thumb-round">' +
          '<span class="min-w-0 flex-1"><span class="search-result-title">' + escape(artist.name) + '</span><span class="search-result-meta">' + escape(artist.genre || "Independent") + '</span></span>' +
          '<span class="search-result-tag">Artist</span></a>'
      ).join("")));
    }

    if (albums.length) {
      sections.push(section("Albums", albums.map((album) => {
        const href = album.href || ((bootstrap.ajax?.listen || "/listen") + "?album=" + encodeURIComponent(album.slug || ""));
        return '<a href="' + attr(href) + '" class="search-result-item">' +
          '<img src="' + attr(album.cover_image_url || defaultCover()) + '" alt="' + attr(album.title) + '" class="search-result-thumb">' +
          '<span class="min-w-0 flex-1"><span class="search-result-title">' + escape(album.title) + '</span><span class="search-result-meta">' + escape((album.artist || "Naad-e-Maan") + " / " + (album.genre || "Open Format")) + '</span></span>' +
          '<span class="search-result-tag">Album</span></a>';
      }).join("")));
    }

    if (genres.length) {
      sections.push(section("Genres", genres.map((genre) =>
        '<a href="' + attr(genre.href || ((bootstrap.ajax?.listen || "/listen") + "?genre=" + encodeURIComponent(genre.name || ""))) + '" class="search-result-item">' +
          '<img src="' + attr(genre.image_url || defaultCover()) + '" alt="' + attr(genre.name) + '" class="search-result-thumb">' +
          '<span class="min-w-0 flex-1"><span class="search-result-title">' + escape(genre.name) + '</span><span class="search-result-meta">' + escape(genre.description || "Browse tracks in this sound lane.") + '</span></span>' +
          '<span class="search-result-tag">Genre</span></a>'
      ).join("")));
    }

    if (!sections.length) {
      return '<div class="search-empty-state">No matches found in the catalog.</div>';
    }

    const resultsUrl = (bootstrap.ajax?.searchPage || "/search") + "?q=" + encodeURIComponent(query || payload.query || "");
    return sections.join("") +
      '<div class="search-result-footer"><a href="' + attr(resultsUrl) + '" class="search-result-footer-link">View all results</a></div>';
  }

  function section(title, body) {
    return '<section class="search-section"><p class="search-section-title">' + escape(title) + "</p>" + body + "</section>";
  }

  function syncFavorites(songId, active) {
    document.querySelectorAll('[data-favorite-toggle][data-song-id="' + songId + '"]').forEach((button) => {
      button.setAttribute("aria-pressed", active ? "true" : "false");
      const label = button.querySelector(".favorite-label");
      if (label) {
        label.innerHTML = active ? "&#9829;" : "&#9825;";
      }
    });
  }

  function syncFollows(artistId, active) {
    document.querySelectorAll('[data-follow-toggle][data-artist-id="' + artistId + '"]').forEach((button) => {
      button.setAttribute("aria-pressed", active ? "true" : "false");
      const label = button.querySelector(".follow-label");
      if (label) {
        label.textContent = active ? "Following" : "Follow Artist";
      }
    });
  }

  function highlightTrack(track) {
    document.querySelectorAll("[data-track-card]").forEach((card) => {
      const current = normalizeTrack(json(card.getAttribute("data-track-card"), null));
      card.classList.toggle("is-active", Boolean(current && track && String(current.id) === String(track.id)));
    });
  }

  function initCounters() {
    const counters = Array.from(document.querySelectorAll("[data-counter]"));
    if (!counters.length) {
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        const element = entry.target;
        observer.unobserve(element);
        animateCounter(element);
      });
    }, { threshold: 0.45 });

    counters.forEach((counter) => observer.observe(counter));
  }

  function animateCounter(element) {
    const target = Number(element.dataset.counter || 0);
    if (!Number.isFinite(target)) {
      element.textContent = String(element.dataset.counter || "0");
      return;
    }

    const duration = 1400;
    const start = performance.now();

    const step = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      element.textContent = Math.round(target * eased).toLocaleString();

      if (progress < 1) {
        requestAnimationFrame(step);
      }
    };

    requestAnimationFrame(step);
  }

  function closeSearchPanels() {
    document.querySelectorAll("[data-search-results]").forEach((panel) => panel.classList.add("hidden"));
  }

  function paintRange(input) {
    if (!input) {
      return;
    }
    const min = Number(input.min || 0);
    const max = Number(input.max || 100);
    const value = Number(input.value || 0);
    const percent = max > min ? ((value - min) / (max - min)) * 100 : 0;
    input.style.background = "linear-gradient(90deg, rgba(59,242,255,0.95) 0%, rgba(255,79,216,0.9) " + percent + "%, rgba(255,255,255,0.12) " + percent + "%, rgba(255,255,255,0.12) 100%)";
  }

  function normalizeQueue(queue) {
    return Array.isArray(queue) ? queue.map(normalizeTrack).filter(Boolean) : [];
  }

  function normalizeTrack(track) {
    if (!track || !track.audio_url) {
      return null;
    }
    const artist = track.artist && typeof track.artist === "object"
      ? { id: track.artist.id ?? null, name: track.artist.name || "Naad-e-Maan", slug: track.artist.slug || "" }
      : track.artist ? { id: track.artist_id ?? null, name: track.artist, slug: track.artist_slug || "" } : null;
    const album = track.album && typeof track.album === "object"
      ? { id: track.album.id ?? null, title: track.album.title || "Single release", slug: track.album.slug || "" }
      : track.album_title ? { id: track.album_id ?? null, title: track.album_title, slug: "" } : null;
    return {
      id: track.id ?? null,
      title: track.title || "Untitled Track",
      slug: track.slug || "",
      genre: track.genre || "Open Format",
      duration: Number(track.duration) || 0,
      audio_url: track.audio_url,
      cover_image_url: track.cover_image_url || defaultCover(),
      streams_count: Number(track.streams_count) || 0,
      artist,
      album,
    };
  }

  function mergePlaylist(playlists, playlist) {
    const normalized = normalizePlaylist(playlist);
    const index = playlists.findIndex((entry) => String(entry.id) === String(normalized.id));
    if (index >= 0) {
      playlists.splice(index, 1, normalized);
    } else {
      playlists.push(normalized);
    }
  }

  function normalizePlaylist(playlist) {
    return { id: playlist.id, name: playlist.name || "Untitled Playlist", slug: playlist.slug || "" };
  }

  function artistUrl(bootstrap, slug) {
    return (bootstrap.ajax?.artists || "/artists").replace(/\/$/, "") + "/" + encodeURIComponent(slug || "");
  }

  function errorMessage(payload) {
    if (payload?.message) {
      return payload.message;
    }
    if (payload?.errors) {
      const first = Object.values(payload.errors).flat()[0];
      if (first) {
        return String(first);
      }
    }
    return "Request failed.";
  }

  function formatTime(value) {
    const total = Math.max(0, Math.floor(Number(value) || 0));
    return Math.floor(total / 60) + ":" + String(total % 60).padStart(2, "0");
  }

  function defaultCover() {
    return "https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&w=400&q=80";
  }

  function clamp(value, min, max) {
    return Math.min(Math.max(Number(value) || 0, min), max);
  }

  function json(value, fallback) {
    try {
      return value ? JSON.parse(value) : fallback;
    } catch (error) {
      return fallback;
    }
  }

  function escape(value) {
    return String(value ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function attr(value) {
    return escape(value);
  }
})();
