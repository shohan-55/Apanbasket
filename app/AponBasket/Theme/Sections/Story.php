<?php

namespace App\AponBasket\Theme\Sections;

final class Story extends AponSection
{
    protected string $code = 'apon_story';

    protected bool $singleton = false;

    public function getTitle(): string
    {
        return 'AponBasket · আমাদের গল্প';
    }

    public function getFields(): array
    {
        return [
            Fields::image('image', 'গল্পের ছবি'),
            Fields::text('image_alt', 'ছবির বিকল্প লেখা'),
            Fields::text('stamp_first', 'গোল ব্যাজের প্রথম লাইন'),
            Fields::text('stamp_second', 'গোল ব্যাজের দ্বিতীয় লাইন'),
            Fields::text('stamp_caption', 'গোল ব্যাজের নিচের লেখা'),
            Fields::text('kicker', 'ছোট উপশিরোনাম'),
            Fields::text('title_start', 'শিরোনাম'),
            Fields::text('title_highlight', 'রঙিন শিরোনাম'),
            Fields::textarea('description', 'গল্পের বিবরণ'),
            Fields::repeater('points', 'মূল বিষয়গুলো', 'বিষয় যোগ করুন', [
                Fields::text('text', 'বিষয়'),
            ], 6),
            Fields::text('link_text', 'বাটনের লেখা'),
            Fields::text('link_url', 'বাটনের লিংক'),
        ];
    }
}
