{{--
    AponBasket footer.
    Edit the $columns / $contact arrays below to change the link columns and
    contact details. Newsletter form uses Bagisto's own subscription route.
--}}
@php
    $channel = core()->getCurrentChannel();

    $columns = [
        'Shop' => [
            ['title' => 'All products', 'url' => route('shop.search.index')],
            ['title' => 'New arrivals', 'url' => route('shop.search.index', ['sort' => 'created_at-desc'])],
            ['title' => 'Best sellers', 'url' => route('shop.search.index', ['sort' => 'price-desc'])],
            ['title' => 'Compare products', 'url' => route('shop.compare.index')],
        ],
        'Company' => [
            ['title' => 'About us', 'url' => url('about-us')],
            ['title' => 'Our story', 'url' => url('about-us')],
            ['title' => 'Why organic', 'url' => url('why-organic')],
            ['title' => 'Contact us', 'url' => route('shop.home.contact_us')],
        ],
        'Help' => [
            ['title' => 'Shipping & delivery', 'url' => url('shipping-and-delivery')],
            ['title' => 'Returns & refunds', 'url' => url('returns-and-refunds')],
            ['title' => 'Privacy policy', 'url' => url('privacy-policy')],
            ['title' => 'Terms of service', 'url' => url('terms-of-service')],
        ],
    ];

    $contact = [
        'phone'   => '+880 1700 000 000',
        'email'   => 'hello@aponbasket.com',
        'address' => 'House 12, Road 7, Dhanmondi, Dhaka 1205',
        'hours'   => 'Saturday – Thursday, 9 AM – 9 PM',
    ];

    $socials = [
        ['icon' => 'ab-facebook', 'label' => 'Facebook', 'url' => 'https://facebook.com/aponbasket'],
        ['icon' => 'ab-instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com/aponbasket'],
        ['icon' => 'ab-youtube', 'label' => 'YouTube', 'url' => 'https://youtube.com/@aponbasket'],
        ['icon' => 'ab-tiktok', 'label' => 'TikTok', 'url' => 'https://tiktok.com/@aponbasket'],
    ];

    $payments = ['bKash', 'Nagad', 'Rocket', 'Cash on Delivery'];
@endphp

{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<footer class="ab-footer">
    <div class="ab-container">

        {{-- Footer call to action --}}
        <div class="ab-footer__cta">
            <div>
                <span class="ab-footer__cta-badge">
                    <svg width="13" height="13" aria-hidden="true"><use href="#ab-leaf"></use></svg>
                    Pure nutrition for your family
                </span>

                <h2>Real food, made the slow way.</h2>

                <p>Order before 4 PM and we dispatch the same day — free delivery inside Dhaka over ৳1,500.</p>
            </div>

            @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                <form
                    class="ab-news-form"
                    method="POST"
                    action="{{ route('shop.subscription.store') }}"
                >
                    @csrf

                    <label for="ab-footer-email" class="sr-only">Email address</label>

                    <input
                        id="ab-footer-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="@lang('shop::app.components.layouts.footer.email')"
                        autocomplete="email"
                        required
                    >

                    <button type="submit">Join us</button>
                </form>
            @endif
        </div>

        <div class="ab-footer__grid">
            <div>
                <a href="{{ route('shop.home.index') }}" class="ab-footer__logo" aria-label="{{ config('app.name') }}">
                    @include('aponbasket.brand', ['variant' => 'light'])
                </a>

                <p class="ab-footer__brand-text">
                    Natural &amp; organic products from Bangladeshi farms — homemade gawa ghee,
                    stone ground spices and cold pressed oil, delivered to your kitchen.
                </p>

                <div class="ab-footer__socials">
                    @foreach ($socials as $social)
                        <a
                            href="{{ $social['url'] }}"
                            class="ab-footer__social"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="{{ $social['label'] }}"
                        >
                            <svg width="16" height="16" aria-hidden="true"><use href="#{{ $social['icon'] }}"></use></svg>
                        </a>
                    @endforeach
                </div>
            </div>

            @foreach ($columns as $heading => $links)
                <div>
                    <h3>{{ $heading }}</h3>

                    <ul class="ab-footer__links">
                        @foreach ($links as $link)
                            <li>
                                <a href="{{ $link['url'] }}">{{ $link['title'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div>
                <h3>Talk to us</h3>

                <ul class="ab-footer__contact">
                    <li>
                        <svg width="16" height="16" aria-hidden="true"><use href="#ab-phone"></use></svg>
                        <div>
                            <strong>Call / WhatsApp</strong>
                            {{ $contact['phone'] }}
                        </div>
                    </li>
                    <li>
                        <svg width="16" height="16" aria-hidden="true"><use href="#ab-mail"></use></svg>
                        <div>
                            <strong>Email</strong>
                            {{ $contact['email'] }}
                        </div>
                    </li>
                    <li>
                        <svg width="16" height="16" aria-hidden="true"><use href="#ab-pin"></use></svg>
                        <div>
                            <strong>Pickup point</strong>
                            {{ $contact['address'] }}
                        </div>
                    </li>
                    <li>
                        <svg width="16" height="16" aria-hidden="true"><use href="#ab-clock"></use></svg>
                        <div>
                            <strong>Open</strong>
                            {{ $contact['hours'] }}
                        </div>
                    </li>
                </ul>

                <div class="ab-footer__pay" aria-label="Accepted payment methods">
                    @foreach ($payments as $payment)
                        <span class="ab-pay-chip">{{ $payment }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="ab-footer__bottom">
            <p>
                &copy; <span data-ab-year>{{ date('Y') }}</span>
                {{ $channel->name ?? 'AponBasket' }}. Natural &amp; Organic Products. All rights reserved.
            </p>

            <div class="ab-footer__bottom-links">
                <a href="{{ url('privacy-policy') }}">Privacy</a>
                <a href="{{ url('terms-of-service') }}">Terms</a>
                <a href="{{ url('returns-and-refunds') }}">Refunds</a>
                <a href="{{ route('shop.home.contact_us') }}">Support</a>
            </div>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
