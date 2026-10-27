{{-- Mobile header: menu drawer, brand, cart and search. --}}
@include('aponbasket.menu')

<div class="ab-header-mobile__row">
    <x-shop::drawer position="left" width="330px">
        <x-slot:toggle>
            <button type="button" class="ab-headicons__link" aria-label="Open menu">
                <svg width="22" height="22" aria-hidden="true"><use href="#ab-menu"></use></svg>
            </button>
        </x-slot>

        <x-slot:header>
            <p class="ab-drawer__title">Menu</p>
        </x-slot>

        <x-slot:content>
            <div class="ab-drawer__content">
                <a href="{{ route('shop.home.index') }}" class="ab-drawer__link">Home</a>

                @foreach ($abMenuCategories as $abItem)
                    <div class="ab-drawer__group">
                        <a href="{{ $abItem['url'] }}" class="ab-drawer__link">{{ $abItem['name'] }}</a>

                        @if (count($abItem['children']))
                            <div class="ab-drawer__sub">
                                @foreach ($abItem['children'] as $abChild)
                                    <a href="{{ $abChild['url'] }}">{{ $abChild['name'] }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach

                <a href="{{ route('shop.search.index') }}" class="ab-drawer__link">Shop all</a>
                <a href="{{ route('shop.compare.index') }}" class="ab-drawer__link">Compare</a>
                <a href="{{ route('shop.home.contact_us') }}" class="ab-drawer__link">Contact</a>

                <div class="ab-drawer__footer">
                    @guest('customer')
                        <a href="{{ route('shop.customer.session.create') }}" class="ab-btn ab-btn--primary ab-btn--sm">Sign in</a>
                        <a href="{{ route('shop.customers.register.index') }}" class="ab-btn ab-btn--ghost ab-btn--sm">Sign up</a>
                    @endguest

                    @auth('customer')
                        <a href="{{ route('shop.customers.account.index') }}" class="ab-btn ab-btn--ghost ab-btn--sm">My account</a>
                        <a href="{{ route('shop.customers.account.orders.index') }}" class="ab-btn ab-btn--ghost ab-btn--sm">My orders</a>
                    @endauth
                </div>
            </div>
        </x-slot>
    </x-shop::drawer>

    <a href="{{ route('shop.home.index') }}" class="ab-logo" aria-label="{{ config('app.name') }}">
        @include('aponbasket.brand')
    </a>

    <div class="ab-headicons">
        @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
            <div class="ab-headicons__cart">
                @include('shop::checkout.cart.mini-cart')
            </div>
        @endif
    </div>
</div>

<form action="{{ route('shop.search.index') }}" class="ab-searchbar ab-searchbar--mobile" role="search">
    <svg width="16" height="16" class="ab-searchbar__icon" aria-hidden="true"><use href="#ab-search"></use></svg>

    <label for="ab-search-mobile" class="sr-only">Search products</label>

    <input
        id="ab-search-mobile"
        type="search"
        name="query"
        value="{{ request('query') }}"
        placeholder="@lang('shop::app.components.layouts.header.desktop.bottom.search-text')"
        required
    >

    <button type="submit" aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.submit')">
        <svg width="15" height="15" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
    </button>
</form>
