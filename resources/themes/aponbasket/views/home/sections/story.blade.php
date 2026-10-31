@php
    $link = \App\AponBasket\Theme\Support\Urls::link($data['link_url'] ?? '');
    $image = \App\AponBasket\Theme\Support\Urls::image($data['image'] ?? '');
@endphp
<section class="ab-story-section" v-pre>
    <div class="ab-story-section__visual">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $data['image_alt'] ?? '' }}" loading="lazy">
        @endif
        <div class="ab-story-stamp"><span aria-hidden="true">✳</span><strong>{{ $data['stamp_first'] ?? '' }}<br>{{ $data['stamp_second'] ?? '' }}</strong><small>{{ $data['stamp_caption'] ?? '' }}</small></div>
    </div>
    <div class="ab-story-section__copy">
        <span class="ab-kicker">{{ $data['kicker'] ?? '' }}</span>
        <h2>{{ $data['title_start'] ?? '' }} <em>{{ $data['title_highlight'] ?? '' }}</em></h2>
        <p>{{ $data['description'] ?? '' }}</p>
        <div class="ab-story-points">
            @foreach (is_array($data['points'] ?? null) ? $data['points'] : [] as $point)
                @continue (! is_array($point))
                <span><i aria-hidden="true">✓</i> {{ $point['text'] ?? '' }}</span>
            @endforeach
        </div>
        @if ($link && ! empty($data['link_text']))
            <a class="ab-button ab-button--primary" href="{{ $link }}">{{ $data['link_text'] }} <span aria-hidden="true">↗</span></a>
        @endif
    </div>
</section>
