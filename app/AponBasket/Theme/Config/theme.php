<?php

use App\AponBasket\Theme\Sections\Announcement;
use App\AponBasket\Theme\Sections\Benefits;
use App\AponBasket\Theme\Sections\CallToAction;
use App\AponBasket\Theme\Sections\Categories;
use App\AponBasket\Theme\Sections\Hero;
use App\AponBasket\Theme\Sections\Products;
use App\AponBasket\Theme\Sections\Promos;
use App\AponBasket\Theme\Sections\Services;
use App\AponBasket\Theme\Sections\Story;
use Webkul\Theme\Enums\SectionTypeEnum;

return [
    'name' => 'AponBasket Organic',
    'assets_path' => 'public/themes/shop/aponbasket',
    'views_path' => 'resources/themes/aponbasket/views',
    'parent' => 'default',
    // Reuse the installed Bagisto shop build rather than duplicating its JS or checkout.
    'vite' => [
        'hot_file' => 'shop-default-vite.hot',
        'build_directory' => 'themes/shop/default/build',
        'package_assets_directory' => 'src/Resources/assets',
    ],
    'customize' => [
        'sections' => [
            Announcement::class,
            Hero::class,
            Benefits::class,
            Promos::class,
            Categories::class,
            Products::class,
            Story::class,
            CallToAction::class,
            Services::class,
            SectionTypeEnum::IMAGE_CAROUSEL,
            SectionTypeEnum::PRODUCT_CAROUSEL,
            SectionTypeEnum::CATEGORY_CAROUSEL,
            SectionTypeEnum::FOOTER_LINKS,
            SectionTypeEnum::STATIC_CONTENT,
        ],
    ],
];
