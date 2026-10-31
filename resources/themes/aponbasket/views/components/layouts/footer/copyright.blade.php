{{-- Keep Bagisto's channel/locale-aware copyright setting authoritative. --}}
@php($copyrightContent = core()->getConfigData('general.content.footer.copyright_content'))

<span v-pre>
    @if ($copyrightContent)
        {!! clean_content((string) $copyrightContent) !!}
    @else
        © {{ date('Y') }} AponBasket. সর্বস্বত্ব সংরক্ষিত। <span aria-hidden="true">|</span> ডিজাইন: ঢাকা ওলা
    @endif
</span>
