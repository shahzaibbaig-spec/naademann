@extends('layouts.app')

@section('title', 'Naad-e-Maan | Track Moderation')

@php
    $statusMeta = [
        'draft' => 'border-white/10 bg-white/5 text-white/70',
        'pending' => 'border-neon-blue/20 bg-neon-blue/10 text-neon-blue',
        'approved' => 'border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan',
        'rejected' => 'border-neon-pink/20 bg-neon-pink/10 text-neon-pink',
    ];
@endphp

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    @include('admin._nav')

    <div class="glass-card rounded-[2rem] border border-white/10 p-6">
      <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
        <div>
          <p class="section-kicker">Moderate Tracks</p>
          <h1 class="mt-3 font-display text-4xl font-semibold">Status filters and moderation controls</h1>
        </div>
        <div class="flex flex-wrap gap-3">
          <a href="{{ route('admin.moderation.index') }}" class="pill-nav {{ $activeStatus === '' ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">All ({{ number_format($statusCounts['all']) }})</a>
          @foreach (['draft', 'pending', 'approved', 'rejected'] as $status)
            <a href="{{ route('admin.moderation.index', ['status' => $status]) }}" class="pill-nav {{ $activeStatus === $status ? 'is-active' : '' }} rounded-full border border-white/10 px-4 py-2 text-sm text-white/70">{{ ucfirst($status) }} ({{ number_format($statusCounts[$status]) }})</a>
          @endforeach
        </div>
      </div>

      <div class="mt-6 overflow-x-auto">
        <table class="w-full min-w-[1040px] text-left text-sm">
          <thead class="text-white/45">
            <tr>
              <th class="pb-4">Track</th>
              <th class="pb-4">Artist</th>
              <th class="pb-4">Album</th>
              <th class="pb-4">Release Date</th>
              <th class="pb-4">Queue</th>
              <th class="pb-4">Status</th>
              <th class="pb-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10">
            @foreach ($tracks as $track)
              <tr>
                <td class="py-4">
                  <div class="flex items-center gap-4">
                    <img src="{{ $track->cover_image_url }}" alt="{{ $track->title }}" class="h-12 w-12 rounded-[1rem] object-cover">
                    <div class="min-w-0">
                      <div class="truncate font-medium">{{ $track->title }}</div>
                      <div class="truncate text-white/45">{{ $track->genre }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-4 text-white/60">{{ $track->artist?->name ?? 'Unknown artist' }}</td>
                <td class="py-4 text-white/60">{{ $track->album?->title ?? 'Single' }}</td>
                <td class="py-4 text-white/60">{{ ($track->release_date ?: $track->album?->release_date)?->format('M d, Y') ?? 'TBA' }}</td>
                <td class="py-4">
                  <span class="rounded-full border px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $track->is_featured ? 'border-neon-cyan/20 bg-neon-cyan/10 text-neon-cyan' : 'border-white/10 bg-white/5 text-white/55' }}">
                    {{ $track->is_featured ? 'In Queue' : 'Hidden' }}
                  </span>
                </td>
                <td class="py-4">
                  <span class="rounded-full border px-3 py-1 text-xs uppercase tracking-[0.2em] {{ $statusMeta[$track->moderation_status] ?? 'border-white/10 bg-white/5 text-white/70' }}">
                    {{ $track->moderation_status }}
                  </span>
                </td>
                <td class="py-4">
                  <form method="POST" action="{{ route('admin.moderation.update', $track) }}" class="flex items-center justify-end gap-3">
                    @csrf
                    @method('PUT')
                    <select name="moderation_status" class="rounded-full border border-white/10 bg-slate-950/70 px-3 py-2 text-white">
                      @foreach (['draft', 'pending', 'approved', 'rejected'] as $status)
                        <option value="{{ $status }}" @selected($track->moderation_status === $status)>{{ ucfirst($status) }}</option>
                      @endforeach
                    </select>
                    <label class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-slate-950/45 px-3 py-2 text-xs uppercase tracking-[0.18em] text-white/70">
                      <input type="hidden" name="is_featured" value="0">
                      <input type="checkbox" name="is_featured" value="1" @checked($track->is_featured) class="h-4 w-4 rounded border-white/20 bg-transparent text-neon-cyan focus:ring-neon-cyan/40">
                      <span>Queue</span>
                    </label>
                    <button type="submit" class="rounded-full border border-neon-cyan/20 bg-neon-cyan/10 px-4 py-2 text-xs font-semibold text-neon-cyan">Save</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="mt-8">{{ $tracks->links() }}</div>
    </div>
  </section>
@endsection
