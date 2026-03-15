@props([
    'label',
    'value',
    'description' => null,
    'accent' => 'cyan',
])

@php
    $accentClasses = match ($accent) {
        'pink' => 'text-neon-blue',
        'blue' => 'text-neon-blue',
        default => 'text-neon-cyan',
    };
@endphp

<article class="glass-card rounded-[1.75rem] border border-neon-cyan/25 bg-neon-cyan/5 p-6">
  <p class="text-sm uppercase tracking-[0.28em] text-neon-cyan/60">{{ $label }}</p>
  <p class="mt-4 text-4xl font-semibold {{ $accentClasses }}">{{ $value }}</p>
  @if ($description)
    <p class="mt-3 text-sm leading-7 text-neon-cyan/75">{{ $description }}</p>
  @endif
</article>
