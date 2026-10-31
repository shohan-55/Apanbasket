@php($link = \App\AponBasket\Theme\Support\Urls::link($data['link_url'] ?? ''))
<section class="ab-bottom-cta" v-pre>
    <div><span class="ab-bottom-cta__spark" aria-hidden="true">✦</span><span class="ab-kicker">{{ $data['kicker'] ?? '' }}</span><h2>{{ $data['title_start'] ?? '' }} <em>{{ $data['title_highlight'] ?? '' }}</em></h2><p>{{ $data['description'] ?? '' }}</p></div>
    @if ($link && ! empty($data['link_text']))
        <a class="ab-button ab-button--light" href="{{ $link }}">{{ $data['link_text'] }} <span aria-hidden="true">→</span></a>
    @endif
</section>
