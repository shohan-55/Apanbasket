<?php

/**
 * Compile all views with Laravel's real Blade compiler (including component tags).
 * LARAVEL_FRAMEWORK_DIR must point to the Laravel framework source or
 * vendor/laravel/framework of an installed Bagisto. This is not a Bagisto E2E test.
 */

namespace Illuminate\Container {
    // Blade resolves aliases through the container. The test never renders components.
    class Container
    {
        public static function getInstance(): self { return new self; }
        public function make(string $name): object { return new class { public function exists(string $name): bool { return true; } }; }
    }
}

namespace AponBasket\Testing {
    class DummyComponent {}
}

namespace {
    // Laravel's Composer install normally supplies the PHP 8.5 array polyfills.
    if (! function_exists('array_last')) {
        function array_last(array $array): mixed { return $array === [] ? null : end($array); }
    }
    if (! function_exists('array_first')) {
        function array_first(array $array): mixed { return $array === [] ? null : reset($array); }
    }

    $framework = getenv('LARAVEL_FRAMEWORK_DIR') ?: __DIR__.'/../../vendor/laravel/framework';
    if (! is_file($framework.'/src/Illuminate/View/Compilers/BladeCompiler.php')) {
        throw new \RuntimeException('Set LARAVEL_FRAMEWORK_DIR to a Laravel 12 framework checkout.');
    }

    spl_autoload_register(function (string $class) use ($framework): void {
        if (str_starts_with($class, 'Illuminate\\Support\\')) {
            $suffix = str_replace('\\', '/', substr($class, strlen('Illuminate\\Support\\'))).'.php';
            foreach (['Macroable', 'Collections', 'Conditionable', 'Reflection'] as $directory) {
                $path = $framework.'/src/Illuminate/'.$directory.'/'.$suffix;
                if (is_file($path)) { require $path; return; }
            }
        }
        if (str_starts_with($class, 'Illuminate\\')) {
            $path = $framework.'/src/'.str_replace('\\', '/', $class).'.php';
            if (is_file($path)) require $path;
        }
        if (str_starts_with($class, 'App\\AponBasket\\Theme\\')) {
            $path = __DIR__.'/../../app/AponBasket/Theme/'.str_replace('\\', '/', substr($class, strlen('App\\AponBasket\\Theme\\'))).'.php';
            if (is_file($path)) require $path;
        }
    });
    require $framework.'/src/Illuminate/Collections/functions.php';
    require $framework.'/src/Illuminate/Collections/helpers.php';
    require $framework.'/src/Illuminate/Support/functions.php';
    require $framework.'/src/Illuminate/Support/helpers.php';

    function asset(string $path): string { return 'https://example.test/'.trim($path, '/'); }
    function url(string $path): string { return 'https://example.test/'.ltrim($path, '/'); }
    function check(bool $condition, string $message): void { if (! $condition) throw new \RuntimeException($message); }

    $compiler = new \Illuminate\View\Compilers\BladeCompiler(new \Illuminate\Filesystem\Filesystem, sys_get_temp_dir());
    foreach (['shop::layouts', 'shop::categories.carousel', 'shop::products.carousel', 'shop::carousel'] as $alias) {
        $compiler->component(\AponBasket\Testing\DummyComponent::class, $alias);
    }

    $root = __DIR__.'/../../resources/themes/aponbasket/views';
    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));
    $count = 0;
    foreach ($iterator as $file) {
        if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) continue;
        $compiled = $compiler->compileString(file_get_contents($file->getPathname()));
        try {
            token_get_all($compiled, TOKEN_PARSE);
        } catch (\ParseError $error) {
            throw new \RuntimeException($file->getPathname().' failed to compile: '.$error->getMessage(), 0, $error);
        }
        $count++;
    }
    check($count === 12, 'Expected twelve theme views');

    // Render an actual compiled partial: dynamic text is escaped and unsafe URLs
    // are omitted even if a malformed value somehow reaches the view.
    $compiler->withoutComponentTags();
    $source = file_get_contents($root.'/home/sections/hero.blade.php');
    $compiled = $compiler->compileString($source);
    $data = [
        'title_start' => '<script>alert(1)</script> {{ unexpected }}',
        'title_highlight' => 'Safe',
        'primary_text' => 'Click',
        'primary_url' => 'javascript:alert(1)',
        'image' => 'themes/shop/aponbasket/images/gawa-ghee-promo.jpg',
    ];
    ob_start();
    eval('?>'.$compiled);
    $html = ob_get_clean();
    check(str_contains($html, '&lt;script&gt;alert(1)&lt;/script&gt;'), 'Hero text must be escaped');
    check(! str_contains($html, 'href="javascript:'), 'Unsafe href must not render');
    check(str_contains($html, 'v-pre'), 'Merchant text must not be compiled as Vue expressions');
    check(str_contains($html, 'https://example.test/themes/shop/aponbasket/images/gawa-ghee-promo.jpg'), 'Bundled image URL missing');

    echo "ALL BLADE COMPILATION AND ESCAPING TESTS PASSED\n";
}
