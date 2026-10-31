<?php

namespace App\AponBasket\Theme\Sections;

final class Benefits extends AponSection
{
    protected string $code = 'apon_benefits';

    public function getTitle(): string
    {
        return 'AponBasket · আস্থার বার';
    }

    public function getFields(): array
    {
        return [
            Fields::repeater('items', 'সুবিধাসমূহ (সর্বোচ্চ ৪টি)', 'সুবিধা যোগ করুন', [
                Fields::text('symbol', 'চিহ্ন (যেমন ♧, ♡, ⌂, ✓)'),
                Fields::text('title', 'শিরোনাম'),
                Fields::text('subtitle', 'ছোট বিবরণ'),
            ], 4),
        ];
    }
}
