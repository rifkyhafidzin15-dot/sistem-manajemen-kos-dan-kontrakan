@props(['dark' => false, 'size' => 'md', 'text' => true])

@php
    $sizes = [
        'sm' => ['icon' => 'w-6 h-6',   'text' => 'text-base', 'gap' => 'gap-2'],
        'md' => ['icon' => 'w-9 h-9',   'text' => 'text-2xl',  'gap' => 'gap-2.5'],
        'lg' => ['icon' => 'w-12 h-12', 'text' => 'text-3xl',  'gap' => 'gap-3'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $ring = $dark ? '#818CF8' : '#5B4AE6';
    $roof = $dark ? '#C4B5FD' : '#7C3AED';
    $kost = $dark ? 'text-white' : 'text-[#0F172A]';
    $zenn = $dark ? 'text-indigo-400' : 'text-[#5B4AE6]';
@endphp

<span {{ $attributes->class(['inline-flex items-center', $s['gap']]) }}>
    <svg class="{{ $s['icon'] }} shrink-0" viewBox="0 0 40 40" fill="none" aria-hidden="true">
        <circle cx="20" cy="20" r="16.5" fill="none" stroke="{{ $ring }}" stroke-width="3.5"
                stroke-linecap="round" stroke-dasharray="84 20" transform="rotate(-50 20 20)"/>
        <path d="M12 22 L20 14 L28 22" fill="none" stroke="{{ $roof }}" stroke-width="3"
              stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="20" cy="28.5" r="2.6" fill="{{ $roof }}"/>
    </svg>
    @if ($text)
        <span class="{{ $s['text'] }} font-extrabold tracking-tight leading-none {{ $kost }}">Kost<span class="{{ $zenn }}">Zenn</span></span>
    @endif
</span>