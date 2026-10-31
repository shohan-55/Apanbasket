<?php

/** In-memory command tests; real database/HTTP coverage lives in the Bagisto Feature test. */

namespace Illuminate\Console {
    abstract class Command
    {
        public const SUCCESS = 0;
        public const FAILURE = 1;
        public array $testOptions = [];
        public array $messages = [];
        public function option(string $name): mixed { return $this->testOptions[$name] ?? null; }
        public function error(string $message): void { $this->messages[] = $message; }
        public function info(string $message): void { $this->messages[] = $message; }
        public function warn(string $message): void { $this->messages[] = $message; }
    }
}

namespace Illuminate\Support\Facades {
    class Schema
    {
        public static bool $ready = true;
        public static function hasTable(string $table): bool { return self::$ready; }
    }
    class DB
    {
        public static array $markers = [];
        public static function transaction(callable $callback): mixed
        {
            $backup = serialize([self::$markers, \Webkul\Theme\Models\Section::$records, \Webkul\Theme\Models\Section::$nextId]);
            try {
                return $callback();
            } catch (\Throwable $error) {
                [self::$markers, \Webkul\Theme\Models\Section::$records, \Webkul\Theme\Models\Section::$nextId] = unserialize($backup);
                throw $error;
            }
        }
        public static function table(string $name): object
        {
            return new class {
                private mixed $channelId;
                public function where(string $field, mixed $value): self { $this->channelId = $value; return $this; }
                public function exists(): bool { return isset(DB::$markers[$this->channelId]); }
                public function insert(array $row): void { DB::$markers[$row['channel_id']] = $row; }
            };
        }
    }
}

namespace Webkul\Theme\Enums {
    enum SectionTypeEnum: string { case FOOTER_LINKS = 'footer_links'; }
}

namespace TestDoubles {
    use ArrayIterator;
    use IteratorAggregate;
    use Traversable;

    class Collection implements IteratorAggregate
    {
        public function __construct(public array $items) {}
        public function getIterator(): Traversable { return new ArrayIterator($this->items); }
        public function pluck(string $key): self { return new self(array_map(fn ($item) => $item->$key, $this->items)); }
        public function filter(): self { return new self(array_filter($this->items)); }
        public function unique(): self { return new self(array_unique($this->items)); }
        public function values(): self { return new self(array_values($this->items)); }
        public function isEmpty(): bool { return $this->items === []; }
        public function max(string $key): mixed { return $this->items ? max(array_map(fn ($item) => $item->$key, $this->items)) : null; }
        public function contains(string $key, mixed $value): bool { return $this->firstWhere($key, $value) !== null; }
        public function firstWhere(string $key, mixed $value): mixed
        {
            foreach ($this->items as $item) if ($item->$key === $value) return $item;
            return null;
        }
    }
}

namespace Webkul\Core\Models {
    use TestDoubles\Collection;
    class Channel
    {
        public static array $records = [];
        public Collection $locales;
        public function __construct(public int $id, public string $code, public ?string $theme, array $locales)
        {
            $this->locales = new Collection(array_map(fn ($code) => (object) ['code' => $code], $locales));
            self::$records[$id] = $this;
        }
        public static function query(): object
        {
            return new class {
                private ?string $code = null;
                public function where(string $key, string $value): self { $this->code = $value; return $this; }
                public function first(): ?Channel { foreach (Channel::$records as $channel) if ($channel->code === $this->code) return $channel; return null; }
                public function with(string $relation): self { return $this; }
                public function lockForUpdate(): self { return $this; }
                public function findOrFail(int $id): Channel { return Channel::$records[$id] ?? throw new \RuntimeException('missing channel'); }
            };
        }
    }
}

namespace Webkul\Theme\Models {
    use TestDoubles\Collection;
    class Section
    {
        public static array $records = [];
        public static int $nextId = 1;
        public int $id;
        public string $type;
        public string $name;
        public string $theme_code;
        public int $channel_id;
        public int $sort_order;
        public int $status;
        public Collection $translations;
        public function __construct(array $data)
        {
            $this->id = self::$nextId++;
            foreach ($data as $key => $value) $this->$key = $value;
            $this->translations = new Collection([]);
        }
        public static function query(): object
        {
            return new class {
                private array $filters = [];
                private ?string $order = null;
                public function where(string $key, mixed $value): self { $this->filters[$key] = $value; return $this; }
                public function orderBy(string $key): self { $this->order = $key; return $this; }
                public function get(): Collection
                {
                    $items = array_values(array_filter(Section::$records, function ($section) {
                        foreach ($this->filters as $key => $value) if ($section->$key !== $value) return false;
                        return true;
                    }));
                    if ($this->order) usort($items, fn ($a, $b) => $a->{$this->order} <=> $b->{$this->order});
                    return new Collection($items);
                }
                public function first(): ?Section { return $this->get()->items[0] ?? null; }
                public function create(array $data): Section { $section = new Section($data); Section::$records[$section->id] = $section; return $section; }
            };
        }
        public function translateOrNew(string $locale): object
        {
            $existing = $this->translations->firstWhere('locale', $locale);
            if ($existing) return $existing;
            $translation = (object) ['locale' => $locale, 'options' => null, 'draft_options' => null];
            $this->translations->items[] = $translation;
            return $translation;
        }
        public function save(): void {}
    }
}

