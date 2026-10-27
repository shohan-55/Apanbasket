{{--
    Service promises strip, shown above the footer on every page.
    Edit the $services array to change the four promises.

    Note: Bagisto's "Services Content" section (Appearance → Sections) is not
    used by this theme, so the strip is defined here to keep the design consistent.
--}}
@php
    $services = [
        [
            'icon'        => 'ab-leaf',
            'title'       => '100% Natural',
            'description' => 'Straight from the farm',
        ],
        [
            'icon'        => 'ab-lab',
            'title'       => 'No Chemicals',
            'description' => 'Zero additives, zero colour',
        ],
        [
            'icon'        => 'ab-truck',
            'title'       => 'Fast Delivery',
            'description' => '1–3 days across Bangladesh',
        ],
        [
            'icon'        => 'ab-cash',
            'title'       => 'Cash on Delivery',
            'description' => 'Pay when it reaches you',
        ],
    ];
@endphp

{!! view_render_event('bagisto.shop.layout.features.before') !!}

<section class="ab-usp" aria-label="Our promises">
    <div class="ab-container">
        <div class="ab-usp__grid">
            @foreach ($services as $service)
                <div class="ab-usp__item">
                    <span class="ab-usp__icon">
                        <svg width="22" height="22" aria-hidden="true"><use href="#{{ $service['icon'] }}"></use></svg>
                    </span>

                    <div>
                        <b>{{ $service['title'] }}</b>
                        <span>{{ $service['description'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{!! view_render_event('bagisto.shop.layout.features.after') !!}
