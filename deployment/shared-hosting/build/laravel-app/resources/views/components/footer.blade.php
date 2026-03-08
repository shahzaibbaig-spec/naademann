@props(['platformSettings' => collect()])

@php
    $socialLinks = [
        'Instagram' => $platformSettings['social_instagram'] ?? null,
        'YouTube' => $platformSettings['social_youtube'] ?? null,
        'SoundCloud' => $platformSettings['social_soundcloud'] ?? null,
    ];
@endphp

<footer id="contact" class="relative mt-24 overflow-hidden border-t border-white/10 bg-slate-950/75">
  <div class="footer-glow-strip"></div>
  <div class="mx-auto max-w-7xl px-6 py-14 lg:px-8">
    <div class="grid gap-10 lg:grid-cols-[1.15fr_0.85fr_0.85fr]">
      <div class="glass-card rounded-[2rem] border border-white/10 p-8">
        <div class="flex items-center gap-5">
          <div class="footer-mic-shell flex h-20 w-20 items-center justify-center rounded-[1.75rem] border border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan">
            <svg viewBox="0 0 48 48" class="h-10 w-10" fill="none" aria-hidden="true">
              <rect x="17" y="8" width="14" height="22" rx="7" stroke="currentColor" stroke-width="3" />
              <path d="M12 23c0 6.627 5.373 12 12 12s12-5.373 12-12" stroke="currentColor" stroke-linecap="round" stroke-width="3" />
              <path d="M24 35v7" stroke="currentColor" stroke-linecap="round" stroke-width="3" />
              <path d="M17 42h14" stroke="currentColor" stroke-linecap="round" stroke-width="3" />
            </svg>
          </div>
          <div>
            <p class="brand-title text-2xl font-semibold tracking-[0.2em]">{{ $platformSettings['logo_text'] ?? 'Naad-e-Maan' }}</p>
            <p class="mt-2 text-sm uppercase tracking-[0.32em] text-white/45">{{ $platformSettings['platform_tagline'] ?? 'The Sound of the Soul' }}</p>
          </div>
        </div>
        <p class="mt-6 max-w-xl text-base leading-8 text-white/60">{{ $platformSettings['footer_text'] ?? 'Immersive music discovery, creator publishing, and admin-ready platform control.' }}</p>
      </div>

      <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
        <p class="section-kicker">Explore</p>
        <div class="mt-6 grid gap-3 text-sm text-white/60">
          <a href="{{ route('home') }}" class="transition hover:text-neon-cyan">Home</a>
          <a href="{{ route('artists.index') }}" class="transition hover:text-neon-cyan">Artists</a>
          <a href="{{ route('home').'#genres' }}" class="transition hover:text-neon-cyan">Genres</a>
          <a href="{{ route('home').'#videos' }}" class="transition hover:text-neon-cyan">Videos</a>
          <a href="{{ route('home').'#creators' }}" class="transition hover:text-neon-cyan">Creators</a>
        </div>
      </div>

      <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
        <p class="section-kicker">Contact</p>
        <div class="mt-6 space-y-3 text-sm text-white/60">
          <p>{{ $platformSettings['support_email'] ?? 'support@naademaan.test' }}</p>
          <p>Mon - Sat / 7PM - 2AM streaming desk</p>
          <p class="text-white/45">Follow the glow and bring your next release to the platform.</p>
        </div>
        <div class="mt-6 flex flex-wrap gap-3 text-sm text-white/55">
          @foreach ($socialLinks as $label => $href)
            <a href="{{ $href ?: '#' }}" @if($href) target="_blank" rel="noreferrer" @endif class="rounded-full border border-white/10 px-4 py-2 transition hover:border-neon-cyan/30 hover:text-neon-cyan">{{ $label }}</a>
          @endforeach
        </div>
      </div>
    </div>

    <div class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 text-sm text-white/45 sm:flex-row sm:items-center sm:justify-between">
      <p>&copy; 2026 Naad-e-Maan. All rights reserved.</p>
      <div class="flex flex-wrap gap-4">
        <a href="#" class="transition hover:text-neon-cyan">Terms</a>
        <a href="#" class="transition hover:text-neon-cyan">Privacy</a>
        <a href="{{ route('home').'#contact' }}" class="transition hover:text-neon-cyan">Contact</a>
      </div>
    </div>
  </div>
</footer>
