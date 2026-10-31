<?php

namespace Tests\Feature;

use App\AponBasket\Theme\Defaults;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Admin\Tests\AdminTestCase;
use Webkul\Core\Models\Channel;
use Webkul\Theme\Models\Section;
use Webkul\Theme\SectionSchema;

/**
 * Run inside an installed, migrated Bagisto 2.4.13 / 2.5.0 TEST database:
 * vendor/bin/phpunit tests/Feature/AponBasketThemeTest.php
 */
final class AponBasketThemeTest extends AdminTestCase
{
    private Channel $channel;

    private string $locale;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        $this->channel = Channel::factory()->create(['theme' => Defaults::THEME]);
        $this->locale = $this->channel->locales->firstOrFail()->code;
    }

    public function test_theme_registers_native_editable_types(): void
    {
        $types = app(SectionSchema::class)->types(Defaults::THEME);

        foreach (Defaults::sections() as $definition) {
            $this->assertTrue($types->has($definition['type']), $definition['type']);
            $this->assertNotEmpty($types->get($definition['type'])->getFields());
        }

        $this->assertSame('default', config('themes.shop.aponbasket.parent'));
        $this->assertTrue($types->get('apon_services')->rendersInLayout());
    }

    public function test_install_is_channel_scoped_and_never_overwrites_or_recreates_merchant_data(): void
    {
        $other = Channel::factory()->create(['theme' => Defaults::THEME]);
        $hero = Section::query()->create([
            'type' => 'apon_hero', 'name' => 'Existing hero', 'theme_code' => Defaults::THEME,
            'channel_id' => $this->channel->id, 'sort_order' => 42, 'status' => 0,
        ]);
        $hero->translateOrNew($this->locale)->options = ['title_start' => 'Merchant content'];
        $hero->translateOrNew($this->locale)->draft_options = ['title_start' => 'Merchant draft'];
        $hero->save();

        $this->artisan('aponbasket:install', ['--channel' => $this->channel->code])->assertExitCode(0);

        $this->assertSame(9, Section::query()->where('channel_id', $this->channel->id)->where('theme_code', Defaults::THEME)->count());
        $this->assertSame(0, Section::query()->where('channel_id', $other->id)->where('theme_code', Defaults::THEME)->count());
        $this->assertSame('Merchant content', $hero->fresh()->translate($this->locale)->options['title_start']);
        $this->assertSame('Merchant draft', $hero->fresh()->translate($this->locale)->draft_options['title_start']);
        $this->assertSame(42, (int) $hero->fresh()->sort_order);
        $this->assertFalse((bool) $hero->fresh()->status);
        $this->assertSame(
            Defaults::VERSION,
            DB::table('aponbasket_theme_installations')->where('channel_id', $this->channel->id)->value('theme_version')
        );

        // An intentionally deleted section must not come back on a repeated install.
        $hero->delete();
        $this->artisan('aponbasket:install', ['--channel' => $this->channel->code])->assertExitCode(0);
        $this->assertFalse(Section::query()->where('channel_id', $this->channel->id)->where('type', 'apon_hero')->exists());
        $this->assertSame(8, Section::query()->where('channel_id', $this->channel->id)->where('theme_code', Defaults::THEME)->count());
    }

    public function test_install_copies_only_published_footer_links_from_the_current_theme(): void
    {
        $channel = Channel::factory()->create(['theme' => 'default']);
        $locale = $channel->locales->firstOrFail()->code;
        $footer = Section::query()->create([
            'type' => 'footer_links', 'name' => 'Store links', 'theme_code' => 'default',
            'channel_id' => $channel->id, 'sort_order' => 1, 'status' => 1,
        ]);
        $footer->translateOrNew($locale)->options = ['column_1' => [['title' => 'Published', 'url' => '/published']]];
        $footer->translateOrNew($locale)->draft_options = ['column_1' => [['title' => 'Unpublished', 'url' => '/draft']]];
        $footer->save();

        $this->artisan('aponbasket:install', ['--channel' => $channel->code])->assertExitCode(0);

        $copy = Section::query()->where('channel_id', $channel->id)->where('theme_code', Defaults::THEME)->where('type', 'footer_links')->firstOrFail();
        $this->assertSame('Published', $copy->translate($locale)->options['column_1'][0]['title']);
        $this->assertNull($copy->translate($locale)->draft_options);
        $this->assertSame('Unpublished', $footer->fresh()->translate($locale)->draft_options['column_1'][0]['title']);
    }

    public function test_storefront_renders_the_editable_home_and_global_services(): void
    {
        $this->artisan('aponbasket:install', ['--channel' => $this->channel->code])->assertExitCode(0);
        core()->setCurrentChannel($this->channel);

        $this->get(route('shop.home.index'))
            ->assertOk()
            ->assertSee('প্রকৃতির খাঁটি স্বাদ,')
            ->assertSee('themes/shop/aponbasket/css/aponbasket.css', false)
            ->assertSee('<v-products-carousel', false)
            ->assertSee('<v-categories-carousel', false)
            ->assertSee('ab-global-services', false);

        // The layout section is deliberately shared beyond the homepage.
        $this->get(route('shop.home.contact_us'))
            ->assertOk()
            ->assertSee('ab-global-services', false)
            ->assertDontSee('ab-hero__copy', false);
    }

    public function test_order_and_visibility_are_drafted_then_published(): void
    {
        $this->artisan('aponbasket:install', ['--channel' => $this->channel->code])->assertExitCode(0);
        $sections = Section::query()->where('channel_id', $this->channel->id)
            ->where('theme_code', Defaults::THEME)->orderBy('sort_order')->get();
        $hero = $sections->firstWhere('type', 'apon_hero');
        $benefits = $sections->firstWhere('type', 'apon_benefits');
        $promos = $sections->firstWhere('type', 'apon_promos');
        $ids = $sections->pluck('id')->all();
        [$ids[2], $ids[3]] = [$ids[3], $ids[2]];
        $this->loginAsAdmin();

        $this->postJson(route('admin.appearance.sections.reorder'), ['sections' => $ids])->assertOk();
        $this->postJson(route('admin.appearance.sections.status', ['id' => $hero->id]), ['status' => false])->assertOk();
        $this->assertTrue((bool) $hero->fresh()->status);
        $this->assertFalse((bool) $hero->fresh()->draft_status);
        $this->assertLessThan($promos->fresh()->sort_order, $benefits->fresh()->sort_order);

        $this->get(route('shop.appearance.preview', ['theme' => Defaults::THEME, 'channel' => $this->channel->code, 'locale' => $this->locale]))
            ->assertOk()
            ->assertDontSee('ab-hero__copy', false)
            ->assertSeeInOrder(['THE EVERYDAY PANTRY', 'প্রাকৃতিক পছন্দ']);

        $this->postJson(route('admin.appearance.sections.publish', ['code' => Defaults::THEME, 'channel' => $this->channel->code]))->assertOk();
        core()->setCurrentChannel($this->channel);
        $this->get(route('shop.home.index'))
            ->assertOk()
            ->assertDontSee('ab-hero__copy', false)
            ->assertSeeInOrder(['THE EVERYDAY PANTRY', 'প্রাকৃতিক পছন্দ']);
    }

    public function test_styles_are_not_injected_when_a_different_theme_is_active(): void
    {
        $this->channel->update(['theme' => 'default']);
        core()->setCurrentChannel($this->channel->fresh());

        $this->get(route('shop.home.index'))
            ->assertOk()
            ->assertDontSee('themes/shop/aponbasket/css/aponbasket.css', false);
    }

    public function test_bengali_editor_font_is_scoped_to_aponbasket_sections(): void
    {
        $this->artisan('aponbasket:install', ['--channel' => $this->channel->code])->assertExitCode(0);
        $this->loginAsAdmin();
        $font = 'themes/shop/aponbasket/fonts/noto-sans-bengali-bengali-wght-normal.woff2';

        $this->get(route('admin.appearance.sections.index', [
            'code' => Defaults::THEME, 'channel' => $this->channel->code, 'locale' => $this->locale,
        ]))->assertOk()->assertSee($font, false);

        $this->get(route('admin.appearance.themes.index'))
            ->assertOk()
            ->assertDontSee($font, false);

        $other = Channel::factory()->create(['theme' => 'default']);
        $this->get(route('admin.appearance.sections.index', [
            'code' => 'default', 'channel' => $other->code,
        ]))->assertOk()->assertDontSee($font, false);
    }

    public function test_editor_drafts_uploads_preview_and_publish_use_the_merchant_values(): void
    {
        $this->artisan('aponbasket:install', ['--channel' => $this->channel->code])->assertExitCode(0);
        $hero = Section::query()->where('channel_id', $this->channel->id)->where('type', 'apon_hero')->firstOrFail();
        $original = $hero->translate($this->locale)->options;
        $this->loginAsAdmin();

        $this->getJson(route('admin.appearance.sections.fields', ['id' => $hero->id, 'locale' => $this->locale]))
            ->assertOk()
            ->assertJsonPath('options.image', asset($original['image']));

        $upload = $this->postJson(route('admin.appearance.sections.media', ['id' => $hero->id]), [
            'file' => UploadedFile::fake()->image('new-hero.jpg', 800, 800),
        ])->assertOk();
        $path = $upload->json('path');
        $this->assertStringContainsString('themes/aponbasket/sections/'.$hero->id.'/', $path);

        $draft = array_merge($original, ['title_start' => 'নতুন শিরোনাম', 'image' => $path, 'primary_url' => 'javascript:alert(1)']);
        $this->postJson(route('admin.appearance.sections.draft', ['id' => $hero->id, 'locale' => $this->locale]), ['options' => $draft])->assertOk();
        $this->assertSame($original['title_start'], $hero->fresh()->translate($this->locale)->options['title_start']);
        $this->assertSame('', $hero->fresh()->translate($this->locale)->draft_options['primary_url']);

        $this->get(route('shop.appearance.preview', ['theme' => Defaults::THEME, 'channel' => $this->channel->code, 'locale' => $this->locale]))
            ->assertOk()
            ->assertSee('নতুন শিরোনাম')
            ->assertSee('themes/shop/aponbasket/css/aponbasket.css', false)
            ->assertDontSee('javascript:alert(1)', false);

        $this->postJson(route('admin.appearance.sections.publish', ['code' => Defaults::THEME, 'channel' => $this->channel->code]))->assertOk();
        $translation = $hero->fresh()->translate($this->locale);
        $this->assertSame('নতুন শিরোনাম', $translation->options['title_start']);
        $this->assertSame($path, $translation->options['image']);
        $this->assertNull($translation->draft_options);
    }
}
