<?php

namespace App\AponBasket\Theme\Sections;

final class Services extends AponSection
{
    protected string $code = 'apon_services';

    protected string $icon = 'icon-store';

    protected bool $pinned = true;

    protected bool $layout = true;

    public function getTitle(): string
    {
        return 'AponBasket · সব পৃষ্ঠার সেবা বার';
    }

    public function getFields(): array
    {
        return [
            Fields::repeater('items', 'সেবাসমূহ', 'সেবা যোগ করুন', [
                Fields::text('symbol', 'চিহ্ন'),
                Fields::text('title', 'শিরোনাম'),
                Fields::text('subtitle', 'ছোট বিবরণ'),
            ], 6),
        ];
    }
}
