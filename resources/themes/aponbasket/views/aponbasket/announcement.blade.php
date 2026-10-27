{{--
    Announcement bar — the strip above the header.
    Edit the $offers array to change the rotating messages.
--}}
@php
    $offers = [
        ['icon' => 'ab-truck', 'html' => 'Free delivery inside Dhaka on orders over <strong>৳1,500</strong>'],
        ['icon' => 'ab-leaf', 'html' => '100% natural — no chemicals, no preservatives'],
        ['icon' => 'ab-cash', 'html' => 'Cash on delivery available all over Bangladesh'],
    ];

    /* Set to null to hide the phone number on the right side. */
    $supportPhone = '+8801700000000';
    $supportLabel = '+880 1700 000 000';

    $headerOffer = core()->getConfigData('general.content.header_offer.title');

    if (filled($headerOffer)) {
        $offers[] = ['icon' => 'ab-spark', 'html' => e($headerOffer)];
    }
@endphp

<div class="ab-announce" data-ab-announce>
    <div class="ab-announce__inner">
        <div class="ab-announce__marquee">
            <div class="ab-announce__track">
                @foreach (array_merge($offers, $offers) as $offer)
                    <span class="ab-announce__item">
                        <svg width="15" height="15" aria-hidden="true"><use href="#{{ $offer['icon'] }}"></use></svg>
                        <span>{!! $offer['html'] !!}</span>
                    </span>
                @endforeach
            </div>
        </div>

        <div class="ab-announce__side">
            @if ($supportPhone)
                <a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}">
                    <svg width="14" height="14" aria-hidden="true"><use href="#ab-phone"></use></svg>
                    {{ $supportLabel }}
                </a>
            @endif

            <button
                class="ab-announce__close"
                type="button"
                data-ab-announce-close
                aria-label="Dismiss announcement"
            >
                <svg width="13" height="13" aria-hidden="true"><use href="#ab-close"></use></svg>
            </button>
        </div>
    </div>
</div>
