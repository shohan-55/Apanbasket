@php
    $channel = core()->getCurrentChannel();
    $seo = $channel->home_seo ?? [];
    $title = ($seo['meta_title'] ?? '') ?: 'AponBasket | Natural & Organic Products';
    $description = ($seo['meta_description'] ?? '') ?: 'খাঁটি ঘি, সরিষার তেল ও বিশুদ্ধ মসলা—AponBasket থেকে প্রকৃতির কাছের খাবার আপনার ঘরে।';
    $keywords = ($seo['meta_keywords'] ?? '') ?: 'AponBasket, natural products, organic food, ghee, mustard oil, turmeric, chili powder, Bangladesh';
    // Only these shipped templates can be selected by a database section type.
    $aponViews = [
        'apon_announcement' => 'shop::home.sections.announcement',
        'apon_hero' => 'shop::home.sections.hero',
        'apon_benefits' => 'shop::home.sections.benefits',
        'apon_promos' => 'shop::home.sections.promos',
        'apon_categories' => 'shop::home.sections.categories',
        'apon_products' => 'shop::home.sections.products',
        'apon_story' => 'shop::home.sections.story',
        'apon_cta' => 'shop::home.sections.cta',
    ];
@endphp

@push('meta')
    <meta name="title" content="{{ $title }}" />
    <meta name="description" content="{{ $description }}" />
    <meta name="keywords" content="{{ $keywords }}" />
@endpush

@push('scripts')
    @if (! empty($categories))
        <script>localStorage.setItem('categories', JSON.stringify(@json($categories)));</script>
    @endif
@endpush

<x-shop::layouts>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="ab-storefront">
        @foreach ($sections as $section)
            @php
                $type = $section->getTypeInstance();
                $data = $type?->sanitize((array) $section->options) ?? (array) $section->options;
                $marks = ($preview ?? false) && ! $type?->rendersInLayout();
            @endphp

            @if ($marks)
                <div data-section-id="{{ $section->id }}" data-section-name="{{ $section->name }}">
            @endif

            @if (isset($aponViews[$section->type]))
                @include($aponViews[$section->type], ['data' => $data])
            @else
                {{-- The native Bagisto types remain available alongside the designed sections. --}}
                @switch ($section->type)
                    @case (\Webkul\Theme\Enums\SectionTypeEnum::IMAGE_CAROUSEL->value)
                        <x-shop::carousel :options="$data" aria-label="{{ trans('shop::app.home.index.image-carousel') }}" />
                        @break
                    @case (\Webkul\Theme\Enums\SectionTypeEnum::STATIC_CONTENT->value)
                        @if (! empty($data['css']))
                            @push('styles')
                                <style>{!! function_exists('bagisto_theme_storage') ? bagisto_theme_storage()->resolveMarkup($data['css']) : $data['css'] !!}</style>
                            @endpush
                        @endif
                        @if (! empty($data['html']))
                            {!! function_exists('bagisto_theme_storage') ? bagisto_theme_storage()->resolveMarkup($data['html']) : $data['html'] !!}
                        @endif
                        @break
                    @case (\Webkul\Theme\Enums\SectionTypeEnum::CATEGORY_CAROUSEL->value)
                        <x-shop::categories.carousel
                            :title="$data['title'] ?? ''"
                            :src="route('shop.api.categories.index', $data['filters'] ?? [])"
                            :navigation-link="route('shop.home.index')"
                            aria-label="{{ trans('shop::app.home.index.categories-carousel') }}"
                        />
                        @break
                    @case (\Webkul\Theme\Enums\SectionTypeEnum::PRODUCT_CAROUSEL->value)
                        <x-shop::products.carousel
                            :title="$data['title'] ?? ''"
                            :src="route('shop.api.products.index', $data['filters'] ?? [])"
                            :navigation-link="route('shop.search.index', $data['filters'] ?? [])"
                            aria-label="{{ trans('shop::app.home.index.product-carousel') }}"
                        />
                        @break
                @endswitch
            @endif

            @if ($marks)
                </div>
            @endif
        @endforeach

        @if ($preview ?? false)
            @include('shop::home.preview-bridge')
        @endif
    </div>
</x-shop::layouts>
