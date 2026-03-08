@extends('layouts.app')

@section('title', 'Naad-e-Maan | Banners')

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    @include('admin._nav')

    <div class="grid gap-5 md:grid-cols-2">
      <x-stats-card label="Banner Slots" :value="number_format($bannerMetrics['total'])" description="Homepage banner records in the system." />
      <x-stats-card label="Active Banners" :value="number_format($bannerMetrics['active'])" accent="pink" description="Banner sections currently live on the homepage." />
    </div>

    <div class="mt-8 grid gap-8 xl:grid-cols-[0.88fr_1.12fr]">
      <section class="glass-card rounded-[2rem] border border-white/10 p-6">
        <p class="section-kicker">Create Banner</p>
        <h1 class="mt-3 font-display text-4xl font-semibold">Homepage banner manager</h1>
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4">
          @csrf
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Title</span><input name="title" type="text" class="w-full bg-transparent text-white outline-none" required></label>
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Subtitle</span><input name="subtitle" type="text" class="w-full bg-transparent text-white outline-none"></label>
          <div class="grid gap-4 md:grid-cols-2">
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">CTA Label</span><input name="cta_label" type="text" class="w-full bg-transparent text-white outline-none"></label>
            <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">CTA URL</span><input name="cta_url" type="text" class="w-full bg-transparent text-white outline-none"></label>
          </div>
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Image URL</span><input name="image_url" type="url" class="w-full bg-transparent text-white outline-none"></label>
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Or Upload Image</span><input name="image_file" type="file" accept="image/*" class="w-full text-sm text-white/70"></label>
          <label class="glass-card rounded-[1.5rem] border border-white/10 p-4"><span class="mb-2 block text-sm text-white/50">Sort Order</span><input name="sort_order" type="number" min="0" class="w-full bg-transparent text-white outline-none"></label>
          <button type="submit" class="rounded-full bg-white px-5 py-3 text-sm font-semibold text-slate-950">Create Banner</button>
        </form>
      </section>

      <section class="space-y-5">
        @foreach ($banners as $banner)
          <div class="glass-card overflow-hidden rounded-[2rem] border border-white/10">
            <div class="relative h-44">
              <img src="{{ $banner->image_url ?: 'https://images.unsplash.com/photo-1506157786151-b8491531f063?auto=format&fit=crop&w=1600&q=80' }}" alt="{{ $banner->title }}" class="h-full w-full object-cover">
              <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>
              <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between gap-3">
                <div>
                  <p class="text-lg font-semibold">{{ $banner->title }}</p>
                  <p class="text-sm text-white/60">{{ $banner->subtitle }}</p>
                </div>
                <span class="rounded-full border {{ $banner->is_active ? 'border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan' : 'border-white/10 bg-white/5 text-white/65' }} px-3 py-1 text-xs uppercase tracking-[0.2em]">{{ $banner->is_active ? 'Active' : 'Paused' }}</span>
              </div>
            </div>

            <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="p-6">
              @csrf
              @method('PUT')
              <div class="grid gap-4 md:grid-cols-2">
                <input name="title" type="text" value="{{ $banner->title }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none">
                <input name="subtitle" type="text" value="{{ $banner->subtitle }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none">
                <input name="cta_label" type="text" value="{{ $banner->cta_label }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none">
                <input name="cta_url" type="text" value="{{ $banner->cta_url }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none">
                <input name="image_url" type="url" value="{{ $banner->image_url }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none md:col-span-2">
                <input name="image_file" type="file" accept="image/*" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none md:col-span-2">
              </div>

              <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                  <input name="sort_order" type="number" min="0" value="{{ $banner->sort_order }}" class="rounded-full border border-white/10 bg-transparent px-4 py-3 text-white outline-none">
                  <label class="flex items-center gap-2 text-sm text-white/65">
                    <input type="hidden" name="is_active" value="0">
                    <input name="is_active" type="checkbox" value="1" @checked($banner->is_active)>
                    Active
                  </label>
                </div>
                <div class="flex items-center gap-4">
                  <button type="submit" class="text-neon-cyan">Save</button>
                </div>
              </div>
            </form>

            <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="px-6 pb-6">
              @csrf
              @method('DELETE')
              <div class="flex justify-end">
                <button type="submit" class="text-neon-pink">Delete</button>
              </div>
            </form>
          </div>
        @endforeach
      </section>
    </div>
  </section>
@endsection
