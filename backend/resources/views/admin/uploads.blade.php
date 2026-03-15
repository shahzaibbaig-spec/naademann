@extends('layouts.app')

@section('title', 'Naad-e-Maan | Admin Uploads')

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
    @include('admin._nav')

    @if ($errors->any())
      <div class="mb-8 rounded-[1.75rem] border border-neon-pink/20 bg-neon-pink/10 px-6 py-5 text-sm text-white/80">
        <p class="font-semibold text-neon-pink">The track could not be uploaded.</p>
        <ul class="mt-3 grid gap-1 text-white/70">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="glass-card rounded-[2rem] border border-white/10 p-8">
      <p class="section-kicker">Admin Uploads</p>
      <h1 class="mt-4 font-display text-5xl font-semibold">Upload tracks for any artist</h1>
      <p class="mt-5 max-w-3xl text-base leading-8 text-white/65">Admins can publish tracks directly into the catalog or save them as drafts without signing into a creator account. Album selection is validated against the chosen artist before the release is stored.</p>
    </div>

    <div class="mt-10 grid gap-8 xl:grid-cols-[1.15fr_0.85fr]">
      <section class="glass-card rounded-[2rem] border border-white/10 p-6 lg:p-7">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div>
            <p class="section-kicker">Track Upload</p>
            <h2 class="mt-4 font-display text-3xl font-semibold">Admin-side release workflow</h2>
            <p class="mt-3 max-w-2xl text-sm leading-7 text-white/55">Choose the target artist, attach an MP3, optionally connect the track to an existing album, and decide whether the song should go live immediately or stay in draft review.</p>
          </div>
          <div class="rounded-[1.5rem] border border-white/10 bg-slate-950/50 px-4 py-3 text-sm text-white/55">
            <p>Accepted audio: <span class="text-white/80">MP3</span></p>
            <p class="mt-1">Max upload size: <span class="text-white/80">{{ $effectiveAudioUploadLimitLabel }}</span></p>
            @if ($audioUploadLimitConstrained)
              <p class="mt-2 text-xs leading-6 text-neon-pink">
                Current server upload limit is below the app target of {{ $targetAudioUploadLimitLabel }}.
              </p>
            @endif
          </div>
        </div>

        <form method="POST" action="{{ route('admin.uploads.store') }}" enctype="multipart/form-data" class="mt-8 grid gap-4">
          @csrf

          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Artist Profile</span>
            <select name="artist_id" class="w-full bg-transparent text-white outline-none" required>
              <option value="" disabled @selected(!old('artist_id')) class="bg-slate-950">Select an artist</option>
              @foreach ($artists as $artist)
                <option value="{{ $artist->id }}" @selected((string) old('artist_id') === (string) $artist->id) class="bg-slate-950">{{ $artist->name }}{{ $artist->user?->email ? ' - '.$artist->user->email : '' }}</option>
              @endforeach
            </select>
          </label>

          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Track Title</span>
            <input name="title" type="text" value="{{ old('title') }}" class="w-full bg-transparent text-white outline-none" required>
          </label>

          <div class="grid gap-4 md:grid-cols-2">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Genre</span>
              <select name="genre" class="w-full bg-transparent text-white outline-none" required>
                @foreach ($genres as $genre)
                  <option value="{{ $genre->name }}" @selected(old('genre') === $genre->name) class="bg-slate-950">{{ $genre->name }}</option>
                @endforeach
                @if (old('genre') && !$genres->contains(fn ($genre) => $genre->name === old('genre')))
                  <option value="{{ old('genre') }}" selected class="bg-slate-950">{{ old('genre') }}</option>
                @endif
              </select>
            </label>

            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
              <span class="mb-2 block text-sm text-white/50">Album</span>
              <select name="album_id" class="w-full bg-transparent text-white outline-none">
                <option value="" class="bg-slate-950">Single release</option>
                @foreach ($artists as $artist)
                  @if ($artist->albums->isNotEmpty())
                    <optgroup label="{{ $artist->name }}">
                      @foreach ($artist->albums as $album)
                        <option value="{{ $album->id }}" @selected((string) old('album_id') === (string) $album->id) class="bg-slate-950">{{ $album->title }}</option>
                      @endforeach
                    </optgroup>
                  @endif
                @endforeach
              </select>
            </label>
          </div>

          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Description</span>
            <textarea name="description" rows="4" class="w-full resize-none bg-transparent text-white outline-none" placeholder="Add context for the release, performance mood, or upload notes.">{{ old('description') }}</textarea>
          </label>

          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4">
            <span class="mb-2 block text-sm text-white/50">Lyrics</span>
            <textarea name="lyrics" rows="7" class="w-full resize-none bg-transparent text-white outline-none" placeholder="Optional full lyrics.">{{ old('lyrics') }}</textarea>
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
              <span class="mt-1 block text-xs leading-6 text-white/50">Checked releases become approved immediately. Leave unchecked to store the track in drafts.</span>
            </div>
            <input name="publish_now" type="checkbox" value="1" @checked(old('publish_now')) class="h-5 w-5 rounded border-white/20 bg-slate-950/70 text-neon-cyan focus:ring-neon-cyan">
          </label>

          <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950" @disabled($artists->isEmpty())>Upload Track</button>
            <p class="text-sm text-white/45">
              @if ($artists->isEmpty())
                Create an artist profile first before using the admin uploader.
              @else
                The selected album must belong to the selected artist.
              @endif
            </p>
          </div>
        </form>
      </section>

      <section class="space-y-8">
        <div class="glass-card rounded-[2rem] border border-white/10 p-6">
          <p class="section-kicker">Upload Notes</p>
          <div class="mt-6 space-y-4 text-sm leading-7 text-white/60">
            <p>Use this when a creator needs help publishing or when the admin team is onboarding catalog content manually.</p>
            <p>Tracks uploaded here use the same public storage flow as creator uploads, so audio and cover art are served directly from the site storage disk.</p>
            <p>Uploaded drafts appear in moderation and admin tables immediately. Published uploads appear in the live catalog without another review step.</p>
          </div>
        </div>

        <div class="glass-card rounded-[2rem] border border-white/10 p-6">
          <div class="flex items-end justify-between gap-4">
            <div>
              <p class="section-kicker">Recent Uploads</p>
              <h2 class="mt-3 font-display text-2xl font-semibold">Latest admin-visible releases</h2>
            </div>
            <a href="{{ route('admin.moderation.index') }}" class="text-sm text-white/55 transition hover:text-neon-cyan">Open moderation</a>
          </div>

          <div class="mt-6 space-y-4">
            @forelse ($recentUploads as $track)
              @php
                  $status = $trackStatusMeta[$track->moderation_status] ?? ['label' => ucfirst($track->moderation_status), 'classes' => 'border-white/10 bg-white/5 text-white/70'];
              @endphp
              <article class="flex items-center gap-4 rounded-[1.5rem] border border-white/10 bg-slate-950/45 p-4">
                <img src="{{ $track->cover_image_url }}" alt="{{ $track->title }}" class="h-16 w-16 rounded-[1rem] object-cover">
                <div class="min-w-0 flex-1">
                  <p class="truncate font-semibold">{{ $track->title }}</p>
                  <p class="truncate text-sm text-white/50">{{ $track->artist?->name ?? 'Unknown artist' }} / {{ $track->album?->title ?? 'Single' }}</p>
                </div>
                <span class="rounded-full border px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $status['classes'] }}">{{ $status['label'] }}</span>
              </article>
            @empty
              <div class="rounded-[1.5rem] border border-dashed border-white/10 bg-slate-950/45 px-4 py-8 text-center text-sm text-white/45">
                No tracks uploaded yet.
              </div>
            @endforelse
          </div>
        </div>
      </section>
    </div>
  </section>
@endsection
