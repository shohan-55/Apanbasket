<?php

namespace App\AponBasket\Theme\Sections;

use App\AponBasket\Theme\Support\Urls;

/** Shared input handling for theme sections; all text is escaped by the Blade views. */
trait HandlesOptions
{
    public function sanitize(array $options): array
    {
        return $this->mapOptions($options, static function (mixed $value, string $key): mixed {
            if ($key === 'filters') {
                if (! is_array($value)) {
                    return [];
                }

                $allowed = ['sort', 'limit', 'category_id', 'parent_id', 'featured', 'new'];
                $filters = [];

                foreach ($allowed as $name) {
                    if (isset($value[$name]) && is_scalar($value[$name])) {
                        $filters[$name] = substr((string) $value[$name], 0, 255);
                    }
                }

                return $filters;
            }

            if (self::isImageKey($key)) {
                return Urls::cleanImage($value);
            }

            if ($key === 'url' || str_ends_with($key, '_url')) {
                return Urls::cleanLink($value);
            }

            return is_scalar($value) ? (string) $value : '';
        });
    }

    public function prepareForEditor(array $options): array
    {
        return $this->mapOptions($options, static fn (mixed $value, string $key): mixed => self::isImageKey($key)
            ? Urls::imageForEditor($value)
            : $value);
    }

    public function prepareForStorage(array $options): array
    {
        return $this->mapOptions($options, static fn (mixed $value, string $key): mixed => self::isImageKey($key)
            ? Urls::imageForStorage($value)
            : $value);
    }

    private static function isImageKey(string $key): bool
    {
        return $key === 'image' || str_ends_with($key, '_image');
    }

    /** Only known repeater fields may hold rows; scalar fields must not become arrays. */
    private function mapOptions(array $options, callable $map): array
    {
        foreach ($options as $key => $value) {
            $keyName = (string) $key;

            if (is_array($value) && in_array($keyName, ['items', 'points'], true)) {
                $rows = array_filter(array_slice($value, 0, 12), 'is_array');
                $options[$key] = array_values(array_map(fn (array $row) => $this->mapOptions($row, $map), $rows));
            } else {
                $options[$key] = $map($value, $keyName);
            }
        }

        return $options;
    }
}
