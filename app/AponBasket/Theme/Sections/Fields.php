<?php

namespace App\AponBasket\Theme\Sections;

use Webkul\Theme\SectionSchema;

/** Small helpers keep the native Appearance editor schemas consistent. */
final class Fields
{
    public static function text(string $key, string $label): array
    {
        return ['key' => $key, 'type' => SectionSchema::TEXT, 'label' => $label];
    }

    public static function textarea(string $key, string $label): array
    {
        return ['key' => $key, 'type' => SectionSchema::TEXTAREA, 'label' => $label];
    }

    public static function image(string $key, string $label): array
    {
        return ['key' => $key, 'type' => SectionSchema::IMAGE, 'label' => $label];
    }

    public static function repeater(string $key, string $label, string $addLabel, array $fields, int $max = 12): array
    {
        return [
            'key' => $key,
            'type' => SectionSchema::REPEATER,
            'label' => $label,
            'add_label' => $addLabel,
            'max' => $max,
            'fields' => $fields,
        ];
    }
}
