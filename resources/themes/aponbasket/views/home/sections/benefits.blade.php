<section class="ab-benefit-strip" id="ab-our-promise" aria-label="AponBasket promises" v-pre>
    @foreach (is_array($data['items'] ?? null) ? $data['items'] : [] as $item)
        @continue (! is_array($item))
        <div class="ab-benefit"><span class="ab-benefit__icon" aria-hidden="true">{{ $item['symbol'] ?? '' }}</span><div><strong>{{ $item['title'] ?? '' }}</strong><small>{{ $item['subtitle'] ?? '' }}</small></div></div>
    @endforeach
</section>
