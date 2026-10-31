<?php

namespace App\AponBasket\Theme\Sections;

use Webkul\Theme\Sections\SectionType;

abstract class AponSection extends SectionType
{
    use HandlesOptions;

    protected bool $singleton = true;
}
