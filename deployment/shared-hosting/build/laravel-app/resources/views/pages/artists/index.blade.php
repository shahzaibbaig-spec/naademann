@extends('layouts.app')

@section('title', 'Naad-e-Maan | Artists')

@section('content')
  <section class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
    <div class="glass-card rounded-[2rem] border border-white/10 p-8">
      <p class="section-kicker">Artist Directory</p>
      <h1 class="mt-4 font-display text-5xl font-semibold">Discover the Voices Behind the Platform</h1>
      <p class="mt-5 max-w-3xl text-base leading-8 text-white/65">
        Follow artists, jump straight into their releases, and move between creator-led profiles with the same floating playback queue.
      </p>
    </div>

    <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
      @foreach ($artists as $artist)
        <x-artist-card :artist="$artist" :followed-artist-ids="$followedArtistIds" />
      @endforeach
    </div>

    <div class="mt-10">
      {{ $artists->links() }}
    </div>
  </section>
@endsection
