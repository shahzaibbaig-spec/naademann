<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Naad-e-Maan')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              neon: { cyan: "#3bf2ff", blue: "#4f7cff", pink: "#ff4fd8" },
            },
            fontFamily: {
              display: ["Space Grotesk", "sans-serif"],
              body: ["Sora", "sans-serif"],
            },
          },
        },
      };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/naad.css') }}">
  </head>
  @php
    $naadBootstrap = [
        'authenticated' => auth()->check(),
        'favorites' => $favoriteSongIds ?? [],
        'follows' => $followedArtistIds ?? [],
        'playlists' => ($userPlaylists ?? collect())->values(),
        'queue' => $playerQueue ?? [],
        'ajax' => [
            'search' => route('ajax.search'),
            'searchPage' => route('search.index'),
            'playlists' => auth()->check() ? route('ajax.playlists') : null,
            'login' => route('login'),
            'listen' => route('listen'),
            'artists' => route('artists.index'),
        ],
    ];
  @endphp
  <body class="font-body text-white {{ $bodyClass ?? '' }}">
    <script id="naad-bootstrap" type="application/json">
      {{ Illuminate\Support\Js::from($naadBootstrap) }}
    </script>

    <x-navbar :platform-settings="$platformSettings ?? collect()" />

    @if (session('status'))
      <div class="mx-auto mt-6 max-w-7xl px-6 lg:px-8">
        <div class="rounded-[1.5rem] border border-neon-cyan/20 bg-neon-cyan/10 px-5 py-4 text-sm text-neon-cyan">
          {{ session('status') }}
        </div>
      </div>
    @endif

    <main>
      @yield('content')
    </main>

    <x-footer :platform-settings="$platformSettings ?? collect()" />
    <x-player-widget :queue="$playerQueue ?? []" />
    <script src="{{ asset('js/naad.js') }}"></script>
    @stack('scripts')
  </body>
</html>
