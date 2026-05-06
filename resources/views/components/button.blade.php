@props([
    'type' => 'button',
    'href' => null
])

@php
$base = "inline-flex items-center px-4 py-2 rounded-lg font-semibold transition duration-200";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
    </button>
@endif