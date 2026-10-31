<?php

/**
 * Check the integrated Bagisto source and AponBasket registration without a
 * database, an installed vendor directory, or a running application.
 */
$root = dirname(__DIR__, 2);

function projectCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}

$core = file_get_contents($root.'/packages/Webkul/Core/src/Core.php');
projectCheck((bool) preg_match("/const\\s+BAGISTO_VERSION\\s*=\\s*'2\\.5\\.0'/", $core), 'Expected Bagisto v2.5.0 source');

$composer = json_decode(file_get_contents($root.'/composer.json'), true, 512, JSON_THROW_ON_ERROR);
projectCheck(($composer['autoload']['psr-4']['App\\'] ?? null) === 'app/', 'App namespace must resolve the theme provider');
projectCheck(($composer['require']['php'] ?? null) === '^8.4', 'Expected the upstream PHP 8.4 requirement');

$providers = require $root.'/bootstrap/providers.php';
$provider = 'App\\AponBasket\\Theme\\Providers\\AponBasketServiceProvider';
projectCheck(count(array_keys($providers, $provider, true)) === 1, 'Theme provider must be registered exactly once');

foreach ([
    'app/AponBasket/Theme/Providers/AponBasketServiceProvider.php',
    'resources/themes/aponbasket/views/home/index.blade.php',
    'resources/themes/aponbasket/views/components/layouts/footer/copyright.blade.php',
    'public/themes/shop/aponbasket/css/aponbasket.css',
    'LICENSE',
    'composer.lock',
] as $file) {
    projectCheck(is_file($root.'/'.$file), 'Required project file missing: '.$file);
}

// These are the original upstream Vite artifacts, tracked in the Bagisto tag.
// Verify all referenced files exist rather than silently shipping empty builds.
foreach ([
    'public/build/manifest.json',
    'public/themes/shop/default/build/manifest.json',
    'public/themes/admin/default/build/manifest.json',
    'public/themes/installer/default/build/manifest.json',
] as $relative) {
    $path = $root.'/'.$relative;
    projectCheck(is_file($path), 'Vite manifest missing: '.$relative);
    $manifest = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    projectCheck($manifest !== [], 'Vite manifest is empty: '.$relative);

    foreach ($manifest as $entry) {
        foreach (['file', 'css', 'assets'] as $key) {
            $values = $entry[$key] ?? [];
            foreach (is_array($values) ? $values : [$values] as $file) {
                projectCheck(is_file(dirname($path).'/'.$file), 'Vite asset missing: '.$relative.' -> '.$file);
            }
        }
    }
}

echo "BAGISTO 2.5 PROJECT AND THEME REGISTRATION CHECKS PASSED\n";
