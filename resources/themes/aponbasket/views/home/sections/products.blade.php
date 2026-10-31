@php($link = \App\AponBasket\Theme\Support\Urls::link($data['link_url'] ?? ''))
<section class="ab-product-section" aria-label="Featured products">
    <div class="ab-product-section__top" v-pre>
        <div><span class="ab-kicker">{{ $data['kicker'] ?? '' }}</span><h2>{{ $data['title_start'] ?? '' }} <em>{{ $data['title_highlight'] ?? '' }}</em></h2><p>{{ $data['description'] ?? '' }}</p></div>
        @if ($link && ! empty($data['link_text']))
            <a class="ab-button ab-button--outline" href="{{ $link }}">{{ $data['link_text'] }} <span aria-hidden="true">→</span></a>
        @endif
    </div>
    <x-shop::products.carousel
        title=""
        :src="route('shop.api.products.index', $data['filters'] ?? [])"
        :navigation-link="route('shop.search.index', $data['filters'] ?? [])"
        aria-label="Featured AponBasket products"
    />
</section>
