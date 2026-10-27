{{--
    Floating actions. Change $whatsapp to your store number (digits only,
    with the country code, no plus sign) or set it to null to hide the button.
--}}
@php
    $whatsapp = '8801700000000';
@endphp

<div class="ab-fabs">
    @if ($whatsapp)
        <a
            class="ab-fab ab-fab--wa"
            href="https://wa.me/{{ $whatsapp }}"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="Chat with AponBasket on WhatsApp"
        >
            <svg width="26" height="26" aria-hidden="true"><use href="#ab-whatsapp"></use></svg>
        </a>
    @endif

    <button
        class="ab-fab ab-fab--top"
        type="button"
        data-ab-top
        aria-label="Back to top"
    >
        <svg width="20" height="20" aria-hidden="true"><use href="#ab-arrow-up"></use></svg>
    </button>
</div>
