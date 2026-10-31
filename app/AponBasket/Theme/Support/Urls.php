<?php

namespace App\AponBasket\Theme\Support;

use Illuminate\Support\Facades\Storage;

/** Resolves merchant-authored links and both Bagisto 2.4/2.5 image path formats. */
final class Urls
{
    private const BUNDLED_IMAGES = 'themes/shop/aponbasket/images/';

    public static function cleanLink(mixed $value): string
    {
        return self::clean($value, ['http', 'https', 'mailto', 'tel']);
    }

    public static function cleanImage(mixed $value): string
    {
        return self::clean($value, ['http', 'https']);
    }

    private static function clean(mixed $value, array $allowedSchemes): string
    {
        if (! is_string($value)) {
            return '';
        }

        $value = trim($value);

        // Protocol-relative links, backslashes and control characters can change how a
        // browser interprets a URL. Do not try to repair an unsafe value.
        if ($value === '' || str_starts_with($value, '//') || preg_match('/[\x00-\x1f\x7f\\\\]/', $value)) {
            return '';
        }

        if (preg_match('/^([a-z][a-z0-9+.-]*):/i', $value, $matches)) {
            $scheme = strtolower($matches[1]);

            if (! in_array($scheme, $allowedSchemes, true)) {
                return '';
            }

            if (in_array($scheme, ['http', 'https'], true) && ! filter_var($value, FILTER_VALIDATE_URL)) {
                return '';
            }
        }

        return $value;
    }

    /** Turn a site-relative link into a URL without hard-coding a staging hostname. */
    public static function link(mixed $value): string
    {
        $value = self::cleanLink($value);

        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, '#') || preg_match('/^[a-z][a-z0-9+.-]*:/i', $value)) {
            return $value;
        }

        return url(ltrim($value, '/'));
    }

    /** Bundled artwork lives in public/, uploads live on Bagisto's configured disk. */
    public static function image(mixed $value): string
    {
        $value = self::cleanImage($value);

        if ($value === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $value)) {
            return $value;
        }

        $value = ltrim($value, '/');

        if (str_starts_with($value, self::BUNDLED_IMAGES)) {
            return asset($value);
        }

        // 2.5 stores disk-relative paths and knows how to resolve local or remote disks.
        // 2.4 stores a legacy "storage/" prefix; Storage::url supports either disk.
        if (function_exists('bagisto_theme_storage')) {
            return (string) bagisto_theme_storage()->url($value);
        }

        return Storage::url(preg_replace('#^storage/#', '', $value));
    }

    /** The editor expects public URLs for shipped assets, not paths on its upload disk. */
    public static function imageForEditor(mixed $value): string
    {
        $value = self::cleanImage($value);
        $path = ltrim($value, '/');

        if (str_starts_with($path, self::BUNDLED_IMAGES)) {
            return asset($path);
        }

        if (function_exists('bagisto_theme_storage') && str_starts_with($path, 'storage/')) {
            return substr($path, strlen('storage/'));
        }

        return $value;
    }

    /** Undo the editor-only URL so a moved site never retains the old hostname. */
    public static function imageForStorage(mixed $value): string
    {
        $value = self::cleanImage($value);
        $prefix = rtrim(asset(self::BUNDLED_IMAGES), '/').'/';

        if (str_starts_with($value, $prefix)) {
            return self::BUNDLED_IMAGES.substr($value, strlen($prefix));
        }

        return $value;
    }
}
