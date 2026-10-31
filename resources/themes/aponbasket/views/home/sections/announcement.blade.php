@php($link = \App\AponBasket\Theme\Support\Urls::link($data['link_url'] ?? ''))
<div class="ab-announcement" v-pre>
    <span class="ab-announcement__leaf" aria-hidden="true">✦</span>
    {{ $data['text'] ?? '' }}
    @if ($link && ! empty($data['link_text']))
        <a href="{{ $link }}">{{ $data['link_text'] }} <span aria-hidden="true">→</span></a>
    @endif
</div>
