<?php

namespace App\AponBasket\Theme\Sections;

use Webkul\Theme\Sections\ProductCarousel;

final class Products extends ProductCarousel
{
    use HandlesOptions;

    protected string $code = 'apon_products';

    protected ?string $title = null;

    public function getTitle(): string
    {
        return 'AponBasket · পণ্যের ক্যারোসেল';
    }

    public function getFields(): array
    {
        return [
            Fields::text('kicker', 'ছোট উপশিরোনাম'),
            Fields::text('title_start', 'শিরোনাম'),
            Fields::text('title_highlight', 'রঙিন শিরোনাম'),
            Fields::textarea('description', 'বিবরণ'),
            Fields::text('link_text', 'বাটনের লেখা'),
            Fields::text('link_url', 'বাটনের লিংক'),
            parent::getFields()[1],
        ];
    }
}
