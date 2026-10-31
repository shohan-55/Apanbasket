{{-- Injected only when AponBasket is the selected storefront theme. --}}
@prepend('styles')
    <link rel="stylesheet" href="{{ asset('themes/shop/aponbasket/css/aponbasket.css') }}?v={{ \App\AponBasket\Theme\Defaults::VERSION }}">
@endprepend
