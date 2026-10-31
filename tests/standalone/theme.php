<?php

/**
 * Dependency-free contract tests. These stubs model the Bagisto section API;
 * the separate Feature test must also be run in an installed Bagisto project.
 * Run: php tests/standalone/theme.php legacy && php tests/standalone/theme.php modern
 */

namespace Webkul\Theme {
    final class SectionSchema
    {
        public const TEXT = 'text';
        public const TEXTAREA = 'textarea';
        public const IMAGE = 'image';
        public const REPEATER = 'repeater';
        public const FILTERS = 'filters';
    }
}

namespace Webkul\Theme\Enums {
    enum SectionTypeEnum: string
    {
        case IMAGE_CAROUSEL = 'image_carousel';
        case PRODUCT_CAROUSEL = 'product_carousel';
        case CATEGORY_CAROUSEL = 'category_carousel';
        case FOOTER_LINKS = 'footer_links';
        case STATIC_CONTENT = 'static_content';
    }
}

namespace Webkul\Theme\Sections {
    abstract class SectionType
    {
        protected string $code;
        protected ?string $title = null;
        protected string $icon = 'icon-cms';
        protected bool $singleton = false;
        protected bool $pinned = false;
        protected bool $layout = false;

        public function getCode(): string { return $this->code; }
        public function isSingleton(): bool { return $this->singleton; }
        public function isPinned(): bool { return $this->pinned; }
        public function rendersInLayout(): bool { return $this->layout; }
    }

    class ProductCarousel extends SectionType
    {
        public function getFields(): array
        {
            return [
                ['key' => 'title', 'type' => 'text', 'label' => 'Title'],
                ['key' => 'filters', 'type' => 'filters', 'label' => 'Filters', 'keys' => []],
            ];
        }
    }

    class CategoryCarousel extends SectionType
    {
        public function getFields(): array
        {
            return [['key' => 'filters', 'type' => 'filters', 'label' => 'Filters', 'keys' => []]];
        }
    }
}

namespace Illuminate\Support\Facades {
    class Storage
    {
        public static function url(string $path): string { return 'https://media.example/'.$path; }
    }
}

namespace {
    use App\AponBasket\Theme\Defaults;
    use App\AponBasket\Theme\Sections\Hero;
    use App\AponBasket\Theme\Sections\Promos;
    use App\AponBasket\Theme\Support\Urls;

    function asset(string $path): string { return 'https://store.example/subdir/'.trim($path, '/'); }
    function url(string $path): string { return 'https://store.example/subdir/'.ltrim($path, '/'); }

    class FakeThemeStorage
    {
        public function url(string $path): string
        {
            return 'https://media.example/'.preg_replace('#^storage/#', '', $path);
        }
    }

