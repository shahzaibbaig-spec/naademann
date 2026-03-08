<div class="mb-8 rounded-[1.75rem] border border-white/10 bg-slate-950/55 p-3 backdrop-blur-xl">
  <div class="flex flex-wrap gap-3">
    <a href="{{ route('admin.dashboard') }}" class="pill-nav {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Dashboard</a>
    <a href="{{ route('admin.users.index') }}" class="pill-nav {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Users</a>
    <a href="{{ route('admin.artists.index') }}" class="pill-nav {{ request()->routeIs('admin.artists.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Artists</a>
    <a href="{{ route('admin.uploads.index') }}" class="pill-nav {{ request()->routeIs('admin.uploads.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Uploads</a>
    <a href="{{ route('admin.moderation.index') }}" class="pill-nav {{ request()->routeIs('admin.moderation.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Moderation</a>
    <a href="{{ route('admin.genres.index') }}" class="pill-nav {{ request()->routeIs('admin.genres.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Genres</a>
    <a href="{{ route('admin.banners.index') }}" class="pill-nav {{ request()->routeIs('admin.banners.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Banners</a>
    <a href="{{ route('admin.settings.index') }}" class="pill-nav {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">Settings</a>
  </div>
</div>
