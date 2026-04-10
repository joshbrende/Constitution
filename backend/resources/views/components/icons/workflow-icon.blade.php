@props([
    'key',
    'size' => 18,
    'class' => '',
    'color' => 'currentColor',
])

@php
    $k = (string) $key;
    $s = (int) $size;
@endphp

<span class="{{ $class }}" style="display:inline-flex;align-items:center;justify-content:center;width:{{ $s }}px;height:{{ $s }}px;color:{{ $color }};">
    @switch($k)
        @case('system.gear')
            <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z" stroke="currentColor" stroke-width="2"/>
                <path d="M19.4 13.5a8.2 8.2 0 0 0 .06-1.5 8.2 8.2 0 0 0-.06-1.5l2.02-1.57a.7.7 0 0 0 .17-.9l-1.91-3.3a.7.7 0 0 0-.84-.31l-2.38.96a8.5 8.5 0 0 0-2.6-1.5l-.36-2.53A.7.7 0 0 0 12.8 1h-3.6a.7.7 0 0 0-.69.6l-.36 2.53a8.5 8.5 0 0 0-2.6 1.5l-2.38-.96a.7.7 0 0 0-.84.31L.42 6.28a.7.7 0 0 0 .17.9L2.6 8.75A8.2 8.2 0 0 0 2.54 10.25c0 .51.02 1.01.06 1.5L.59 13.32a.7.7 0 0 0-.17.9l1.91 3.3c.18.31.55.44.89.31l2.38-.96a8.5 8.5 0 0 0 2.6 1.5l.36 2.53c.05.35.35.6.69.6h3.6c.35 0 .64-.25.69-.6l.36-2.53a8.5 8.5 0 0 0 2.6-1.5l2.38.96c.34.13.71 0 .89-.31l1.91-3.3a.7.7 0 0 0-.17-.9L19.4 13.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            @break

        @case('system.bell')
            <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M10 19a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            @break

        @case('academy.course')
            <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 19.5c0-1.5 1.3-2.5 2.8-2.5H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M6.8 17H20V5H6.8C5.3 5 4 6 4 7.5v9.9" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            @break

        @case('academy.assessment')
            <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="8" y="3" width="8" height="4" rx="1" stroke="currentColor" stroke-width="2"/>
                <rect x="6" y="5" width="12" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                <path d="M9 12h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                <path d="M9 16h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            @break

        @case('academy.badge')
            <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l3 7h7l-5.5 4 2.1 7-6.6-4.3L5.4 20l2.1-7L2 9h7l3-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
            </svg>
            @break

        @case('academy.membership')
            <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l3 5 6 .5-4.5 3.8 1.5 6-6-3.2-6 3.2 1.5-6L3 7.5 9 7l3-5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                <path d="M8 21l4-2 4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            @break

        @default
            <svg width="{{ $s }}" height="{{ $s }}" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                <path d="M12 7h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                <path d="M11 11h1v6h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
    @endswitch
</span>

