<?php

namespace App\AponBasket\Theme\Sections;

final class CallToAction extends AponSection
{
    protected string $code = 'apon_cta';

    protected bool $singleton = false;

    public function getTitle(): string
    {
        return 'AponBasket · নিচের ব্যানার';
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
        ];
    }
}
