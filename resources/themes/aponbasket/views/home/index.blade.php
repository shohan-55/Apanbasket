{{--
    AponBasket homepage.

    Everything below is editable in this file:
      • $slides        – the three hero slides (image, copy, price chip, links)
      • category tiles – taken from your catalogue automatically
      • product rows   – filled by Bagisto's product API (best sellers / new arrivals)
      • story, why, steps, testimonials, coupon – static copy you can rewrite freely

    Image paths point at public/themes/shop/aponbasket/images/. Replace those files
    with your own photos (same file names) to update the design without touching code.
--}}
@php
    $channel = core()->getCurrentChannel();

    $asset = fn (string $path) => asset('themes/shop/aponbasket/images/' . $path);

    $slides = [
        [
            'tag_icon'  => 'ab-leaf',
            'tag'       => '100% Pure & Natural',
            'bengali'   => 'খাঁটি স্বাদের নিশ্চয়তা',
            'title_top' => 'Home Made',
            'title_em'  => 'Gawa Ghee',
            'text'      => 'Slow churned in earthen pots from pure cow milk, the way our grandmothers made it. Traditional taste, honest nutrition — no chemicals, ever.',
            'price'     => '৳1,450',
            'compare'   => '৳1,750',
            'chip'      => 'Save 17%',
            'image'     => $asset('products/ghee.jpg'),
            'alt'       => 'Home made gawa ghee in a glass jar',
            'cta'       => 'Shop Gawa Ghee',
            'cta_url'   => route('shop.search.index', ['query' => 'ghee']),
            'secondary' => 'Our Story',
            'secondary_url' => '#ab-story',
            'trust'     => ['Farm sourced', 'Lab tested', 'Cash on delivery'],
            'seal'      => '100%',
            'seal_small' => 'Pure',
            'seal_text' => '100% NATURAL · HOMEMADE · PURE · ',
            'badge_icon' => 'ab-shield',
            'badge_title' => 'No preservatives',
            'badge_text' => 'Made fresh, every week',
        ],
        [
            'tag_icon'  => 'ab-spice',
            'tag'       => 'Pure & Natural',
            'bengali'   => 'প্রকৃতির গুণে, স্বাস্থ্যের জন্য',
            'title_top' => 'Turmeric',
            'title_em'  => 'Powder',
            'text'      => 'Sun dried and stone ground from fresh Bangladeshi roots. Bright colour, deep curcumin and the aroma of a village kitchen.',
            'price'     => '৳320',
            'compare'   => '৳390',
            'chip'      => '250 gm pack',
            'image'     => $asset('products/turmeric.jpg'),
            'alt'       => 'Turmeric powder in a wooden bowl',
            'cta'       => 'Shop Turmeric',
            'cta_url'   => route('shop.search.index', ['query' => 'turmeric']),
            'secondary' => 'Why organic',
            'secondary_url' => '#ab-why',
            'trust'     => ['Stone ground', 'No colour added', 'High curcumin'],
            'seal'      => '100%',
            'seal_small' => 'Natural',
            'seal_text' => 'PURE NATURE · ALWAYS · FRESH · ',
            'badge_icon' => 'ab-lab',
            'badge_title' => 'Lab tested',
            'badge_text' => 'Zero adulteration',
        ],
        [
            'tag_icon'  => 'ab-droplet',
            'tag'       => 'Cold Pressed',
            'bengali'   => 'প্রতি ফোঁটায় প্রকৃতির গুণ',
            'title_top' => 'Pure Mustard',
            'title_em'  => 'Oil',
            'text'      => 'Wood pressed from premium black mustard seeds in small batches. Strong aroma, golden colour, nothing mixed in.',
            'price'     => '৳520',
            'compare'   => '৳600',
            'chip'      => '1 litre bottle',
            'image'     => $asset('products/mustard-oil.jpg'),
            'alt'       => 'Cold pressed mustard oil in a glass bottle',
            'cta'       => 'Shop Mustard Oil',
            'cta_url'   => route('shop.search.index', ['query' => 'mustard oil']),
            'secondary' => 'Why cold pressed',
            'secondary_url' => '#ab-why',
            'trust'     => ['Wood pressed', 'Single origin', 'Good for heart & skin'],
            'seal'      => '0%',
            'seal_small' => 'Chemicals',
            'seal_text' => 'COLD PRESSED · PURE · HONEST · ',
            'badge_icon' => 'ab-heart',
            'badge_title' => 'Heart friendly',
            'badge_text' => 'Naturally cholesterol free',
        ],
    ];

    /* Category tiles — the icons cycle in this order. */
    $categoryIcons = ['ab-jar', 'ab-spice', 'ab-bottle', 'ab-honey', 'ab-wheat', 'ab-heart'];

    $categoryTiles = [];

    foreach (($categories ?? collect()) as $index => $category) {
        if ($index > 5) {
            break;
        }

        $categoryTiles[] = [
            'name'  => $category->name,
            'url'   => $category->url,
            'count' => $category->children?->count() ? $category->children->count() . ' sub categories' : 'Shop now',
            'icon'  => $categoryIcons[$index % count($categoryIcons)],
        ];
    }

    /* Shown when the catalogue has no categories yet, so the design stays intact. */
    if (! count($categoryTiles)) {
        $fallbackCategories = [
            ['name' => 'Ghee & Butter', 'icon' => 'ab-jar'],
            ['name' => 'Spices & Masala', 'icon' => 'ab-spice'],
            ['name' => 'Cold Pressed Oil', 'icon' => 'ab-bottle'],
            ['name' => 'Honey', 'icon' => 'ab-honey'],
            ['name' => 'Rice & Grains', 'icon' => 'ab-wheat'],
            ['name' => 'Gift Packs', 'icon' => 'ab-heart'],
        ];

        foreach ($fallbackCategories as $fallback) {
            $categoryTiles[] = [
                'name'  => $fallback['name'],
                'url'   => route('shop.search.index'),
                'count' => 'Coming soon',
                'icon'  => $fallback['icon'],
            ];
        }
    }

    $isPreview = ($preview ?? false) && ! empty($sections ?? null);
