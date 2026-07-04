@php
    $appStoreUrl = $appStoreUrl ?? null;
    $playStoreUrl = $playStoreUrl ?? null;
    $hasAny = filled($appStoreUrl) || filled($playStoreUrl);
@endphp

@if ($hasAny)
    <div class="store-badges" role="group" aria-label="Download the mobile app">
        @if (filled($appStoreUrl))
            <a href="{{ $appStoreUrl }}" class="store-badge-link" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('images/badges/app-store.svg') }}" width="135" height="40" alt="Download on the App Store">
            </a>
        @endif
        @if (filled($playStoreUrl))
            <a href="{{ $playStoreUrl }}" class="store-badge-link" target="_blank" rel="noopener noreferrer">
                <img src="{{ asset('images/badges/google-play.svg') }}" width="135" height="40" alt="Get it on Google Play">
            </a>
        @endif
    </div>
@endif
