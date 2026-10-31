{!! view_render_event('bagisto.shop.layout.features.before') !!}

@inject('sectionRepository', 'Webkul\Theme\Repositories\SectionRepository')
@php
    $channel = core()->getCurrentChannel();
    $serviceSections = $sectionRepository->findAllOfType('apon_services', $channel->id, $channel->theme, app()->getLocale());
@endphp

@foreach ($serviceSections as $section)
    @php($data = $section->getTypeInstance()?->sanitize((array) $section->options) ?? [])
    <section class="ab-global-services" aria-label="AponBasket shopping information" v-pre
        @if ($sectionRepository->isPreviewing())
            data-section-id="{{ $section->id }}" data-section-name="{{ $section->name }}"
        @endif
    >
        <div class="ab-global-services__inner">
            @foreach (is_array($data['items'] ?? null) ? $data['items'] : [] as $item)
                @continue (! is_array($item))
                <div class="ab-global-service">
                    <span aria-hidden="true">{{ $item['symbol'] ?? '' }}</span>
                    <div><strong>{{ $item['title'] ?? '' }}</strong><small>{{ $item['subtitle'] ?? '' }}</small></div>
                </div>
            @endforeach
        </div>
    </section>
@endforeach

{!! view_render_event('bagisto.shop.layout.features.after') !!}
