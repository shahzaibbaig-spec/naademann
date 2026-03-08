@extends('layouts.app')

@section('title', 'Naad-e-Maan | Creator Dashboard')

@php
    $trackStatusMeta = [
        'draft' => ['label' => 'Draft', 'classes' => 'border-white/10 bg-white/5 text-white/70'],
        'pending' => ['label' => 'Pending', 'classes' => 'border-neon-blue/20 bg-neon-blue/10 text-neon-blue'],
        'approved' => ['label' => 'Published', 'classes' => 'border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan'],
        'rejected' => ['label' => 'Rejected', 'classes' => 'border-neon-pink/20 bg-neon-pink/10 text-neon-pink'],
    ];
@endphp

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    @include('creator._nav')

    @if ($errors->any())
      <div class="mb-8 rounded-[1.75rem] border border-neon-pink/20 bg-neon-pink/10 px-6 py-5 text-sm text-white/80">
        <p class="font-semibold text-neon-pink">The track could not be saved.</p>
        <ul class="mt-3 grid gap-1 text-white/70">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="glass-card rounded-[2rem] border border-white/10 p-8">
      <p class="section-kicker">Creator Dashboard</p>
      <h1 class="mt-4 font-display text-5xl font-semibold">{{ $artist->name }}</h1>
      <p class="mt-4 max-w-3xl text-base leading-8 text-white/65">Upload new releases, save ideas as drafts, publish tracks to your public catalog, and manage every release from one workspace.</p>
    </div>

    <div class="mt-10 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
      <x-stats-card label="Monthly Listeners" :value="number_format($analytics['monthly_listeners'])" description="Listeners currently tuned into your catalog." />
      <x-stats-card label="Followers" :value="number_format($analytics['followers'])" accent="pink" description="Fans following your artist profile." />
      <x-stats-card label="Streams" :value="number_format($analytics['streams'])" accent="blue" description="Total streams across approved tracks." />
      <x-stats-card label="Tracks" :value="number_format($analytics['tracks'])" description="Songs currently saved in your artist vault." />
      <x-stats-card label="Albums" :value="number_format($analytics['albums'])" accent="pink" description="Album releases attached to your artist profile." />
      <x-stats-card label="Favorites" :value="number_format($analytics['favorites'])" accent="blue" description="How many times listeners saved your songs." />
    </div>

    <div class="mt-10 grid gap-8 xl:grid-cols-[1.2fr_0.8fr]">
      <section class="glass-card rounded-[2rem] border border-white/10 p-6 lg:p-7">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <p class="section-kicker">Upload Track</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Creator release module</h2>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-white/55">MP3 files are stored on Laravel's public storage disk, slugs are generated automatically, and the publish toggle decides whether the release goes live now or stays as a draft.</p>
          </div>
          <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/50 px-4 py-3 text-sm text-white/55">
            <p>Accepted audio: <span class="text-white/80">MP3</span></p>
            <p class="mt-1">Max upload size: <span class="text-white/80">20 MB</span></p>
          </div>
        </div>

        <form method="POST" action="{{ route('creator.tracks.store') }}" enctype="multipart/form-data" class="mt-8 grid gap-4">
          @csrf

          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Track Title</span>
            <input name="title" type="text" value="{{ old('title') }}" class="w-full bg-transparent text-white outline-none" required>
          </label>

          <div class="grid gap-4 md:grid-cols-2">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Genre</span>
              <select name="genre" class="w-full bg-transparent text-white outline-none" required>
                @foreach ($genres as $genre)
                  <option value="{{ $genre->name }}" @selected(old('genre', $artist->genre) === $genre->name) class="bg-slate-950">{{ $genre->name }}</option>
                @endforeach
                @if (!$genres->contains(fn ($genre) => $genre->name === old('genre', $artist->genre)))
                  <option value="{{ old('genre', $artist->genre) }}" selected class="bg-slate-950">{{ old('genre', $artist->genre) }}</option>
                @endif
              </select>
            </label>

            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Album</span>
              <select name="album_id" class="w-full bg-transparent text-white outline-none">
                <option value="" class="bg-slate-950">Single release</option>
                @foreach ($albums as $album)
                  <option value="{{ $album->id }}" @selected((string) old('album_id') === (string) $album->id) class="bg-slate-950">{{ $album->title }}</option>
                @endforeach
              </select>
            </label>
          </div>

          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Description</span>
            <textarea name="description" rows="4" class="w-full resize-none bg-transparent text-white outline-none" placeholder="Describe the mood, story, or sonic identity of the track.">{{ old('description') }}</textarea>
          </label>

          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Lyrics</span>
            <textarea name="lyrics" rows="7" class="w-full resize-none bg-transparent text-white outline-none" placeholder="Optional full lyrics for your release.">{{ old('lyrics') }}</textarea>
          </label>

          <div class="grid gap-4 md:grid-cols-2">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Release Date</span>
              <input name="release_date" type="date" value="{{ old('release_date', now()->toDateString()) }}" class="w-full bg-transparent text-white outline-none">
            </label>

            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Cover Image</span>
              <input name="cover_image" type="file" accept="image/*" class="w-full text-sm text-white/70">
            </label>
          </div>

          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Audio File</span>
            <input name="audio_file" type="file" accept=".mp3,audio/mpeg" class="w-full text-sm text-white/70" required>
          </label>

          <label class="glass-card flex items-center justify-between gap-4 rounded-[1.5rem] border border-white/10 p-4">
            <div>
              <span class="block text-sm font-medium text-white">Publish now</span>
              <span class="mt-1 block text-xs leading-6 text-white/50">Checked releases go live immediately. Leave unchecked to save as a draft in your tracks table.</span>
            </div>
            <input name="publish_now" type="checkbox" value="1" @checked(old('publish_now')) class="h-5 w-5 rounded border-white/20 bg-slate-950/70 text-neon-cyan focus:ring-neon-cyan">
          </label>

          <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Save Track</button>
            <p class="text-sm text-white/45">Drafts remain private. Published tracks are added to your catalog table immediately.</p>
          </div>
        </form>
      </section>

      <section class="space-y-8">
        <div class="glass-card rounded-[2rem] border border-white/10 p-6">
          <p class="section-kicker">Create Album</p>
          <form method="POST" action="{{ route('creator.albums.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4">
            @csrf
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Album Title</span><input name="title" type="text" class="w-full bg-transparent text-white outline-none" required></label>
            <div class="grid gap-4 md:grid-cols-2">
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
                <span class="mb-2 block text-sm text-white/50">Genre</span>
                <select name="genre" class="w-full bg-transparent text-white outline-none" required>
                  @foreach ($genres as $genre)
                    <option value="{{ $genre->name }}" @selected(old('genre', $artist->genre) === $genre->name) class="bg-slate-950">{{ $genre->name }}</option>
                  @endforeach
                </select>
              </label>
              <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Release Date</span><input name="release_date" type="date" class="w-full bg-transparent text-white outline-none"></label>
            </div>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Description</span><textarea name="description" rows="4" class="w-full resize-none bg-transparent text-white outline-none"></textarea></label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Cover Image</span><input name="cover_image" type="file" accept="image/*" class="w-full text-sm text-white/70"></label>
            <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Create Album</button>
          </form>
        </div>

        <div class="glass-card rounded-[2rem] border border-white/10 p-6">
          <p class="section-kicker">Profile Settings</p>
          <form method="POST" action="{{ route('creator.profile.update') }}" enctype="multipart/form-data" class="mt-6 grid gap-4">
            @csrf
            @method('PUT')
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Display Name</span><input name="name" type="text" value="{{ old('name', auth()->user()->name) }}" class="w-full bg-transparent text-white outline-none"></label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Email</span><input name="email" type="email" value="{{ old('email', auth()->user()->email) }}" class="w-full bg-transparent text-white outline-none"></label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Headline</span><input name="headline" type="text" value="{{ old('headline', auth()->user()->headline) }}" class="w-full bg-transparent text-white outline-none"></label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Genre</span>
              <select name="genre" class="w-full bg-transparent text-white outline-none">
                @foreach ($genres as $genre)
                  <option value="{{ $genre->name }}" @selected(old('genre', $artist->genre) === $genre->name) class="bg-slate-950">{{ $genre->name }}</option>
                @endforeach
              </select>
            </label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Bio</span><textarea name="bio" rows="4" class="w-full resize-none bg-transparent text-white outline-none">{{ old('bio', auth()->user()->bio ?: $artist->bio) }}</textarea></label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Avatar</span><input name="avatar" type="file" accept="image/*" class="w-full text-sm text-white/70"></label>
            <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Save Profile</button>
          </form>
        </div>
      </section>
    </div>

    <div class="mt-10 grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
      <section class="glass-card rounded-[2rem] border border-white/10 p-6">
        <div class="flex items-end justify-between gap-4">
          <div>
            <p class="section-kicker">Manage Tracks</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Your releases</h2>
          </div>
          <p class="text-sm text-white/45">{{ number_format($tracks->count()) }} tracks total</p>
        </div>
        <div class="mt-6 overflow-x-auto">
          <table class="w-full min-w-[860px] text-left text-sm">
            <thead class="text-white/45">
              <tr>
                <th class="pb-4">Track</th>
                <th class="pb-4">Album</th>
                <th class="pb-4">Release Date</th>
                <th class="pb-4">Status</th>
                <th class="pb-4">Streams</th>
                <th class="pb-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
              @forelse ($tracks as $track)
                @php
                    $status = $trackStatusMeta[$track->moderation_status] ?? ['label' => ucfirst($track->moderation_status), 'classes' => 'border-white/10 bg-white/5 text-white/70'];
                    $releaseDate = $track->release_date ?: $track->album?->release_date;
                @endphp
                <tr>
                  <td class="py-4">
                    <div class="font-medium">{{ $track->title }}</div>
                    <div class="mt-1 text-white/45">{{ $track->genre }}</div>
                    @if ($track->description)
                      <div class="mt-2 max-w-xs truncate text-xs text-white/35">{{ $track->description }}</div>
                    @endif
                  </td>
                  <td class="py-4 text-white/60">{{ $track->album?->title ?? 'Single' }}</td>
                  <td class="py-4 text-white/60">{{ $releaseDate ? $releaseDate->format('M d, Y') : 'TBA' }}</td>
                  <td class="py-4">
                    <span class="rounded-full border px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $status['classes'] }}">{{ $status['label'] }}</span>
                  </td>
                  <td class="py-4 text-white/60">{{ number_format($track->streams_count) }}</td>
                  <td class="py-4 text-right">
                    <form method="POST" action="{{ route('creator.tracks.destroy', $track) }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-sm text-neon-pink">Delete</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="py-10 text-center text-white/50">No tracks uploaded yet. Use the release module above to add your first song.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>

      <section class="glass-card rounded-[2rem] border border-white/10 p-6">
        <p class="section-kicker">Manage Albums</p>
        <div class="mt-6 space-y-4">
          @forelse ($albums as $album)
            <article class="flex items-center gap-4 rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-4">
              <img src="{{ $album->cover_image_url }}" alt="{{ $album->title }}" class="h-16 w-16 rounded-[1rem] object-cover">
              <div class="min-w-0 flex-1">
                <p class="font-semibold">{{ $album->title }}</p>
                <p class="text-sm text-white/50">{{ $album->genre }} - {{ optional($album->release_date)->format('M d, Y') ?? 'TBA' }}</p>
              </div>
              <form method="POST" action="{{ route('creator.albums.destroy', $album) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-neon-pink">Delete</button>
              </form>
            </article>
          @empty
            <div class="rounded-[1.5rem] border border-dashed border-white/10 bg-slate-950/45 px-4 py-8 text-center text-sm text-white/45">
              No albums yet. Create one to group future uploads.
            </div>
          @endforelse
        </div>
      </section>
    </div>
  </section>
@endsection
