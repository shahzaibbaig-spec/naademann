@extends('layouts.app')

@section('title', 'Naad-e-Maan | Genres')

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    @include('admin._nav')

    <div class="grid gap-5 md:grid-cols-2">
      <x-stats-card label="Genre Entries" :value="number_format($genreMetrics['total'])" description="All genre records available in discovery." />
      <x-stats-card label="Active Genres" :value="number_format($genreMetrics['active'])" accent="pink" description="Genres currently visible across the platform." />
    </div>

    <div class="mt-8 grid gap-8 xl:grid-cols-[0.88fr_1.12fr]">
      <section class="glass-card rounded-[2rem] border border-white/10 p-6">
        <p class="section-kicker">Create Genre</p>
        <h1 class="mt-3 font-display text-4xl font-semibold">Genre CRUD</h1>
        <form method="POST" action="{{ route('admin.genres.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4">
          @csrf
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Name</span><input name="name" type="text" class="w-full bg-transparent text-white outline-none" required></label>
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Description</span><textarea name="description" rows="4" class="w-full resize-none bg-transparent text-white outline-none"></textarea></label>
          <div class="grid gap-4 md:grid-cols-2">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Image URL</span><input name="image_url" type="url" class="w-full bg-transparent text-white outline-none"></label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Accent Color</span><input name="color" type="text" value="#3bf2ff" class="w-full bg-transparent text-white outline-none" placeholder="#3bf2ff"></label>
          </div>
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Or Upload Image</span><input name="image_file" type="file" accept="image/*" class="w-full text-sm text-white/70"></label>
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Sort Order</span><input name="sort_order" type="number" min="0" class="w-full bg-transparent text-white outline-none"></label>
          <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Create Genre</button>
        </form>
      </section>

      <section class="glass-card rounded-[2rem] border border-white/10 p-6">
        <div class="flex items-end justify-between gap-4">
          <div>
            <p class="section-kicker">Manage Genres</p>
            <h2 class="mt-3 font-display text-3xl font-semibold">Image and color controls</h2>
          </div>
          <p class="text-sm text-white/45">{{ number_format($genres->count()) }} genres</p>
        </div>

        <div class="mt-6 space-y-4">
          @foreach ($genres as $genre)
            <div class="rounded-[1.75rem] border border-white/10 bg-slate-950/45 p-5">
              <form method="POST" action="{{ route('admin.genres.update', $genre) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="flex flex-col gap-5 sm:flex-row">
                  <div class="relative h-24 w-24 overflow-hidden rounded-[1.25rem] border border-white/10">
                    <img src="{{ $genre->image_url ?: 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $genre->name }}" class="h-full w-full object-cover">
                    <span class="absolute bottom-2 left-2 h-4 w-4 rounded-full border border-white/20" style="background-color: {{ $genre->color ?: '#3bf2ff' }}"></span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="font-semibold">{{ $genre->name }}</p>
                    <p class="mt-1 text-sm text-white/50">{{ $genre->slug }}</p>
                  </div>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                  <input name="name" type="text" value="{{ $genre->name }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none">
                  <input name="color" type="text" value="{{ $genre->color }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none" placeholder="#3bf2ff">
                  <input name="image_url" type="url" value="{{ $genre->image_url }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none md:col-span-2" placeholder="Image URL">
                  <input name="image_file" type="file" accept="image/*" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none md:col-span-2">
                </div>

                <textarea name="description" rows="3" class="mt-4 w-full rounded-[1.25rem] border border-white/10 bg-transparent px-4 py-3 text-white outline-none">{{ $genre->description }}</textarea>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                  <div class="flex flex-wrap items-center gap-3">
                    <input name="sort_order" type="number" min="0" value="{{ $genre->sort_order }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none">
                    <label class="flex items-center gap-2 text-sm text-white/65">
                      <input type="hidden" name="is_active" value="0">
                      <input name="is_active" type="checkbox" value="1" @checked($genre->is_active)>
                      Active
                    </label>
                  </div>
                  <div class="flex items-center gap-4">
                    <button type="submit" class="text-neon-cyan">Save</button>
                  </div>
                </div>
              </form>

              <form method="POST" action="{{ route('admin.genres.destroy', $genre) }}" class="mt-4 flex justify-end">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-neon-pink">Delete</button>
              </form>
            </div>
          @endforeach
        </div>
      </section>
    </div>
  </section>
@endsection