    if (($argv[1] ?? 'legacy') === 'modern') {
        function bagisto_theme_storage(): FakeThemeStorage { return new FakeThemeStorage; }
    }

    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\AponBasket\\Theme\\';
        if (str_starts_with($class, $prefix)) {
            $path = __DIR__.'/../../app/AponBasket/Theme/'.str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
            if (is_file($path)) {
                require $path;
            }
        }
    });

    function check(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException($message);
        }
    }

    function same(mixed $expected, mixed $actual, string $message): void
    {
        check($expected === $actual, $message.'; got '.var_export($actual, true));
    }

    function verifyFields(array $options, array $fields, string $path): void
    {
        $byKey = [];
        foreach ($fields as $field) {
            foreach (['key', 'type', 'label'] as $required) {
                check(isset($field[$required]), "$path field is missing $required");
            }
            check(! isset($byKey[$field['key']]), "$path has a duplicate field key");
            $byKey[$field['key']] = $field;
        }

        foreach ($options as $key => $value) {
            check(isset($byKey[$key]), "$path.$key has no editable field");
            $field = $byKey[$key];
            if ($field['type'] === 'repeater') {
                check(is_array($value), "$path.$key must contain rows");
                check(count($value) <= ($field['max'] ?? PHP_INT_MAX), "$path.$key exceeds its editor limit");
                foreach ($value as $i => $row) {
                    verifyFields($row, $field['fields'], "$path.$key.$i");
                }
            } elseif ($field['type'] === 'image') {
                check(is_file(__DIR__.'/../../public/'.ltrim($value, '/')), "$path.$key artwork is missing");
            }
        }
    }

    $config = require __DIR__.'/../../app/AponBasket/Theme/Config/theme.php';
    $types = [];
    foreach ($config['customize']['sections'] as $class) {
        if (is_string($class) && is_subclass_of($class, \Webkul\Theme\Sections\SectionType::class)) {
            $type = new $class;
            check(! isset($types[$type->getCode()]), 'Section codes must be unique');
            $types[$type->getCode()] = $type;
        }
    }

    $defaults = Defaults::sections();
    same(9, count($defaults), 'Expected all original design sections');
    foreach ($defaults as $section) {
        $type = $types[$section['type']] ?? null;
        check($type !== null, $section['type'].' is not registered');
        verifyFields($section['options'], $type->getFields(), $section['type']);
        same($section['options'], $type->sanitize($section['options']), $section['type'].' default options must survive sanitization');
    }
    check($types['apon_services']->isPinned() && $types['apon_services']->rendersInLayout(), 'Services must render in the shared layout');
    check($types['apon_hero']->isSingleton(), 'There should only be one main hero');
    check(! $types['apon_products']->isSingleton(), 'Additional product carousels must be allowed');

    same('search?query=ghee', Urls::cleanLink('search?query=ghee'), 'Relative link');
    same('https://store.example/subdir/search?query=ghee', Urls::link('/search?query=ghee'), 'Site-relative links must follow the app base path');
    same('#ab-our-promise', Urls::link('#ab-our-promise'), 'Anchor link');
    same('mailto:hello@example.com', Urls::link('mailto:hello@example.com'), 'Mail link');
    foreach (["javascript:alert(1)", "JaVaScRiPt:alert(1)", "java\nscript:alert(1)", "data:text/html,boom", "//evil.example/path", "/\\evil.example"] as $unsafe) {
        same('', Urls::cleanLink($unsafe), 'Unsafe link must be rejected');
    }
    same('', Urls::cleanImage('mailto:test@example.com'), 'A mail link is not an image');
    same('', Urls::cleanImage(['unexpected']), 'A malformed image is rejected');

    $hero = new Hero;
    $clean = $hero->sanitize(['title_start' => ['malformed'], 'primary_url' => 'javascript:alert(1)', 'image' => 'data:text/html,bad']);
    same('', $clean['title_start'], 'Scalar fields reject arrays');
    same('', $clean['primary_url'], 'Section link is sanitized');
    same('', $clean['image'], 'Section image is sanitized');
    $promo = new Promos;
    $clean = $promo->sanitize(['items' => [['title' => ['bad'], 'url' => 'javascript:alert(1)']], 'filters' => ['limit' => 4, 'evil' => 'bad']]);
    same('', $clean['items'][0]['title'], 'Nested scalar fields reject arrays');
    same('', $clean['items'][0]['url'], 'Nested links are sanitized');
    same(['limit' => '4'], $clean['filters'], 'Only supported scalar catalog filters are passed to Bagisto');

    $bundled = 'themes/shop/aponbasket/images/gawa-ghee-promo.jpg';
    $editable = $promo->prepareForEditor(['items' => [['image' => $bundled]]]);
    same('https://store.example/subdir/'.$bundled, $editable['items'][0]['image'], 'Editor can preview bundled artwork');
    same($bundled, $promo->prepareForStorage($editable)['items'][0]['image'], 'Saving does not pin the staging hostname');
    same('https://store.example/subdir/'.$bundled, Urls::image($bundled), 'Bundled artwork uses the site asset URL');
    same('https://media.example/themes/aponbasket/sections/1/example.webp', Urls::image('storage/themes/aponbasket/sections/1/example.webp'), 'Legacy upload resolves on the disk');
    $legacyEditorPath = Urls::imageForEditor('storage/themes/aponbasket/sections/1/example.webp');
    same(function_exists('bagisto_theme_storage') ? 'themes/aponbasket/sections/1/example.webp' : 'storage/themes/aponbasket/sections/1/example.webp', $legacyEditorPath, 'Editor path matches Bagisto version');

    echo 'ALL CONTRACT TESTS PASSED ('.($argv[1] ?? 'legacy').")\n";
}
