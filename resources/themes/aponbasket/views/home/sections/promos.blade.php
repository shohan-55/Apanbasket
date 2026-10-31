@php
    $link = \App\AponBasket\Theme\Support\Urls::link($data['link_url'] ?? '');
@endphp
<section class="ab-section ab-category-shortcuts" aria-label="{{ $data['title_start'] ?? '' }}" v-pre>
    <div class="ab-section-heading">
        <div><span class="ab-kicker">{{ $data['kicker'] ?? '' }}</span><h2>{{ $data['title_start'] ?? '' }} <em>{{ $data['title_highlight'] ?? '' }}</em></h2><p>{{ $data['description'] ?? '' }}</p></div>
        @if ($link && ! empty($data['link_text']))
            <a class="ab-text-link" href="{{ $link }}" aria-label="{{ $data['link_text'] }}">{{ $data['link_text'] }} →</a>
        @endif
    </div>
    <div class="ab-promo-grid">
        @foreach (is_array($data['items'] ?? null) ? $data['items'] : [] as $item)
            @continue (! is_array($item))
            @php
                $cardLink = \App\AponBasket\Theme\Support\Urls::link($item['url'] ?? '');
                $image = \App\AponBasket\Theme\Support\Urls::image($item['image'] ?? '');
            @endphp
            <a class="ab-promo-card" @if ($cardLink) href="{{ $cardLink }}" @else aria-disabled="true" @endif>
                @if ($image)
                    <img src="{{ $image }}" alt="{{ $item['image_alt'] ?? '' }}" loading="lazy">
                @endif
                <span class="ab-promo-card__shade"></span><span class="ab-promo-card__copy"><small>{{ $item['kicker'] ?? '' }}</small><strong>{{ $item['title'] ?? '' }}</strong><span>{{ $item['link_text'] ?? '' }} ↗</span></span>
            </a>
        @endforeach
    </div>
</section>
