{{--
    AponBasket storefront layout.

    A copy of the core storefront layout (packages/Webkul/Shop/src/Resources/views/
    components/layouts/index.blade.php) with the theme's fonts and stylesheets added.
    Everything the storefront relies on is kept: the render events, the meta/currency
    tags, @bagistoVite, @stack('meta'|'styles'|'scripts'), <div id="app">, the flash
    group and confirm modal, and the script that mounts the Vue app.
--}}
@props([
    'hasHeader'  => true,
    'hasFeature' => true,
    'hasFooter'  => true,
])

<!DOCTYPE html>

<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}"
>
    <head>

        {!! view_render_event('bagisto.shop.layout.head.before') !!}

        <title>{{ $title ?? '' }}</title>

        <meta charset="UTF-8">

        <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
        >
        <meta
            http-equiv="content-language"
            content="{{ app()->getLocale() }}"
        >

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <meta
            name="base-url"
            content="{{ url()->to('/') }}"
        >
        <meta
            name="currency"
            content="{{ core()->getCurrentCurrency()->toJson() }}"
        >
        <meta
            name="generator"
            content="Bagisto"
        >

        @stack('meta')

        {{-- Theme colour, used by mobile browsers and the preview cards --}}
        <meta name="theme-color" content="#0b3a21">

        <link
            rel="icon"
            sizes="any"
            href="{{ core()->getCurrentChannel()->favicon_url ?? asset('themes/shop/aponbasket/images/favicon.svg') }}"
        >

        {{-- Core storefront build (the theme reuses the default theme's Vite build) --}}
        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

        {{-- Typography: body, display and Bengali --}}
        <link
            rel="preconnect"
            href="https://fonts.googleapis.com"
            crossorigin
        />
        <link
            rel="preconnect"
            href="https://fonts.gstatic.com"
            crossorigin
        />
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=Hind+Siliguri:wght@400;500;600&family=Poppins:wght@400;500;600;700&display=swap"
        />

        {{-- AponBasket theme styles (theme.css first, home.css for the homepage sections) --}}
        <link
            rel="stylesheet"
            href="{{ asset('themes/shop/aponbasket/css/theme.css') }}"
        >
        <link
            rel="stylesheet"
            href="{{ asset('themes/shop/aponbasket/css/home.css') }}"
        >

        <script
            src="{{ asset('themes/shop/aponbasket/js/theme.js') }}"
            defer
        ></script>

        @stack('styles')

        <style>
            {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
        </style>

        {!! view_render_event('bagisto.shop.layout.head.after') !!}

    </head>

    <body>
        {!! view_render_event('bagisto.shop.layout.body.before') !!}

        <a
            href="#main"
            class="skip-to-main-content-link"
        >
            Skip to main content
        </a>

        {{-- Inline icon sprite: every <use href="#ab-…"> on the page points here --}}
        @include('aponbasket.icons')

        <div id="app">
            <!-- Flash Message Blade Component -->
            <x-shop::flash-group />

            <!-- Confirm Modal Blade Component -->
            <x-shop::modal.confirm />

            <!-- Page Header Blade Component -->
            @if ($hasHeader)
                <x-shop::layouts.header />
            @endif

            @if(
                core()->getConfigData('general.gdpr.settings.enabled')
                && core()->getConfigData('general.gdpr.cookie.enabled')
            )
                <x-shop::layouts.cookie />
            @endif

            {!! view_render_event('bagisto.shop.layout.content.before') !!}

            <!-- Page Content Blade Component -->
            <main id="main">
                {{ $slot }}
            </main>

            {!! view_render_event('bagisto.shop.layout.content.after') !!}

            <!-- Page Services Blade Component -->
            @if ($hasFeature)
                <x-shop::layouts.services />
            @endif

            <!-- Page Footer Blade Component -->
            @if ($hasFooter)
                <x-shop::layouts.footer />
            @endif
        </div>

        {!! view_render_event('bagisto.shop.layout.body.after') !!}

        {{-- Floating actions: WhatsApp + back to top --}}
        @include('aponbasket.fabs')

        @stack('scripts')

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.before') !!}
        <script>
            /**
             * Mount the Vue application after every deferred module script and
             * every image has loaded, so all components are registered.
             */
            if (document.readyState === "complete") {
                app.mount("#app");
            } else {
                window.addEventListener("load", function () {
                    app.mount("#app");
                });
            }
        </script>

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.after') !!}

        <script type="text/javascript">
            {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
        </script>
    </body>
</html>
