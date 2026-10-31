<?php

namespace App\AponBasket\Theme\Providers;

use App\AponBasket\Theme\Console\InstallTheme;
use App\AponBasket\Theme\Defaults;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Theme\Repositories\SectionRepository;
use Webkul\Theme\Sections\SectionType;
use Webkul\Theme\SectionSchema;
use Webkul\Theme\ViewRenderEventManager;

final class AponBasketServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Older Bagisto releases do not have the native section editor used here.
        // Leave their existing storefront untouched rather than breaking the site.
        if (! $this->supportsSectionEditor()) {
            return;
        }

        $this->app['config']->set('themes.shop.'.Defaults::THEME, require __DIR__.'/../Config/theme.php');

        if ($this->app->runningInConsole()) {
            $this->commands([InstallTheme::class]);
        }
    }

    public function boot(): void
    {
        if (! $this->supportsSectionEditor()) {
            return;
        }

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        // Bagisto's theme view finder checks a namespace matching the theme code.
        // Register it explicitly so overlays remain resolvable across requests,
        // including in long-lived workers and when several pages are tested in one process.
        $this->loadViewsFrom(resource_path('themes/aponbasket/views'), Defaults::THEME);

        // Use Bagisto's extension point, not a copy of its layout. Prepending to
        // the existing styles stack keeps merchant custom CSS later in the cascade.
        Event::listen('bagisto.shop.layout.head.before', static function (ViewRenderEventManager $event): void {
            if (themes()->current()?->code === Defaults::THEME) {
                $event->addTemplate(Defaults::THEME.'::theme.head');
            }
        });

        // The editor's Bengali field labels must also be readable on systems that
        // do not have a Bengali font. Do not alter unrelated admin pages or icons.
        Event::listen('bagisto.admin.layout.head.before', static function (ViewRenderEventManager $event): void {
            if (request()->routeIs('admin.appearance.sections.index') && request()->route('code') === Defaults::THEME) {
                $event->addTemplate(Defaults::THEME.'::theme.admin-head');
            }
        });
    }

    private function supportsSectionEditor(): bool
    {
        return class_exists(SectionType::class)
            && class_exists(SectionSchema::class)
            && method_exists(SectionRepository::class, 'getRenderable');
    }
}
