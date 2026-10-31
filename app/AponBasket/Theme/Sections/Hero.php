<?php

namespace App\AponBasket\Theme\Sections;

final class Hero extends AponSection
{
    protected string $code = 'apon_hero';

    protected string $icon = 'icon-image';

    public function getTitle(): string
    {
        return 'AponBasket · হিরো ব্যানার';
    }

    public function getFields(): array
    {
        return [
            Fields::text('eyebrow', 'ছোট উপশিরোনাম'),
            Fields::text('title_start', 'শিরোনামের প্রথম লাইন'),
            Fields::text('title_highlight', 'রঙিন শিরোনাম'),
            Fields::text('title_end', 'রঙিন অংশের পরের লেখা'),
            Fields::textarea('description', 'বিবরণ'),
            Fields::image('image', 'হিরো ছবি'),
            Fields::text('image_alt', 'ছবির বিকল্প লেখা'),
            Fields::text('primary_text', 'প্রধান বাটনের লেখা'),
            Fields::text('primary_url', 'প্রধান বাটনের লিংক'),
            Fields::text('secondary_text', 'দ্বিতীয় বাটনের লেখা'),
            Fields::text('secondary_url', 'দ্বিতীয় বাটনের লিংক'),
            Fields::text('proof_title', 'আস্থার লেখার শিরোনাম'),
            Fields::text('proof_subtitle', 'আস্থার লেখার বিবরণ'),
            Fields::text('note_top_title', 'উপরের ব্যাজের শিরোনাম'),
            Fields::text('note_top_subtitle', 'উপরের ব্যাজের বিবরণ'),
            Fields::text('note_bottom_title', 'নিচের ব্যাজের শিরোনাম'),
            Fields::text('note_bottom_subtitle', 'নিচের ব্যাজের বিবরণ'),
        ];
    }
}
