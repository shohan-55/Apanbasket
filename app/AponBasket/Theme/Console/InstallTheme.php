<?php

namespace App\AponBasket\Theme\Console;

use App\AponBasket\Theme\Defaults;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Core\Models\Channel;
use Webkul\Theme\Enums\SectionTypeEnum;
use Webkul\Theme\Models\Section;

/** Import the reference design once, without modifying any existing section or draft. */
final class InstallTheme extends Command
{
    protected $signature = 'aponbasket:install {--channel= : Channel code (defaults to the default channel)}';

    protected $description = 'Install the editable AponBasket design for one channel without overwriting existing sections';

    public function handle(): int
    {
        if (! Schema::hasTable('aponbasket_theme_installations') || ! Schema::hasTable('theme_sections')) {
            $this->error('Required tables are missing. Back up the database and run php artisan migrate first.');

            return self::FAILURE;
        }

        $code = $this->option('channel');
        $channel = $code
            ? Channel::query()->where('code', $code)->first()
            : core()->getDefaultChannel();

        if (! $channel) {
            $this->error('Channel not found. Supply an existing channel code with --channel=.');

            return self::FAILURE;
        }

        if (is_file(resource_path('themes/aponbasket/views/components/layouts/index.blade.php'))) {
            $this->warn('An AponBasket layout override is present. Back it up and review it: the old v1.1 layout must be removed from the active views to use the current Bagisto layout. See README-BN.md.');
        }

        $result = DB::transaction(function () use ($channel): array {
            // Serialize installs on this channel, including the first ever installation.
            $channel = Channel::query()->with('locales')->lockForUpdate()->findOrFail($channel->id);

            if (DB::table('aponbasket_theme_installations')->where('channel_id', $channel->id)->exists()) {
                return ['already_installed' => true, 'created' => 0, 'skipped' => 0];
            }

            $locales = $channel->locales->pluck('code')->filter()->unique()->values();

            if ($locales->isEmpty()) {
                throw new \RuntimeException('The selected channel has no locales. Add a locale to the channel and try again.');
            }

            $existing = Section::query()
                ->where('channel_id', $channel->id)
                ->where('theme_code', Defaults::THEME)
                ->get();

            $sortOrder = (int) ($existing->max('sort_order') ?? 0);
            $created = 0;
            $skipped = 0;

            foreach (Defaults::sections() as $definition) {
                if ($existing->contains('type', $definition['type'])) {
                    $skipped++;

                    continue;
                }

                $section = Section::query()->create([
                    'type' => $definition['type'],
                    'name' => $definition['name'],
                    'theme_code' => Defaults::THEME,
                    'channel_id' => $channel->id,
                    'sort_order' => ++$sortOrder,
                    'status' => 1,
                ]);

                foreach ($locales as $locale) {
                    $section->translateOrNew($locale)->options = $definition['options'];
                }

                $section->save();
                $created++;
            }

            // When preparing a different active theme for a switch, copy only its
            // published footer links. The source and all unpublished drafts stay intact.
            if (
                $channel->theme
                && $channel->theme !== Defaults::THEME
                && ! $existing->contains('type', SectionTypeEnum::FOOTER_LINKS->value)
            ) {
                $sourceFooter = Section::query()
                    ->where('channel_id', $channel->id)
                    ->where('theme_code', $channel->theme)
                    ->where('type', SectionTypeEnum::FOOTER_LINKS->value)
                    ->where('status', 1)
                    ->orderBy('sort_order')
                    ->first();

                if ($sourceFooter) {
                    $footer = Section::query()->create([
                        'type' => SectionTypeEnum::FOOTER_LINKS->value,
                        'name' => $sourceFooter->name,
                        'theme_code' => Defaults::THEME,
                        'channel_id' => $channel->id,
                        'sort_order' => ++$sortOrder,
                        'status' => 1,
                    ]);

                    foreach ($locales as $locale) {
                        $translation = $sourceFooter->translations->firstWhere('locale', $locale);

                        if ($translation && is_array($translation->options)) {
                            $footer->translateOrNew($locale)->options = $translation->options;
                        }
                    }

                    $footer->save();
                    $created++;
                }
            }

            DB::table('aponbasket_theme_installations')->insert([
                'channel_id' => $channel->id,
                'theme_version' => Defaults::VERSION,
                'installed_at' => now(),
            ]);

            return ['already_installed' => false, 'created' => $created, 'skipped' => $skipped];
        });

        if ($result['already_installed']) {
            $this->info("AponBasket is already initialized for {$channel->code}; no data was changed.");
        } else {
            $this->info("AponBasket initialized for {$channel->code}: {$result['created']} sections added, {$result['skipped']} existing sections left untouched.");
        }

        return self::SUCCESS;
    }
}
