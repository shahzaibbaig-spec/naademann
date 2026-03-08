@props(['platformSettings' => collect()])

@php
    $homeHref = route('home');
    $genresHref = route('home').'#genres';
    $videosHref = route('home').'#videos';
    $creatorsHref = route('home').'#creators';
    $contactHref = route('home').'#contact';
    $creatorStudioHref = auth()->check() && auth()->user()->isCreator() ? route('creator.dashboard') : route('register');
    $logoText = $platformSettings['logo_text'] ?? 'Naad-e-Maan';
@endphp

<header class="sticky top-0 z-50 border-b border-white/10 bg-slate-950/80 backdrop-blur-2xl">
  <div class="mx-auto max-w-7xl px-6 py-4 lg:px-8">
    <div class="hidden items-center gap-6 xl:grid xl:grid-cols-[1fr_auto_1fr]">
      <nav class="flex items-center gap-2">
        <a href="{{ $homeHref }}" class="pill-nav {{ request()->routeIs('home') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Home</a>
        <a href="{{ route('artists.index') }}" class="pill-nav {{ request()->routeIs('artists.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Artists</a>
        <a href="{{ $genresHref }}" class="pill-nav rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Genres</a>
      </nav>

      <a href="{{ $homeHref }}" class="justify-self-center text-center">
        <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-2xl border border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan shadow-[0_0_26px_rgba(59,242,255,0.18)]">
          <svg viewBox="0 0 48 48" class="h-6 w-6" fill="none" aria-hidden="true">
            <path d="M8 26h4l3-10 5 18 4-11 4 7h4l3-12 5 8h4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" />
          </svg>
        </span>
        <span class="brand-title mt-3 block text-2xl font-semibold tracking-[0.24em]">{{ $logoText }}</span>
        <span class="mt-1 block text-[0.65rem] uppercase tracking-[0.42em] text-white/40">{{ $platformSettings['platform_tagline'] ?? 'The Sound of the Soul' }}</span>
      </a>

      <nav class="flex items-center justify-end gap-2">
        <a href="{{ $videosHref }}" class="pill-nav rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Videos</a>
        <a href="{{ $creatorsHref }}" class="pill-nav rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Creators</a>
        <a href="{{ $contactHref }}" class="pill-nav rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Contact</a>
      </nav>
    </div>

    <div class="flex items-center justify-between gap-4 xl:hidden">
      <a href="{{ $homeHref }}" class="text-center">
        <span class="brand-title block text-xl font-semibold tracking-[0.22em]">{{ $logoText }}</span>
        <span class="mt-1 block text-[0.62rem] uppercase tracking-[0.35em] text-white/45">{{ $platformSettings['platform_tagline'] ?? 'The Sound of the Soul' }}</span>
      </a>

      <button type="button" data-mobile-menu-toggle class="glass-card inline-flex h-12 w-12 items-center justify-center rounded-2xl border border-white/10 text-white" aria-label="Toggle navigation">
        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <path d="M4 7h16" stroke-linecap="round" />
          <path d="M4 12h16" stroke-linecap="round" />
          <path d="M4 17h16" stroke-linecap="round" />
        </svg>
      </button>
    </div>

    <div class="mt-5 hidden items-center justify-between gap-6 border-t border-white/10 pt-5 xl:flex">
      <div data-search-root class="relative w-full max-w-md">
        <form action="{{ route('search.index') }}" method="GET">
          <label class="glass-card flex items-center gap-3 rounded-full border border-white/10 px-4 py-3">
            <svg viewBox="0 0 24 24" class="h-4 w-4 text-white/50" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
              <path d="m21 21-4.35-4.35" />
              <circle cx="11" cy="11" r="6" />
            </svg>
            <input data-global-search name="q" value="{{ request('q') }}" type="search" autocomplete="off" placeholder="Search artists, tracks, albums, or genres" class="w-full bg-transparent text-sm text-white outline-none placeholder:text-white/30">
          </label>
        </form>
        <div data-search-results class="search-results hidden absolute left-0 right-0 top-[calc(100%+12px)] overflow-hidden rounded-[1.5rem] border border-white/10 bg-slate-950/95 shadow-[0_20px_65px_rgba(3,7,18,0.6)] backdrop-blur-2xl"></div>
      </div>

      <div class="flex items-center gap-3">
        @auth
          @if (auth()->user()->isCreator() || auth()->user()->isAdmin())
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('creator.dashboard') }}" class="rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-sm font-medium text-neon-cyan">Studio</a>
          @endif
          <div class="glass-card flex items-center gap-3 rounded-full border border-white/10 px-4 py-2">
            <img src="{{ auth()->user()->avatar_url ?: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=240&q=80' }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-full object-cover">
            <div>
              <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
              <p class="text-xs text-white/45">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="pill-nav rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Login</a>
          <a href="{{ $creatorStudioHref }}" class="rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-950">Creator Studio</a>
        @endauth
      </div>
    </div>
  </div>

  <div data-mobile-menu class="mobile-menu border-t border-white/10 xl:hidden">
    <div class="mx-auto max-w-7xl px-6 py-5 lg:px-8">
      <div class="grid gap-4">
        <div data-search-root class="relative">
          <form action="{{ route('search.index') }}" method="GET">
            <label class="glass-card flex items-center gap-3 rounded-full border border-white/10 px-4 py-3">
              <svg viewBox="0 0 24 24" class="h-4 w-4 text-white/50" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="m21 21-4.35-4.35" />
                <circle cx="11" cy="11" r="6" />
              </svg>
              <input data-global-search name="q" value="{{ request('q') }}" type="search" autocomplete="off" placeholder="Search artists, tracks, albums, or genres" class="w-full bg-transparent text-sm text-white outline-none placeholder:text-white/30">
            </label>
          </form>
          <div data-search-results class="search-results hidden absolute inset-x-0 top-[calc(100%+12px)] overflow-hidden rounded-[1.5rem] border border-white/10 bg-slate-950/95 shadow-[0_20px_65px_rgba(3,7,18,0.6)] backdrop-blur-2xl"></div>
        </div>

        <nav class="grid gap-2">
          <a href="{{ $homeHref }}" class="pill-nav {{ request()->routeIs('home') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-3 text-sm text-white/70">Home</a>
          <a href="{{ route('artists.index') }}" class="pill-nav {{ request()->routeIs('artists.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-3 text-sm text-white/70">Artists</a>
          <a href="{{ $genresHref }}" class="pill-nav rounded-full border border-white/10 px-4 py-3 text-sm text-white/70">Genres</a>
          <a href="{{ $videosHref }}" class="pill-nav rounded-full border border-white/10 px-4 py-3 text-sm text-white/70">Videos</a>
          <a href="{{ $creatorsHref }}" class="pill-nav rounded-full border border-white/10 px-4 py-3 text-sm text-white/70">Creators</a>
          <a href="{{ $contactHref }}" class="pill-nav rounded-full border border-white/10 px-4 py-3 text-sm text-white/70">Contact</a>
        </nav>

        @auth
          <div class="glass-card flex items-center gap-3 rounded-[1.5rem] border border-white/10 px-4 py-4">
            <img src="{{ auth()->user()->avatar_url ?: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=240&q=80' }}" alt="{{ auth()->user()->name }}" class="h-10 w-10 rounded-full object-cover">
            <div class="min-w-0">
              <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
              <p class="text-xs text-white/45">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
            @if (auth()->user()->isCreator() || auth()->user()->isAdmin())
              <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('creator.dashboard') }}" class="ml-auto rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-sm font-medium text-neon-cyan">Studio</a>
            @endif
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-full bg-white px-4 py-3 text-sm font-semibold text-slate-950">Logout</button>
          </form>
        @else
          <div class="grid gap-2 sm:grid-cols-2">
            <a href="{{ route('login') }}" class="pill-nav rounded-full border border-white/10 px-4 py-3 text-center text-sm text-white/70">Login</a>
            <a href="{{ $creatorStudioHref }}" class="rounded-full bg-white px-4 py-3 text-center text-sm font-semibold text-slate-950">Creator Studio</a>
          </div>
        @endauth
      </div>
    </div>
  </div>
</header>
