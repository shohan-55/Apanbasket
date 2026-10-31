<?php

namespace App\AponBasket\Theme\Sections;

use Webkul\Theme\Sections\CategoryCarousel;

final class Categories extends CategoryCarousel
{
    use HandlesOptions;

    protected string $code = 'apon_categories';

    protected ?string $title = null;

    public function getTitle(): string
    {
        return 'AponBasket · ক্যাটাগরি ক্যারোসেল';
    }

    public function getFields(): array
    {
        return [
            Fields::text('kicker', 'ছোট উপশিরোনাম'),
            Fields::text('title_start', 'শিরোনাম'),
            Fields::text('title_highlight', 'রঙিন শিরোনাম'),
            Fields::text('link_text', 'লিংকের লেখা'),
            Fields::text('link_url', 'লিংকের ঠিকানা'),
            parent::getFields()[0],
        ];
    }
}
