{{-- Desktop header: brand, category navigation, search and account actions. --}}
@include('aponbasket.menu')

<div class="ab-container">
    <div class="ab-header-desktop__bar">
        <a
            href="{{ route('shop.home.index') }}"
            class="ab-logo"
            aria-label="{{ config('app.name') }}"
        >
            @include('aponbasket.brand')
        </a>

        <nav class="ab-nav" aria-label="Main navigation">
            <div class="ab-nav__item">
                <a
                    href="{{ route('shop.home.index') }}"
                    class="ab-nav__link {{ request()->routeIs('shop.home.index') ? 'is-active' : '' }}"
                >
                    Home
                </a>
            </div>

            @foreach ($abMenuCategories as $abItem)
                <div class="ab-nav__item">
                    <a
                        href="{{ $abItem['url'] }}"
                        class="ab-nav__link {{ request()->fullUrlIs($abItem['url']) ? 'is-active' : '' }}"
                    >
                        {{ $abItem['name'] }}

                        @if (count($abItem['children']))
                            <svg width="11" height="11" class="ab-nav__caret" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                        @endif
                    </a>

                    @if (count($abItem['children']))
                        <div class="ab-nav__drop">
                            @foreach ($abItem['children'] as $abChild)
                                <a href="{{ $abChild['url'] }}">
                                    {{ $abChild['name'] }}
                                    <svg width="12" height="12" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

            @if (! count($abMenuCategories))
                <div class="ab-nav__item">
                    <a href="{{ route('shop.search.index') }}" class="ab-nav__link">Shop all</a>
                </div>
            @endif

            <div class="ab-nav__item">
                <a href="{{ route('shop.home.contact_us') }}" class="ab-nav__link">Contact</a>
            </div>
        </nav>

        <form
            action="{{ route('shop.search.index') }}"
            class="ab-searchbar"
            role="search"
        >
            <svg width="16" height="16" class="ab-searchbar__icon" aria-hidden="true"><use href="#ab-search"></use></svg>

            <label for="ab-search-desktop" class="sr-only">Search products</label>

            <input
                id="ab-search-desktop"
                type="search"
                name="query"
                value="{{ request('query') }}"
                placeholder="@lang('shop::app.components.layouts.header.desktop.bottom.search-text')"
                minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                required
            >

            <button type="submit" aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.submit')">
                <svg width="15" height="15" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
            </button>
        </form>

        <div class="ab-headicons">
            @if (core()->getConfigData('catalog.products.settings.compare_option'))
                <a
                    href="{{ route('shop.compare.index') }}"
                    class="ab-headicons__link"
                    aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.compare')"
                >
                    <svg width="21" height="21" aria-hidden="true"><use href="#ab-globe"></use></svg>
                </a>
            @endif

            <x-shop::dropdown position="bottom-right">
                <x-slot:toggle>
                    <span class="ab-headicons__link" role="button" tabindex="0" aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.profile')">
                        <svg width="21" height="21" aria-hidden="true"><use href="#ab-user"></use></svg>
                    </span>
                </x-slot>

                @guest('customer')
                    <x-slot:content>
                        <div class="ab-account-pop">
                            <p class="ab-account-pop__title">Welcome to AponBasket</p>
                            <p class="ab-account-pop__text">Sign in to track orders, save addresses and build your wishlist.</p>

                            <div class="ab-account-pop__actions">
                                <a href="{{ route('shop.customer.session.create') }}" class="ab-btn ab-btn--primary ab-btn--sm">Sign in</a>
                                <a href="{{ route('shop.customers.register.index') }}" class="ab-btn ab-btn--ghost ab-btn--sm">Sign up</a>
                            </div>
                        </div>
                    </x-slot>
                @endguest

                @auth('customer')
                    <x-slot:content>
                        <div class="ab-account-pop">
                            <p class="ab-account-pop__title">Hello, {{ auth()->guard('customer')->user()->first_name }}</p>

                            <div class="ab-account-pop__links">
                                <a href="{{ route('shop.customers.account.index') }}">My account</a>
                                <a href="{{ route('shop.customers.account.orders.index') }}">My orders</a>
                                <a href="{{ route('shop.customers.account.wishlist.index') }}">My wishlist</a>
                                <form method="POST" action="{{ route('shop.customer.session.destroy') }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="ab-account-pop__logout">Sign out</button>
                                </form>
                            </div>
                        </div>
                    </x-slot>
                @endauth
            </x-shop::dropdown>

            @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                <div class="ab-headicons__cart">
                    @include('shop::checkout.cart.mini-cart')
                </div>
            @endif
        </div>
    </div>
</div>
