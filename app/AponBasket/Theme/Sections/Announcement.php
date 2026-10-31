<?php

namespace App\AponBasket\Theme\Sections;

final class Announcement extends AponSection
{
    protected string $code = 'apon_announcement';

    public function getTitle(): string
    {
        return 'AponBasket · ঘোষণা বার';
    }

    public function getFields(): array
    {
        return [
            Fields::text('text', 'ঘোষণার লেখা'),
            Fields::text('link_text', 'লিংকের লেখা'),
            Fields::text('link_url', 'লিংক (যেমন search বা /offers)'),
        ];
    }
}