@endphp

@push('meta')
    <meta name="title" content="{{ $channel->home_seo['meta_title'] ?? '' }}">
    <meta name="description" content="{{ $channel->home_seo['meta_description'] ?? '' }}">
    <meta name="keywords" content="{{ $channel->home_seo['meta_keywords'] ?? '' }}">
@endPush

<x-shop::layouts :has-feature="false">
    <x-slot:title>
        {{ $channel->home_seo['meta_title'] ?? '' }}
    </x-slot>

    @if ($isPreview)
        {{-- Appearance → Sections preview: render the sections the admin is editing. --}}
        @foreach ($sections as $section)
            @php ($data = $section->options)
            @php ($marks = ($preview ?? false) && ! $section->getTypeInstance()?->rendersInLayout())

            @if ($marks)
                <div data-section-id="{{ $section->id }}" data-section-name="{{ $section->name }}">
            @endif

            @switch ($section->type)
                @case (\Webkul\Theme\Enums\SectionTypeEnum::IMAGE_CAROUSEL->value)
                    <x-shop::carousel
                        :options="$section->getTypeInstance()?->sanitize((array) $data) ?? $data"
                        aria-label="{{ trans('shop::app.home.index.image-carousel') }}"
                    />

                    @break
                @case (\Webkul\Theme\Enums\SectionTypeEnum::STATIC_CONTENT->value)
                    @if (! empty($data['css']))
                        @push('styles')
                            <style>{!! $data['css'] !!}</style>
                        @endpush
                    @endif

                    @if (! empty($data['html']))
                        {!! $data['html'] !!}
                    @endif

                    @break
                @case (\Webkul\Theme\Enums\SectionTypeEnum::CATEGORY_CAROUSEL->value)
                    <x-shop::categories.carousel
                        :title="$data['title'] ?? ''"
                        :src="route('shop.api.categories.index', $data['filters'] ?? [])"
                        :navigation-link="route('shop.home.index')"
                        aria-label="{{ trans('shop::app.home.index.categories-carousel') }}"
                    />

                    @break
                @case (\Webkul\Theme\Enums\SectionTypeEnum::PRODUCT_CAROUSEL->value)
                    <x-shop::products.carousel
                        :title="$data['title'] ?? ''"
                        :src="route('shop.api.products.index', $data['filters'] ?? [])"
                        :navigation-link="route('shop.search.index', $data['filters'] ?? [])"
                        aria-label="{{ trans('shop::app.home.index.product-carousel') }}"
                    />

                    @break
            @endswitch

            @if ($marks)
                </div>
            @endif
        @endforeach

        @include('shop::home.preview-bridge')
    @else

        {{-- ============================================================
             HERO
             ============================================================ --}}
        <section class="ab-hero" aria-label="Featured products" tabindex="0" data-ab-hero>
            <div class="ab-container">
                <div class="ab-hero__slider">
                    @foreach ($slides as $index => $slide)
                        <article class="ab-hero__slide {{ $index === 0 ? 'is-active' : '' }}" data-ab-slide>
                            <div class="ab-hero__copy">
                                <span class="ab-hero__tag">
                                    <svg width="14" height="14" aria-hidden="true"><use href="#{{ $slide['tag_icon'] }}"></use></svg>
                                    {{ $slide['tag'] }}
                                </span>

                                <p class="ab-hero__bengali">{{ $slide['bengali'] }}</p>

                                @if ($index === 0)
                                    <h1 class="ab-hero__title">
                                        {{ $slide['title_top'] }}<br><em>{{ $slide['title_em'] }}</em>
                                    </h1>
                                @else
                                    <h2 class="ab-hero__title">
                                        {{ $slide['title_top'] }}<br><em>{{ $slide['title_em'] }}</em>
                                    </h2>
                                @endif

                                <p class="ab-hero__text">{{ $slide['text'] }}</p>

                                <div class="ab-hero__price">
                                    <strong>{{ $slide['price'] }}</strong>
                                    <del>{{ $slide['compare'] }}</del>
                                    <span>{{ $slide['chip'] }}</span>
                                </div>

                                <div class="ab-hero__actions">
                                    <a href="{{ $slide['cta_url'] }}" class="ab-btn ab-btn--primary">
                                        {{ $slide['cta'] }}
                                        <svg width="17" height="17" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                                    </a>

                                    <a href="{{ $slide['secondary_url'] }}" class="ab-btn ab-btn--ghost">
                                        <svg width="15" height="15" aria-hidden="true"><use href="#ab-leaf"></use></svg>
                                        {{ $slide['secondary'] }}
                                    </a>
                                </div>

                                <ul class="ab-hero__trust">
                                    @foreach ($slide['trust'] as $item)
                                        <li>
                                            <svg width="15" height="15" aria-hidden="true"><use href="#ab-check-circle"></use></svg>
                                            {{ $item }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="ab-hero__media">
                                <div class="ab-hero__blob"></div>

                                <div class="ab-hero__frame">
                                    <img
                                        src="{{ $slide['image'] }}"
                                        alt="{{ $slide['alt'] }}"
                                        width="430"
                                        height="430"
                                        @if ($index > 0) loading="lazy" @endif
                                    >
                                </div>

                                <div class="ab-hero__seal" aria-hidden="true">
                                    <svg viewBox="0 0 120 120">
                                        <defs>
                                            <path id="abSealPath{{ $index }}" d="M60,60 m-44,0 a44,44 0 1,1 88,0 a44,44 0 1,1 -88,0"></path>
                                        </defs>
                                        <text><textPath href="#abSealPath{{ $index }}">{{ $slide['seal_text'] }}</textPath></text>
                                    </svg>

                                    <strong>
                                        {{ $slide['seal'] }}
                                        <small>{{ $slide['seal_small'] }}</small>
                                    </strong>
                                </div>

                                <div class="ab-hero__chip">
                                    <span class="ab-hero__chip-icon">
                                        <svg width="18" height="18" aria-hidden="true"><use href="#{{ $slide['badge_icon'] }}"></use></svg>
                                    </span>

                                    <div>
                                        <b>{{ $slide['badge_title'] }}</b>
                                        <span>{{ $slide['badge_text'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="ab-hero__controls">
                    <div class="ab-hero__dots" role="tablist" aria-label="Featured products">
                        @foreach ($slides as $index => $slide)
                            <button
                                class="ab-hero__dot {{ $index === 0 ? 'is-active' : '' }}"
                                type="button"
                                data-ab-dot
                                role="tab"
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="Slide {{ $index + 1 }}"
                            ></button>
                        @endforeach
                    </div>

                    <div class="ab-hero__arrows">
                        <button class="ab-hero__arrow" type="button" data-ab-hero-prev aria-label="Previous slide">
                            <svg width="18" height="18" aria-hidden="true"><use href="#ab-arrow-left"></use></svg>
                        </button>

                        <button class="ab-hero__arrow" type="button" data-ab-hero-next aria-label="Next slide">
                            <svg width="18" height="18" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- Service promises strip (sits right under the hero) --}}
        <x-shop::layouts.services />

        {{-- ============================================================
             CATEGORIES
             ============================================================ --}}
        <section class="ab-section" id="ab-categories">
            <div class="ab-container">
                <div class="ab-section__head" data-ab-reveal>
                    <div>
                        <span class="ab-eyebrow">Shop by category</span>
                        <h2 class="ab-title">Everything your <em>kitchen</em> needs</h2>
                    </div>

                    <a href="{{ route('shop.search.index') }}" class="ab-link-arrow">
                        Browse all products
                        <svg width="15" height="15" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                    </a>
                </div>

                <div class="ab-cats__grid">
                    @foreach ($categoryTiles as $index => $tile)
                        <a
                            href="{{ $tile['url'] }}"
                            class="ab-cat"
                            data-ab-reveal
                            style="--ab-delay: {{ $index * 60 }}ms"
                        >
                            <span class="ab-cat__icon">
                                <svg width="28" height="28" aria-hidden="true"><use href="#{{ $tile['icon'] }}"></use></svg>
                            </span>

                            <b>{{ $tile['name'] }}</b>
                            <span>{{ $tile['count'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ============================================================
             BEST SELLERS
             ============================================================ --}}
        <section class="ab-section ab-products" id="ab-best">
            <div class="ab-container">
                <x-shop::products.carousel
                    title="Our best sellers"
                    eyebrow="Customer favourites"
                    subtitle="Loved by thousands of families across Bangladesh."
                    :src="route('shop.api.products.index')"
                    :navigation-link="route('shop.search.index')"
                    aria-label="{{ trans('shop::app.home.index.product-carousel') }}"
                />
            </div>
        </section>

        {{-- ============================================================
             PROMO BANNERS
             ============================================================ --}}
        <section class="ab-section ab-section--tight">
            <div class="ab-container">
                <div class="ab-duo">
                    <article class="ab-promo" data-ab-reveal>
                        <img src="{{ $asset('products/turmeric.jpg') }}" alt="" loading="lazy" width="800" height="500">

                        <span class="ab-promo__kick">Pure &amp; natural</span>
                        <h3>Turmeric powder, straight from the root</h3>
                        <p>Stone ground, sun dried and packed within 48 hours of harvest.</p>

                        <a href="{{ route('shop.search.index', ['query' => 'turmeric']) }}" class="ab-btn ab-btn--light">
                            Shop spices
                            <svg width="15" height="15" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                        </a>
                    </article>

                    <article class="ab-promo ab-promo--gold" data-ab-reveal style="--ab-delay: 100ms">
                        <img src="{{ $asset('products/mustard-oil.jpg') }}" alt="" loading="lazy" width="800" height="500">

                        <span class="ab-promo__kick">Cold pressed</span>
                        <h3>Goodness of nature in every drop</h3>
                        <p>Wood pressed mustard oil in small batches — strong aroma, no refining.</p>

                        <a href="{{ route('shop.search.index', ['query' => 'mustard oil']) }}" class="ab-btn ab-btn--light">
                            Shop oils
                            <svg width="15" height="15" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                        </a>
                    </article>
                </div>
            </div>
        </section>

        {{-- ============================================================
             NEW ARRIVALS
             ============================================================ --}}
        <section class="ab-section ab-section--tight">
            <div class="ab-container">
                <x-shop::products.carousel
                    title="Fresh in the basket"
                    eyebrow="New arrivals"
                    subtitle="New batches land every week."
                    :src="route('shop.api.products.index', ['sort' => 'created_at-desc'])"
                    :navigation-link="route('shop.search.index', ['sort' => 'created_at-desc'])"
                    aria-label="{{ trans('shop::app.home.index.product-carousel') }}"
                />
            </div>
        </section>

        {{-- ============================================================
             STORY
             ============================================================ --}}
        <section class="ab-section" id="ab-story">
            <div class="ab-container">
                <div class="ab-story">
                    <div class="ab-story__media" data-ab-reveal>
                        <svg class="ab-story__leaf" viewBox="0 0 24 24" aria-hidden="true"><use href="#ab-leaf"></use></svg>

                        <img
                            class="ab-story__img"
                            src="{{ $asset('products/ghee.jpg') }}"
                            alt="Gawa ghee made in small batches"
                            loading="lazy"
                            width="600"
                            height="660"
                        >

                        <div class="ab-story__badge">
                            <b>12+</b>
                            <span>Years of family tradition</span>
                        </div>
                    </div>

                    <div data-ab-reveal style="--ab-delay: 120ms">
                        <span class="ab-eyebrow">Our story</span>

                        <h2 class="ab-title">
                            From our village kitchen<br>
                            to <em>your family table</em>
                        </h2>

                        <p class="ab-lead" style="margin-top: 16px;">
                            AponBasket started with one simple belief — food should be exactly what nature made it.
                            We work directly with small farms and family kitchens in Bangladesh, so nothing is
                            processed, diluted or hurried. What you taste is what the village tastes.
                        </p>

                        <ul class="ab-checklist">
                            <li>
                                <svg width="18" height="18" aria-hidden="true"><use href="#ab-check-circle"></use></svg>
                                <span><b>Sourced from real farmers</b> — no middlemen, fair prices, traceable batches.</span>
                            </li>
                            <li>
                                <svg width="18" height="18" aria-hidden="true"><use href="#ab-check-circle"></use></svg>
                                <span><b>Made in small batches</b> — churned and ground the traditional way.</span>
                            </li>
                            <li>
                                <svg width="18" height="18" aria-hidden="true"><use href="#ab-check-circle"></use></svg>
                                <span><b>Nothing added, ever</b> — no colour, no preservatives, no chemicals.</span>
                            </li>
                        </ul>

                        <div class="ab-stats">
                            <div>
                                <b>18k+</b>
                                <span>Happy families</span>
                            </div>
                            <div>
                                <b>100%</b>
                                <span>Natural products</span>
                            </div>
                            <div>
                                <b>4.9/5</b>
                                <span>Average rating</span>
                            </div>
                        </div>

                        <div class="ab-hero__actions">
                            <a href="{{ url('about-us') }}" class="ab-btn ab-btn--primary">
                                Read our story
                                <svg width="16" height="16" aria-hidden="true"><use href="#ab-arrow-right"></use></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============================================================
             WHY APONBASKET
             ============================================================ --}}
        <section class="ab-section ab-why" id="ab-why">
            <div class="ab-container">
                <div class="ab-section__head ab-section__head--center" data-ab-reveal>
                    <span class="ab-eyebrow ab-eyebrow--center">Why AponBasket</span>
                    <h2 class="ab-title">Pure nutrition<br>for <em>your family</em></h2>
                </div>

                <div class="ab-why__grid">
                    <article class="ab-why__card" data-ab-reveal>
                        <span class="ab-why__icon">
                            <svg width="23" height="23" aria-hidden="true"><use href="#ab-leaf"></use></svg>
                        </span>
                        <h3>100% Natural</h3>
                        <p>Every product is single ingredient — nothing blended, nothing diluted, nothing artificial.</p>
                    </article>

                    <article class="ab-why__card" data-ab-reveal style="--ab-delay: 80ms">
                        <span class="ab-why__icon">
                            <svg width="23" height="23" aria-hidden="true"><use href="#ab-lab"></use></svg>
                        </span>
                        <h3>No Chemicals</h3>
                        <p>Batch tested for adulteration before packing. If it is not clean, it does not ship.</p>
                    </article>

                    <article class="ab-why__card" data-ab-reveal style="--ab-delay: 160ms">
                        <span class="ab-why__icon">
                            <svg width="23" height="23" aria-hidden="true"><use href="#ab-shield"></use></svg>
                        </span>
                        <h3>No Preservatives</h3>
                        <p>Made fresh in small batches so it reaches you the way it left the kitchen.</p>
                    </article>

                    <article class="ab-why__card" data-ab-reveal style="--ab-delay: 240ms">
                        <span class="ab-why__icon">
                            <svg width="23" height="23" aria-hidden="true"><use href="#ab-heart"></use></svg>
                        </span>
                        <h3>Good for Health</h3>
                        <p>Traditional fats, real spices and raw honey — nutrition your grandparents trusted.</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- ============================================================
             STEPS
             ============================================================ --}}
        <section class="ab-section">
            <div class="ab-container">
                <div class="ab-section__head ab-section__head--center" data-ab-reveal>
                    <span class="ab-eyebrow ab-eyebrow--center">How it works</span>
                    <h2 class="ab-title">From the farm to <em>your door</em></h2>
                </div>

                <div class="ab-steps__grid">
                    <article class="ab-step" data-ab-reveal>
                        <span class="ab-step__icon">
                            <svg width="26" height="26" aria-hidden="true"><use href="#ab-wheat"></use></svg>
                        </span>
                        <h3>Sourced from farmers</h3>
                        <p>We buy directly from growers in Satkhira, Bogura and Rajshahi at fair prices.</p>
                    </article>

                    <article class="ab-step" data-ab-reveal style="--ab-delay: 80ms">
                        <span class="ab-step__icon">
                            <svg width="26" height="26" aria-hidden="true"><use href="#ab-jar"></use></svg>
                        </span>
                        <h3>Prepared traditionally</h3>
                        <p>Small batch churning, stone grinding and cold pressing — no shortcuts.</p>
                    </article>

                    <article class="ab-step" data-ab-reveal style="--ab-delay: 160ms">
                        <span class="ab-step__icon">
                            <svg width="26" height="26" aria-hidden="true"><use href="#ab-truck"></use></svg>
                        </span>
                        <h3>Delivered fresh</h3>
                        <p>Packed in food grade glass and shipped within 1–3 days, cash on delivery.</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- ============================================================
             TESTIMONIALS
             ============================================================ --}}
        <section class="ab-section ab-testi">
            <div class="ab-container">
                <div class="ab-section__head" data-ab-reveal>
                    <div>
                        <span class="ab-eyebrow">Real customers</span>
                        <h2 class="ab-title">What families <em>say about us</em></h2>
                    </div>

                    <div style="text-align: right;">
                        <div class="ab-testi__stars" aria-label="Rated 4.9 out of 5">
                            @for ($i = 0; $i < 5; $i++)
                                <svg width="16" height="16" aria-hidden="true"><use href="#ab-star"></use></svg>
                            @endfor
                        </div>

                        <p style="font-size: 12.5px; color: #6c7b71; margin-top: 4px;">
                            4.9 average from 1,240+ orders
                        </p>
                    </div>
                </div>

                <div class="ab-testi__grid">
                    <article class="ab-testi__card" data-ab-reveal>
                        <svg width="26" height="26" style="color: #d9a441" aria-hidden="true"><use href="#ab-quote"></use></svg>

                        <p class="ab-testi__quote">
                            “The ghee smells exactly like the one my mother used to make in our village.
                            My kids finished the first jar in two weeks — ordering the 1 kg pack now.”
                        </p>

                        <div class="ab-testi__person">
                            <span class="ab-testi__avatar">SR</span>
                            <div>
                                <b>Sadia Rahman</b>
                                <span>Dhanmondi, Dhaka</span>
                            </div>
                        </div>
                    </article>

                    <article class="ab-testi__card" data-ab-reveal style="--ab-delay: 80ms">
                        <svg width="26" height="26" style="color: #d9a441" aria-hidden="true"><use href="#ab-quote"></use></svg>

                        <p class="ab-testi__quote">
                            “I ordered turmeric and chili powder together. The colour is natural, not the neon
                            stuff you get in the market. You can actually smell the raw spice.”
                        </p>

                        <div class="ab-testi__person">
                            <span class="ab-testi__avatar">MH</span>
                            <div>
                                <b>Mahmudul Hasan</b>
                                <span>Uttara, Dhaka</span>
                            </div>
                        </div>
                    </article>

                    <article class="ab-testi__card" data-ab-reveal style="--ab-delay: 160ms">
                        <svg width="26" height="26" style="color: #d9a441" aria-hidden="true"><use href="#ab-quote"></use></svg>

                        <p class="ab-testi__quote">
                            “Mustard oil with real pungency — my mother-in-law approved, which is the highest
                            rating possible in this house. Delivery was quick and packaging was solid.”
                        </p>

                        <div class="ab-testi__person">
                            <span class="ab-testi__avatar">NA</span>
                            <div>
                                <b>Nusrat Ara</b>
                                <span>Chattogram</span>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        {{-- ============================================================
             TRUST BAND
             ============================================================ --}}
        <section class="ab-band" aria-hidden="true">
            <div class="ab-band__track">
                @for ($i = 0; $i < 2; $i++)
                    <span><svg width="17" height="17"><use href="#ab-leaf"></use></svg> 100% Natural</span>
                    <span><svg width="17" height="17"><use href="#ab-spark"></use></svg> No Added Chemicals</span>
                    <span><svg width="17" height="17"><use href="#ab-shield"></use></svg> No Preservatives</span>
                    <span><svg width="17" height="17"><use href="#ab-heart"></use></svg> Good for Health</span>
                @endfor
            </div>
        </section>

        {{-- ============================================================
             NEWSLETTER CTA
             ============================================================ --}}
        <section class="ab-section">
            <div class="ab-container">
                <div class="ab-cta" data-ab-reveal>
                    <div>
                        <h2>Get 10% off your first order</h2>

                        <p>
                            Join the AponBasket family for seasonal harvests, kitchen recipes and early
                            access to limited batches.
                        </p>

                        <div style="display: flex; align-items: center; gap: 10px; margin-top: 18px; flex-wrap: wrap;">
                            <span style="font-size: 12px; color: #bcd4b4;">Use code</span>

                            <button type="button" class="ab-coupon" data-ab-copy="ORGANIC10">
                                ORGANIC10
                                <svg width="14" height="14" aria-hidden="true"><use href="#ab-spark"></use></svg>
                            </button>
                        </div>
                    </div>

                    @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                        <form class="ab-news-form" method="POST" action="{{ route('shop.subscription.store') }}">
                            @csrf

                            <label for="ab-home-email" class="sr-only">Email address</label>

                            <input
                                id="ab-home-email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="@lang('shop::app.components.layouts.footer.email')"
                                autocomplete="email"
                                required
                            >

                            <button type="submit">Subscribe</button>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    @endif
</x-shop::layouts>
