@props([
'variant' => 'primary',
'size' => 'md',
'fullWidth' => false
])

@php
// Mapping style tetap sama agar konsisten
$variants = [
'primary' => 'bg-blue-600 text-white hover:bg-blue-700',
'outline' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50',
'success' => 'bg-emerald-600 text-white hover:bg-emerald-700',
];

$sizes = [
'sm' => 'px-2 py-1 text-xs',
'md' => 'px-4 py-2 text-sm',
'lg' => 'px-6 py-3 text-base',
];

$classes = "inline-flex items-center justify-center font-medium rounded transition "
. ($variants[$variant] ?? $variants['primary']) . " "
. ($sizes[$size] ?? $sizes['md']) . " "
. ($fullWidth ? 'w-full' : '');
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    {{-- $slot adalah tempat di mana isi kustom kamu akan muncul --}}
    {{ $slot }}
</button>