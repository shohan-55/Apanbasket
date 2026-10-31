{{-- Only the AponBasket Appearance editor receives this fallback; Bagisto icons keep their own font. --}}
@push('styles')
    <style>
        @font-face {
            font-family: 'AponBasket Editor Bengali';
            font-style: normal;
            font-weight: 100 900;
            font-display: swap;
            src: url('{{ asset('themes/shop/aponbasket/fonts/noto-sans-bengali-bengali-wght-normal.woff2') }}') format('woff2');
            unicode-range: U+0951-0952,U+0964-0965,U+0980-09FE,U+1CD0,U+1CD2,U+1CD5-1CD6,U+1CD8,U+1CE1,U+1CEA,U+1CED,U+1CF2,U+1CF5-1CF7,U+200C-200D,U+20B9,U+25CC,U+A8F1;
        }

        html {
            --font-sans: ui-sans-serif, system-ui, 'AponBasket Editor Bengali', sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
            font-family: ui-sans-serif, system-ui, 'AponBasket Editor Bengali', sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
        }
    </style>
@endpush
