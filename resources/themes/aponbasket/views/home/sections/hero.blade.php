@php
    $primary = \App\AponBasket\Theme\Support\Urls::link($data['primary_url'] ?? '');
    $secondary = \App\AponBasket\Theme\Support\Urls::link($data['secondary_url'] ?? '');
    $image = \App\AponBasket\Theme\Support\Urls::image($data['image'] ?? '');
@endphp
<section class="ab-hero" aria-label="{{ $data['title_start'] ?? '' }}" v-pre>
    <div class="ab-hero__copy">
        <span class="ab-eyebrow"><span class="ab-eyebrow__dot"></span> {{ $data['eyebrow'] ?? '' }}</span>
        <h1>{{ $data['title_start'] ?? '' }}<br><em>{{ $data['title_highlight'] ?? '' }}</em> {{ $data['title_end'] ?? '' }}</h1>
        <p class="ab-hero__description">{{ $data['description'] ?? '' }}</p>
        <div class="ab-hero__actions">
            @if ($primary && ! empty($data['primary_text']))
                <a class="ab-button ab-button--primary" href="{{ $primary }}">{{ $data['primary_text'] }} <span aria-hidden="true">↗</span></a>
            @endif
            @if ($secondary && ! empty($data['secondary_text']))
                <a class="ab-button ab-button--quiet" href="{{ $secondary }}">{{ $data['secondary_text'] }} <span aria-hidden="true">↓</span></a>
            @endif
        </div>
        <div class="ab-hero__proof">
            <div class="ab-avatars" aria-hidden="true"><span>আ</span><span>প</span><span>ন</span></div>
            <div><strong>{{ $data['proof_title'] ?? '' }}</strong><small>{{ $data['proof_subtitle'] ?? '' }}</small></div>
        </div>
    </div>
    <div class="ab-hero__art">
        <div class="ab-hero__halo"></div>
        <div class="ab-hero__image-frame">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $data['image_alt'] ?? '' }}" fetchpriority="high">
            @endif
        </div>
        <div class="ab-floating-note ab-floating-note--top"><span class="ab-leaf-icon" aria-hidden="true">✳</span><span><strong>{{ $data['note_top_title'] ?? '' }}</strong><small>{{ $data['note_top_subtitle'] ?? '' }}</small></span></div>
        <div class="ab-floating-note ab-floating-note--bottom"><span class="ab-check-icon" aria-hidden="true">✓</span><span><strong>{{ $data['note_bottom_title'] ?? '' }}</strong><small>{{ $data['note_bottom_subtitle'] ?? '' }}</small></span></div>
        <span class="ab-deco-leaf ab-deco-leaf--one" aria-hidden="true">❧</span>
        <span class="ab-deco-leaf ab-deco-leaf--two" aria-hidden="true">❧</span>
    </div>
</section>