namespace {
    use App\AponBasket\Theme\Console\InstallTheme;
    use App\AponBasket\Theme\Defaults;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Webkul\Core\Models\Channel;
    use Webkul\Theme\Models\Section;

    function now(): string { return '2026-10-31 00:00:00'; }
    function resource_path(string $path): string { return $GLOBALS['mock_legacy_path'] ?? __DIR__.'/../../resources/'.$path; }
    function core(): object { return new class { public function getDefaultChannel(): ?Channel { return Channel::$records[1] ?? null; } }; }
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\AponBasket\\Theme\\';
        if (str_starts_with($class, $prefix)) {
            $path = __DIR__.'/../../app/AponBasket/Theme/'.str_replace('\\', '/', substr($class, strlen($prefix))).'.php';
            if (is_file($path)) require $path;
        }
    });
    function check(bool $condition, string $message): void { if (! $condition) throw new \RuntimeException($message); }
    function sectionsFor(int $id): array { return Section::query()->where('channel_id', $id)->where('theme_code', 'aponbasket')->get()->items; }

    new Channel(1, 'first', 'default', ['en', 'bn']);
    $footer = Section::query()->create(['type' => 'footer_links', 'name' => 'Links', 'theme_code' => 'default', 'channel_id' => 1, 'sort_order' => 1, 'status' => 1]);
    $footer->translateOrNew('en')->options = ['column_1' => [['title' => 'Published', 'url' => '/published']]];
    $footer->translateOrNew('en')->draft_options = ['column_1' => [['title' => 'Draft', 'url' => '/draft']]];
    $footer->translateOrNew('bn')->options = ['column_1' => [['title' => 'বাংলা', 'url' => '/bn']]];
    $hero = Section::query()->create(['type' => 'apon_hero', 'name' => 'Existing', 'theme_code' => 'aponbasket', 'channel_id' => 1, 'sort_order' => 42, 'status' => 0]);
    $hero->translateOrNew('en')->options = ['title_start' => 'Keep me'];
    $hero->translateOrNew('en')->draft_options = ['title_start' => 'Keep draft'];

    $command = new InstallTheme;
    check($command->handle() === 0, 'Default-channel install failed');
    check(DB::$markers[1]['theme_version'] === Defaults::VERSION, 'Install marker version mismatch');
    check(count(sectionsFor(1)) === 10, 'Expected the nine design sections plus copied footer');
    check($hero->sort_order === 42 && $hero->status === 0, 'Existing hero settings were changed');
    check($hero->translateOrNew('en')->options['title_start'] === 'Keep me', 'Existing hero was overwritten');
    check($hero->translateOrNew('en')->draft_options['title_start'] === 'Keep draft', 'Existing hero draft was overwritten');
    $copy = Section::query()->where('channel_id', 1)->where('theme_code', 'aponbasket')->where('type', 'footer_links')->first();
    check($copy->translateOrNew('en')->options['column_1'][0]['title'] === 'Published', 'Published footer not copied');
    check($copy->translateOrNew('en')->draft_options === null, 'Unpublished footer draft must not be copied');
    check($footer->translateOrNew('en')->draft_options['column_1'][0]['title'] === 'Draft', 'Original footer draft changed');
    check($copy->translateOrNew('bn')->options['column_1'][0]['title'] === 'বাংলা', 'Footer locale not copied');

    // An old v1.1 layout would shadow the current Bagisto layout even after copying this package.
    $GLOBALS['mock_legacy_path'] = __FILE__;
    $legacy = new InstallTheme;
    check($legacy->handle() === 0, 'Legacy layout warning should not change installation status');
    check(count(array_filter($legacy->messages, fn ($message) => str_contains($message, 'layout override'))) === 1, 'Legacy layout warning missing');
    unset($GLOBALS['mock_legacy_path']);

    unset(Section::$records[$hero->id]);
    check($command->handle() === 0 && count(sectionsFor(1)) === 9, 'Repeat install must not recreate deleted sections');
    check(Section::query()->where('channel_id', 1)->where('theme_code', 'aponbasket')->where('type', 'apon_hero')->first() === null, 'Deleted hero reappeared');

    new Channel(2, 'second', 'default', ['en', 'bn']);
    $second = new InstallTheme;
    $second->testOptions['channel'] = 'second';
    check($second->handle() === 0 && count(sectionsFor(2)) === 9, 'Second channel install failed');
    $secondHero = Section::query()->where('channel_id', 2)->where('type', 'apon_hero')->first();
    check($secondHero->translateOrNew('en')->options === $secondHero->translateOrNew('bn')->options, 'Initial locales are not seeded');
    check(count(sectionsFor(1)) === 9, 'Installing another channel modified the first');

    $missing = new InstallTheme;
    $missing->testOptions['channel'] = 'not-a-channel';
    check($missing->handle() === 1, 'Unknown channel should fail');
    new Channel(3, 'no-locales', 'default', []);
    $empty = new InstallTheme;
    $empty->testOptions['channel'] = 'no-locales';
    try { $empty->handle(); throw new \RuntimeException('Empty locales should fail'); } catch (\RuntimeException $e) {
        check(str_contains($e->getMessage(), 'no locales'), 'Unexpected empty-locale error');
    }
    check(! isset(DB::$markers[3]) && count(sectionsFor(3)) === 0, 'Failed installation must be atomic');
    Schema::$ready = false;
    check((new InstallTheme)->handle() === 1, 'Missing migration should fail safely');

    echo "ALL INSTALLER CONTRACT TESTS PASSED\n";
}
