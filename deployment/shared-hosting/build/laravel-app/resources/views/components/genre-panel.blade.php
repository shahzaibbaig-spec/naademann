@props(['genre'])

<a href="{{ route('listen', ['genre' => $genre->name]) }}" class="genre-panel hover-lift relative overflow-hidden rounded-[1.75rem]">
  <img src="{{ $genre->image_url }}" alt="{{ $genre->name }}" class="absolute inset-0 h-full w-full object-cover">
  <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(2, 6, 23, 0.08), rgba(2, 6, 23, 0.86)), radial-gradient(circle at top right, {{ $genre->color ?: '#3bf2ff' }}55, transparent 42%);"></div>
  <div class="relative z-10 flex h-full items-end p-6">
    <div>
      <p class="text-xs uppercase tracking-[0.28em]" style="color: {{ $genre->color ?: '#3bf2ff' }}">Frequency</p>
      <h3 class="mt-2 font-display text-2xl font-semibold">{{ $genre->name }}</h3>
      @if ($genre->description)
        <p class="mt-3 max-w-[13rem] text-sm text-white/60">{{ $genre->description }}</p>
      @endif
    </div>
  </div>
</a>
