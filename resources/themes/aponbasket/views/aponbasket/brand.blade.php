{{--
    AponBasket brand lockup.
    Renders the channel logo when the merchant uploaded one (Admin → Settings →
    Channels → Logo), otherwise the built-in AponBasket mark + wordmark.

    Usage:
        @include('aponbasket.brand', ['variant' => 'light'])
--}}
@php
    $variant = $variant ?? 'dark';
    $channelLogo = core()->getCurrentChannel()->logo_url ?? null;
@endphp

@if ($channelLogo)
    <img
        src="{{ $channelLogo }}"
        alt="{{ core()->getCurrentChannel()->logo_alt ?: config('app.name') }}"
        class="ab-brand__logo"
        width="168"
        height="42"
    >
@else
    <span class="ab-brand {{ $variant === 'light' ? 'ab-brand--light' : '' }}">
        <svg
            class="ab-brand__mark"
            viewBox="0 0 48 48"
            aria-hidden="true"
            focusable="false"
        >
            <path d="M23.6 21.4c-4.9-1.9-8-5.7-7.8-11.1 5.2.6 8.4 4.6 7.8 11.1Z" fill="{{ $variant === 'light' ? '#7fb03a' : '#1a7a44' }}"/>
            <path d="M24.4 21.4c4.9-1.9 8-5.7 7.8-11.1-5.2.6-8.4 4.6-7.8 11.1Z" fill="{{ $variant === 'light' ? '#c8e58f' : '#7fb03a' }}"/>
            <path d="M24 21.6v-8.2" stroke="{{ $variant === 'light' ? '#eaf2d7' : '#0b3a21' }}" stroke-width="2" stroke-linecap="round"/>
            <path d="M7.6 24.2h32.8c-1.1 8.2-7.8 14.4-16.4 14.4S8.7 32.4 7.6 24.2Z" fill="{{ $variant === 'light' ? '#eaf2d7' : '#0b3a21' }}"/>
            <path d="M6.2 24.2h35.6" stroke="{{ $variant === 'light' ? '#eaf2d7' : '#0b3a21' }}" stroke-width="3.4" stroke-linecap="round"/>
            <path d="M18.4 27.8l1.3 7.4M29.6 27.8l-1.3 7.4M24 27.8v7.7" stroke="{{ $variant === 'light' ? '#10512e' : '#eaf2d7' }}" stroke-width="1.4" stroke-linecap="round" opacity=".55"/>
        </svg>

        <span class="ab-brand__type">
            <span class="ab-brand__name">Apon<span>Basket</span></span>
            <span class="ab-brand__tag">Natural &amp; Organic Products</span>
        </span>
    </span>
@endif
