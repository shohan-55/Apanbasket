@php($link = \App\AponBasket\Theme\Support\Urls::link($data['link_url'] ?? ''))
<section class="ab-section ab-live-categories" aria-label="Browse product categories">
    <div class="ab-section-heading" v-pre>
        <div><span class="ab-kicker">{{ $data['kicker'] ?? '' }}</span><h2>{{ $data['title_start'] ?? '' }} <em>{{ $data['title_highlight'] ?? '' }}</em></h2></div>
        @if ($link && ! empty($data['link_text']))
            <a class="ab-text-link" href="{{ $link }}">{{ $data['link_text'] }} <span aria-hidden="true">→</span></a>
        @endif
    </div>
    <x-shop::categories.carousel
        title=""
        :src="route('shop.api.categories.index', $data['filters'] ?? [])"
        :navigation-link="route('shop.search.index')"
        aria-label="AponBasket product categories"
    />
</section>
