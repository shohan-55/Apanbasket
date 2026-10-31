{{-- Keep Bagisto's channel/locale-aware copyright setting authoritative. --}}
@php($copyrightContent = core()->getConfigData('general.content.footer.copyright_content'))

<span v-pre>
    @if ($copyrightContent)
        {!! clean_content((string) $copyrightContent) !!}
    @else
        © {{ date('Y') }} AponBasket. All rights reserved. <span aria-hidden="true">|</span> Developed by DhakaWala Developer Team
    @endif
</span>
