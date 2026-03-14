@extends('layouts.app')

@section('title', 'Naad-e-Maan | Admin Artists')

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    @include('admin._nav')

    <div class="grid gap-5 md:grid-cols-3">
      <x-stats-card label="Artist Profiles" :value="number_format($artistMetrics['total'])" description="Total artist records available to manage." />
      <x-stats-card label="Creator-Owned" :value="number_format($artistMetrics['creator_owned'])" accent="pink" description="Profiles linked to creator or admin accounts." />
      <x-stats-card label="Guest Profiles" :value="number_format($artistMetrics['guest_profiles'])" accent="blue" description="Standalone artist entries without an owner account." />
    </div>

    <div class="mt-8">
      <div class="mb-6 flex items-end justify-between gap-4">
        <div>
          <p class="section-kicker">Manage Creators And Artist Profiles</p>
          <h1 class="mt-3 font-display text-4xl font-semibold">Artist profile manager</h1>
        </div>
        <p class="text-sm text-white/45">{{ number_format($artists->total()) }} artists</p>
      </div>

      @can('create', \App\Models\Artist::class)
        <form method="POST" action="{{ route('admin.artists.store') }}" enctype="multipart/form-data" class="mb-6 grid gap-4 rounded-[1.8rem] border border-neon-pink/20 bg-slate-950/35 p-5 md:grid-cols-2">
          @csrf
          <label class="glass-card rounded-[1.2rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Stage Name</span>
            <input name="name" type="text" required class="w-full bg-transparent text-white outline-none">
          </label>
          <label class="glass-card rounded-[1.2rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Genre</span>
            <input name="genre" type="text" required class="w-full bg-transparent text-white outline-none">
          </label>
          <label class="glass-card rounded-[1.2rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Monthly Listeners</span>
            <input name="monthly_listeners" type="number" min="0" value="0" required class="w-full bg-transparent text-white outline-none">
          </label>
          <label class="glass-card rounded-[1.2rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Followers</span>
            <input name="followers" type="number" min="0" value="0" required class="w-full bg-transparent text-white outline-none">
          </label>
          <label class="glass-card rounded-[1.2rem] border border-white/10 p-4 md:col-span-2">
            <span class="mb-2 block text-sm text-white/50">Linked Creator</span>
            <select name="user_id" class="w-full bg-transparent text-white outline-none">
              <option value="" class="bg-slate-950">No linked creator</option>
              @foreach ($creatorUsers as $creator)
                <option value="{{ $creator->id }}" class="bg-slate-950">{{ $creator->name }} / {{ $creator->email }} / {{ $creator->role }}</option>
              @endforeach
            </select>
          </label>
          <label class="glass-card rounded-[1.2rem] border border-white/10 p-4 md:col-span-2">
            <span class="mb-2 block text-sm text-white/50">Profile Image URL</span>
            <input name="image_url" type="text" placeholder="https://... or /storage/uploads/artists/..." class="w-full bg-transparent text-white outline-none">
          </label>
          <label class="glass-card rounded-[1.2rem] border border-white/10 p-4 md:col-span-2">
            <span class="mb-2 block text-sm text-white/50">Or Upload New Image</span>
            <input name="image_file" type="file" accept="image/*" class="w-full text-sm text-white/70">
          </label>
          <label class="glass-card rounded-[1.2rem] border border-white/10 p-4 md:col-span-2">
            <span class="mb-2 block text-sm text-white/50">Bio</span>
            <textarea name="bio" rows="3" class="w-full resize-none bg-transparent text-white outline-none"></textarea>
          </label>
          <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="rounded-full border border-neon-pink/30 bg-neon-pink/15 px-5 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-neon-pink">Create Artist</button>
          </div>
        </form>
      @endcan

      <div class="grid gap-6 xl:grid-cols-2">
        @foreach ($artists as $artist)
          <form method="POST" action="{{ route('admin.artists.update', $artist) }}" enctype="multipart/form-data" class="glass-card rounded-[2rem] border border-white/10 p-6">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-5 sm:flex-row">
              <img src="{{ $artist->image_url }}" alt="{{ $artist->name }}" class="h-28 w-28 rounded-[1.5rem] object-cover">
              <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-3">
                  <p class="text-lg font-semibold">{{ $artist->name }}</p>
                  <span class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.2em] text-white/65">{{ $artist->songs_count }} songs</span>
                </div>
                <p class="mt-2 text-sm text-white/50">{{ $artist->user?->email ?? 'Guest artist profile' }}</p>
                <p class="mt-3 text-sm text-white/40">Update linked creator ownership, profile copy, and visible platform stats from one card.</p>
              </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
                <span class="mb-2 block text-sm text-white/50">Stage Name</span>
                <input name="name" type="text" value="{{ $artist->name }}" class="w-full bg-transparent text-white outline-none">
              </label>
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
                <span class="mb-2 block text-sm text-white/50">Genre</span>
                <input name="genre" type="text" value="{{ $artist->genre }}" class="w-full bg-transparent text-white outline-none">
              </label>
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4 md:col-span-2">
                <span class="mb-2 block text-sm text-white/50">Linked Creator</span>
                <select name="user_id" class="w-full bg-transparent text-white outline-none">
                  <option value="" class="bg-slate-950">No linked creator</option>
                  @foreach ($creatorUsers as $creator)
                    <option value="{{ $creator->id }}" @selected((string) $artist->user_id === (string) $creator->id) class="bg-slate-950">{{ $creator->name }} / {{ $creator->email }} / {{ $creator->role }}</option>
                  @endforeach
                </select>
              </label>
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
                <span class="mb-2 block text-sm text-white/50">Monthly Listeners</span>
                <input name="monthly_listeners" type="number" min="0" value="{{ $artist->monthly_listeners }}" class="w-full bg-transparent text-white outline-none">
              </label>
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
                <span class="mb-2 block text-sm text-white/50">Followers</span>
                <input name="followers" type="number" min="0" value="{{ $artist->followers }}" class="w-full bg-transparent text-white outline-none">
              </label>
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4 md:col-span-2">
                <span class="mb-2 block text-sm text-white/50">Profile Image URL</span>
                <input name="image_url" type="text" value="{{ $artist->image_url }}" placeholder="https://... or /storage/uploads/artists/..." class="w-full bg-transparent text-white outline-none">
              </label>
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4 md:col-span-2">
                <span class="mb-2 block text-sm text-white/50">Or Upload New Image</span>
                <input name="image_file" type="file" accept="image/*" class="w-full text-sm text-white/70">
              </label>
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4 md:col-span-2">
                <span class="mb-2 block text-sm text-white/50">Bio</span>
                <textarea name="bio" rows="4" class="w-full resize-none bg-transparent text-white outline-none">{{ $artist->bio }}</textarea>
              </label>
            </div>

            <div class="mt-5 flex justify-end gap-3">
              @can('delete', $artist)
                <button type="submit" form="delete-artist-{{ $artist->id }}" class="rounded-full border border-rose-400/30 bg-rose-500/10 px-5 py-3 text-sm font-semibold text-rose-300">Delete</button>
              @endcan
              <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Save Profile</button>
            </div>
          </form>
          @can('delete', $artist)
            <form id="delete-artist-{{ $artist->id }}" method="POST" action="{{ route('admin.artists.destroy', $artist) }}" class="hidden" onsubmit="return confirm('Delete this artist profile?');">
              @csrf
              @method('DELETE')
            </form>
          @endcan
        @endforeach
      </div>

      <div class="mt-8">{{ $artists->links() }}</div>
    </div>
  </section>
@endsection
