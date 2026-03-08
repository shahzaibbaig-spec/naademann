@extends('layouts.app')

@section('title', 'Naad-e-Maan | Settings')

@php
    $setting = fn (string $key, string $fallback = '') => old('settings.'.$key, optional($settings->get($key))->value ?? $fallback);
@endphp

@section('content')
  <section class="mx-auto max-w-6xl px-6 py-16 lg:px-8">
    @include('admin._nav')

    <div class="glass-card rounded-[2rem] border border-white/10 p-6 lg:p-8">
      <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
          <p class="section-kicker">Site Settings</p>
          <h1 class="mt-3 font-display text-4xl font-semibold">Brand, video, footer, and social controls</h1>
        </div>
        <p class="text-sm text-white/45">Changes update shared brand content and featured video embeds across the site.</p>
      </div>

      <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-8 grid gap-8">
        @csrf
        @method('PUT')

        <section class="rounded-[1.75rem] border border-white/10 bg-slate-950/45 p-6">
          <p class="section-kicker">Brand Settings</p>
          <div class="mt-6 grid gap-4 md:grid-cols-2">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Logo Text</span>
              <input name="settings[logo_text]" type="text" value="{{ $setting('logo_text', 'Naad-e-Maan') }}" class="w-full bg-transparent text-white outline-none">
            </label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Tagline</span>
              <input name="settings[platform_tagline]" type="text" value="{{ $setting('platform_tagline', 'The Sound of the Soul') }}" class="w-full bg-transparent text-white outline-none">
            </label>
          </div>
        </section>

        <section class="rounded-[1.75rem] border border-white/10 bg-slate-950/45 p-6">
          <p class="section-kicker">Video Settings</p>
          <p class="mt-3 text-sm text-white/45">Paste either a full YouTube URL or the 11-character video ID.</p>
          <div class="mt-6 grid gap-4 md:grid-cols-2">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Homepage Spotlight Video</span>
              <input name="settings[homepage_video]" type="text" value="{{ $setting('homepage_video', 'ScMzIvxBSi4') }}" class="w-full bg-transparent text-white outline-none">
            </label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Artist Default Video</span>
              <input name="settings[artist_default_video]" type="text" value="{{ $setting('artist_default_video', 'ScMzIvxBSi4') }}" class="w-full bg-transparent text-white outline-none">
            </label>
          </div>
        </section>

        <section class="rounded-[1.75rem] border border-white/10 bg-slate-950/45 p-6">
          <p class="section-kicker">Footer Settings</p>
          <div class="mt-6 grid gap-4">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Footer Text</span>
              <textarea name="settings[footer_text]" rows="4" class="w-full resize-none bg-transparent text-white outline-none">{{ $setting('footer_text', 'Immersive music discovery, creator publishing, and admin-ready platform control.') }}</textarea>
            </label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Support Email</span>
              <input name="settings[support_email]" type="email" value="{{ $setting('support_email', 'support@naademaan.test') }}" class="w-full bg-transparent text-white outline-none">
            </label>
          </div>
        </section>

        <section class="rounded-[1.75rem] border border-white/10 bg-slate-950/45 p-6">
          <p class="section-kicker">Social Links</p>
          <div class="mt-6 grid gap-4 md:grid-cols-3">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Instagram</span>
              <input name="settings[social_instagram]" type="url" value="{{ $setting('social_instagram', 'https://instagram.com/naademaan') }}" class="w-full bg-transparent text-white outline-none">
            </label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">YouTube</span>
              <input name="settings[social_youtube]" type="url" value="{{ $setting('social_youtube', 'https://youtube.com/@naademaan') }}" class="w-full bg-transparent text-white outline-none">
            </label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">SoundCloud</span>
              <input name="settings[social_soundcloud]" type="url" value="{{ $setting('social_soundcloud', 'https://soundcloud.com/naademaan') }}" class="w-full bg-transparent text-white outline-none">
            </label>
          </div>
        </section>

        <div class="flex justify-end">
          <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Save Settings</button>
        </div>
      </form>
    </div>
  </section>
@endsection
