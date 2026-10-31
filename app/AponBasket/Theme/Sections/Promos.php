<?php

namespace App\AponBasket\Theme\Sections;

final class Promos extends AponSection
{
    protected string $code = 'apon_promos';

    protected string $icon = 'icon-image';

    protected bool $singleton = false;

    public function getTitle(): string
    {
        return 'AponBasket · প্রচারণার কার্ড';
    }

    public function getFields(): array
    {
        return [
            Fields::text('kicker', 'ছোট উপশিরোনাম'),
            Fields::text('title_start', 'শিরোনাম'),
            Fields::text('title_highlight', 'রঙিন শিরোনাম'),
            Fields::textarea('description', 'বিবরণ'),
            Fields::text('link_text', 'উপরের লিংকের লেখা'),
            Fields::text('link_url', 'উপরের লিংকের ঠিকানা'),
            Fields::repeater('items', 'প্রচারণার কার্ড', 'কার্ড যোগ করুন', [
                Fields::image('image', 'কার্ডের ছবি'),
                Fields::text('image_alt', 'ছবির বিকল্প লেখা'),
                Fields::text('kicker', 'ছোট উপশিরোনাম'),
                Fields::text('title', 'কার্ডের শিরোনাম'),
                Fields::text('link_text', 'লিংকের লেখা'),
                Fields::text('url', 'পণ্য বা ক্যাটাগরির লিংক'),
            ]),
        ];
    }
}
