# AponBasket Organic — সম্পাদনাযোগ্য Bagisto থিম

**সংস্করণ: 2.0.1** · থিম কোড: `aponbasket`

আপনার দেওয়া AponBasket v1.1 ডিজাইনের রং, ছবি, লেখা ও বিন্যাসকে ভিত্তি করে তৈরি এই সংস্করণে হোমপেজের অংশগুলো Bagisto-এর নিজস্ব **Appearance → Themes → Customize** সম্পাদক থেকে বদলানো যায়। পণ্য ও ক্যাটাগরি আসল Bagisto ক্যাটালগ থেকে আসে; এই প্যাকেজ কোনো নমুনা পণ্য বা দাম আপনার ডেটাবেজে ঢোকায় না। Bagisto core package ফাইল পরিবর্তন করা হয়নি।

> **প্রথমে staging-এ পরীক্ষা করুন।** আপনার live Bagisto-এর সংস্করণ ও নিজস্ব পরিবর্তন এই প্যাকেজ তৈরির সময়ে জানা ছিল না। ব্যাকআপ ছাড়া সরাসরি live site-এ প্রয়োগ করবেন না। বিশেষ করে আগে v1.1 চালু থাকলে একই `aponbasket` ফাইলের কিছু অংশ প্রতিস্থাপিত হবে এবং ইনস্টলার শেষ হওয়ার আগে হোমপেজ অসম্পূর্ণ দেখাতে পারে।

## সংস্করণ ও পূর্বশর্ত

- সম্পূর্ণ আলাদা পরীক্ষামূলক Bagisto **2.4.13** এবং **2.5.0**-এ পরীক্ষা করা হয়েছে। 2.4.13-এ PHP 8.3 ও 8.4 এবং 2.5.0-এ PHP 8.4 প্রয়োজন; আপনার Bagisto-এর নিজস্ব PHP ও extension-এর শর্তও মেনে চলুন।
- এই থিমের জন্য নতুন Bagisto Appearance section editor/API দরকার। পুরোনো কিছু 2.4 সংস্করণেও সেটি নেই; কেবল `2.4.x` দেখে সমর্থিত ধরে নেবেন না। অন্য সংস্করণ বা customised Bagisto-তে আগে staging-এ যাচাই ও প্রয়োজনমতো integration পরিবর্তন করতে হবে।
- একটি ইতিমধ্যে ইনস্টল ও migrate করা Bagisto project, CLI access, database backup, `bootstrap/providers.php` সম্পাদনার অনুমতি এবং admin Appearance/Channels অনুমতি লাগবে। Local public storage ব্যবহার করলে uploaded ছবি দেখার জন্য উপযুক্ত storage link ও web-server write permission লাগবে।
- আপনার project root-এ সংস্করণ যাচাইয়ের জন্য, যেখানে ফাইলগুলো আছে:

  ```bash
  php -v
  grep 'BAGISTO_VERSION' packages/Webkul/Core/src/Core.php
  test -f packages/Webkul/Theme/src/Sections/SectionType.php && echo 'Section API exists'
  ```

  আপনার project-এ package path আলাদা হলে সেখানকার `Core.php` দেখুন। API ফাইল থাকা একাই compatibility প্রমাণ করে না।

## ইনস্টলেশন

GitHub থেকে আলাদা checkout নিয়ে cPanel Terminal দিয়ে নিয়ন্ত্রিতভাবে আপডেট করার নির্দেশনা: [CPANEL-GITHUB-BN.md](CPANEL-GITHUB-BN.md)। এটি পুরো Bagisto project-এ সরাসরি `git pull` না করে শুধু থিমের ফাইল backup-সহ copy করে।

### ১. ব্যাকআপ ও staging

1. Database-এর পূর্ণ backup নিন; Bagisto project, `bootstrap/providers.php`, `config/themes.php`, `resources/themes/aponbasket`, `public/themes/shop/aponbasket` এবং `storage/app/public` বা ব্যবহৃত remote media storage-এর backup রাখুন। Backup **ওয়েব থেকে অ্যাক্সেসযোগ্য নয়** এমন জায়গায় রাখুন এবং restore সম্ভব কি না পরীক্ষা করুন।
2. প্রথমে live-এর অনুরূপ কিন্তু আলাদা database, storage ও URL-সহ staging তৈরি করুন। সেখানে আপনার বর্তমান Bagisto ও customizations দিয়ে সম্পূর্ণ ধাপগুলো যাচাই করুন। Production-এ প্রয়োগ করতে হলে একটি maintenance window নিন।
3. আগে দেওয়া v1.1 থিম ইনস্টল থাকলে নিচের **পুরোনো v1.1 থেকে আসলে** অংশটি অবশ্যই পড়ুন। নতুন ফাইল merge করলে পুরোনো অতিরিক্ত ফাইল নিজে থেকে সরে যায় না।

### ২. ফাইল যোগ ও provider নিবন্ধন

ZIP extract করুন। তার `app/AponBasket/`, `resources/themes/aponbasket/` ও `public/themes/shop/aponbasket/` নিজ নিজ একই path-এ Bagisto project root-এ merge/copy করুন। যেসব ফাইলের নাম মিলে যাচ্ছে সেগুলো প্রতিস্থাপিত হবে—আগে backup ও তুলনা করুন। অন্য `app`, `resources` বা `public` ফোল্ডার **পুরোপুরি replace করবেন না**। `preview/` ও `tests/` থিম চালানোর জন্য আবশ্যক নয়; চাইলে পরীক্ষার জন্য রাখুন।

Bagisto project-এর `bootstrap/providers.php`-এ বিদ্যমান provider-গুলোর পাশে নিচের একটি লাইন যোগ করুন; পুরো ফাইল replace করবেন না:

```php
return [
    // ... বিদ্যমান providers ...
    App\AponBasket\Theme\Providers\AponBasketServiceProvider::class,
];
```

Provider নিজেই `aponbasket` registry entry ও installer নিবন্ধন করে। এই প্যাকেজের জন্য `config/themes.php`-এ নতুন snippet যোগ করা বা Bagisto core layout overwrite করা প্রয়োজন নেই।

### ৩. থিমের database marker ও প্রতি-channel section তৈরি

Bagisto project root থেকে চালান (আগে backup নিন):

```bash
php artisan optimize:clear
php artisan migrate --path=app/AponBasket/Theme/Database/Migrations --force
php artisan aponbasket:install --channel=default
php artisan optimize:clear
```

`default` কেবল উদাহরণ; Admin → **Settings → Channels**-এ আপনার আসল channel **code** দেখে সেটি ব্যবহার করুন। একাধিক channel-এ থিম ব্যবহার করলে প্রত্যেকটির জন্য শেষের installer command আলাদাভাবে চালান। নির্দিষ্ট migration command শুধু এই থিমের installation-marker migration চালায়; আগে থেকেই Bagisto-এর core migrations সম্পূর্ণ থাকতে হবে। যদি `aponbasket:install` command না পাওয়া যায়, provider registration, Bagisto version এবং cache যাচাই করুন; `php artisan list`-এ command খুঁজুন।

লোকাল public disk ব্যবহার করলে এবং `public/storage` আগে থেকেই সঠিক link না হলে Bagisto/Laravel-এর `php artisan storage:link` ব্যবহার করুন। আগে থেকে থাকা link বা customised/cloud storage **জোর করে প্রতিস্থাপন করবেন না**।

ইনস্টলার প্রথমবার নির্বাচিত channel-এর জন্য অনুপস্থিত নয়টি AponBasket section যোগ করে; সেই channel-এর locales-এ initial content রাখে। বিদ্যমান AponBasket sections, published content, drafts, visibility ও order বদলায় না। অন্য থিম বর্তমানে active থাকলে এবং AponBasket-এ footer না থাকলে শুধু তার **published** footer links-এর একটি কপি নেয়; মূল footer ও draft অক্ষত থাকে। ইতিমধ্যে `aponbasket` active থাকলে অন্য থিমের footer স্বয়ংক্রিয়ভাবে কপি হবে না।

প্রথম সফল চালানোর পর একই channel-এ পুনরায় চালালে কোনো section পুনরায় তৈরি বা reset হয় না—ইচ্ছাকৃতভাবে মুছে ফেলা section-ও ফেরে না। নতুন locale পরে যোগ করলে সেই locale-এর লেখা editor-এ পরীক্ষা করে নিজে যোগ/সম্পাদনা করুন।

### ৪. থিম চালু ও যাচাই

1. Staging admin → **Appearance → Themes** → **AponBasket Organic** → **Activate** দিয়ে সঠিক channel বেছে নিন। Bagisto-র editor-এ Customize দেখা যায় active channel-এর জন্য; তাই production-এ আগে থেকেই test করতে staging ব্যবহার করুন।
2. **Customize** খুলে উপরে সঠিক channel ও locale নির্বাচন করুন। সেকশনে ক্লিক করে লেখা, ছবি ও লিংক সম্পাদনা করুন। পরিবর্তন draft হিসেবে সংরক্ষিত হয়; preview দেখে **Publish** করলে storefront-এ যায়। Drag দিয়ে অর্ডার বদলানো ও toggle দিয়ে দেখানো/লুকানো যায়। নিচে স্থির global service bar pinned থাকে, তাই তার অবস্থান বদলানো যায় না।
3. Admin → **Settings → Channels**-এ storefront logo আপলোড করুন। তৈরি PNG আছে `public/themes/shop/aponbasket/images/aponbasket-logo.png`-এ; ZIP থেকে কম্পিউটারে থাকা ফাইলটি বেছে আপলোড করুন। পুরোনো SVG force করার নিয়ম এই সংস্করণে নেই।
4. Homepage SEO-এর title/description/keywords channel-এর Home SEO থেকে আসে; খালি থাকলে AponBasket-এর default ব্যবহার হয়। Footer links এবং পণ্য/ক্যাটাগরি Bagisto-এর নিজস্ব editor/catalog-এ যাচাই করুন।
5. Staging-এ desktop ও mobile-এ home, সব বাটনের গন্তব্য, image upload, draft/preview/publish, menu, product page, search, cart, checkout, account এবং অন্যান্য custom extension পরীক্ষা করুন। সব ঠিক থাকলে একইভাবে production-এ প্রয়োগ করুন। CDN/OPcache/queue workers থাকলে আপনার deployment পদ্ধতি অনুযায়ী পুরোনো cache/worker refresh করুন।

### পুরোনো v1.1 থেকে আসলে

পুরোনো ZIP-এর `resources/themes/aponbasket/views/components/layouts/index.blade.php` একটি সম্পূর্ণ layout override ছিল। নতুন সংস্করণ এটি আর দেয় না—Bagisto-এর বর্তমান layout ও event ব্যবহার করে। পুরোনো ফাইলটি রেখে শুধু নতুন ফোল্ডার copy করলে সেটি থেকে যাবে এবং duplicate stylesheet, পুরোনো markup বা version incompatibility তৈরি করতে পারে।

- ফাইলটি আছে কি না দেখুন। এটি সেই v1.1-এর layout হলে প্রথমে এর একটি আলাদা backup রাখুন, তারপর **শুধু এই ফাইলটি** active views path থেকে সরান। আপনার নিজের পরিবর্তন থাকলে developer দিয়ে নতুন Bagisto layout-এর সঙ্গে আলাদাভাবে পর্যালোচনা করুন; অন্ধভাবে মুছবেন না। Installer ফাইলটি পেলে সতর্ক করে, নিজে সরায় না। `components/layouts/services.blade.php` কিন্তু নতুন সংস্করণের অংশ; সেটি রাখুন।
- আগে `config/themes.php`-তে হাতে যোগ করা পুরোনো `aponbasket` entry থাকলে provider runtime-এ নতুন registry সেট করে। প্রয়োজন ছাড়া config-এর অন্য অংশ বদলাবেন না; ভবিষ্যৎ বিভ্রান্তি এড়াতে staging-এ পরীক্ষা শেষে ওই পুরোনো entry backup রেখে সরানো যায়।
- পুরোনো editor sections থাকলে installer সেগুলো মুছে দেয় না। নতুন design sections সেগুলোর পরে যোগ হতে পারে; Customize-এ প্রয়োজনমতো order ও footer দেখে নিন।

## কী কী সম্পাদনা করা যায়

- ঘোষণা বার; hero-এর লেখা, ছবি, badges ও links; চারটি সুবিধার বার; প্রচারণার card ও ছবি।
- ক্যাটাগরি ও পণ্য carousel-এর শিরোনাম, links এবং Bagisto filter; brand story, নিচের CTA, সব পৃষ্ঠার স্থির service bar।
- Bagisto-এর native image/product/category carousel, footer links ও static content section-ও ব্যবহার করা যায়। Native static HTML/CSS কেবল বিশ্বস্ত admin-কে সম্পাদনার অনুমতি দিন।
- পণ্যের নাম, ছবি, মূল্য, stock, category ও channel assignment **Catalog** থেকে আসে, theme editor থেকে নয়। Header/menu/logo-এর তথ্য Bagisto settings/catalog থেকে আসে; checkout Bagisto-এরই থাকে। রং, নতুন layout বা নতুন ধরনের সম্পূর্ণ section বানাতে এখনো developer-এর CSS/Blade/PHP কাজ প্রয়োজন—এটি একটি editable foundation, সর্বক্ষেত্রে no-code page builder নয়।
- Link-এ `/search`, `search?query=ghee`, `#ab-our-promise` বা সম্পূর্ণ `https://...` ব্যবহার করা যায়। বদলানোর পরে destination কাজ করছে কি না যাচাই করুন। Image file, image alt text এবং desktop/mobile crop নিজে দেখে নিন।

## সমস্যা হলে

- **Theme/command দেখা যাচ্ছে না:** সঠিক Bagisto version ও `bootstrap/providers.php` entry দেখুন, `php artisan optimize:clear` চালান।
- **হোমপেজ ফাঁকা বা sections নেই:** সঠিক channel-এ migration ও installer সফল হয়েছে কি না এবং theme active কি না দেখুন। পুরোনো layout override রয়ে গেছে কি না যাচাই করুন।
- **ছবি নেই:** media upload permission, storage disk/link, server/CDN থেকে asset URL ও browser console দেখুন। পণ্য carousel ফাঁকা হলে product status, channel assignment, visibility, stock ও filter যাচাই করুন।
- **Preview iframe ফাঁকা:** staging-এর `APP_URL`, HTTPS/mixed content, session/cookies এবং proxy-এর frame policy দেখুন। নিরাপত্তা নীতি অন্ধভাবে বন্ধ না করে আপনার deployment অনুযায়ী সংশোধন করুন।
- **Editor বা storefront পুরোনো দেখাচ্ছে:** `php artisan optimize:clear`, প্রয়োজনে worker/OPcache/CDN refresh ও browser reload করুন। এতে সাইটের অন্যান্য cache-ও পরিষ্কার হতে পারে।

## Rollback ও ভবিষ্যৎ আপগ্রেড

Rollback-এর সহজ পথ হলো Admin → Appearance → Themes থেকে পূর্বের থিমটি channel-এ আবার activate করা এবং cache refresh করা। AponBasket-এর section/draft ও catalog data স্বয়ংক্রিয়ভাবে মুছে যায় না। আগের hard-coded AponBasket v1.1-এ ফিরতে হলে শুধু theme selector বদলালেই হবে না: পূর্ণ পূর্বের code/config/media backup এবং প্রয়োজন হলে মিলিত database backup একটি পরিকল্পিত maintenance window-এ restore করতে হবে। প্রথমে staging-এ restore পরীক্ষা করুন; অন্য সময়ের database restore করলে পরবর্তী orders ও অন্য নতুন data হারাতে পারে।

Provider সরানো, migration rollback বা database table/marker মুছে ফেলা স্বাভাবিক rollback-এর জন্য প্রয়োজন নেই; data রেখে দেওয়াই নিরাপদ। ভবিষ্যতে Bagisto বা এই থিম upgrade-এর আগে code/database/media backup নিন, release notes তুলনা করুন, staging-এ migration ও সব critical flow পরীক্ষা করুন। Theme-এর নতুন version কখনো installer পুনরায় চালিয়ে merchant content reset করার উদ্দেশ্যে ব্যবহার করবেন না।

## পরীক্ষার তথ্য ও সীমাবদ্ধতা

- Bagisto 2.4.13 এবং 2.5.0-এ PHP 8.4 দিয়ে পৃথক integration suite: **৮টি test, ৮৩টি assertion** প্রতিটিতে। Installer preservation, theme isolation, native editor fields/draft/upload/preview/publish এবং reorder/status পরীক্ষা করা হয়েছে।
- Bagisto 2.4.13/PHP 8.3-এ GD ছাড়া পরীক্ষার জন্য upload test বাদে বাকি tests চালানো হয়েছে। Bagisto 2.5-এর একটি বিচ্ছিন্ন browser test-এ editor থেকে Bengali text ও ছবি বদলে preview, publish ও storefront ফলও যাচাই করা হয়েছে। Desktop/mobile storefront-এ responsive layout ও image loading smoke-tested।
- পরীক্ষামূলক ডেটাবেজ ছিল SQLite; upstream Bagisto migrations/seeder চালাতে শুধু test environment-এ SQLite compatibility adjustments দরকার হয়েছিল। এগুলো এই প্যাকেজে নেই। **MySQL/MariaDB, আপনার live site, payment ও বাস্তব checkout end-to-end পরীক্ষা করা হয়নি।** Production-এ নিশ্চিত করতে আপনার নিজের staging-এ সেসব পরীক্ষা আবশ্যক।

শুধু একটি **আলাদা, disposable test database ও storage**-এ optional tests চালাতে (Bagisto dependencies ও provider installed থাকতে হবে):

```bash
php tests/standalone/theme.php legacy
php tests/standalone/theme.php modern
php tests/standalone/installer.php
bash tests/standalone/deploy.sh
LARAVEL_FRAMEWORK_DIR="$PWD/vendor/laravel/framework" php tests/standalone/blade.php
php vendor/bin/phpunit tests/Feature/AponBasketThemeTest.php
```

PHPUnit চালানোর আগে `.env.testing`/`phpunit.xml`-এ test database ও test storage নিশ্চিত করুন—production database-এ কখনো test চালাবেন না।

ঐচ্ছিক browser integration test `tests/browser/editor.cjs`-এ আছে। এটি `playwright-core`, Chromium, ইংরেজি admin UI, ইতিমধ্যে চালু করা test channel এবং ফাইলের শুরুতে বর্ণিত environment variables চায়। **এই test আসলেই ছবি upload ও publish করে এবং পরিবর্তন ফিরিয়ে দেয় না**—শুধু disposable test database/storage-এ `APON_TEST_DISPOSABLE=yes` দিয়ে চালান; shared staging বা production-এ নয়। Test login তথ্য নিরাপদ environment থেকে দিন, repository বা chat-এ লিখবেন না।

`preview/index.html` একটি static design reference, live Bagisto নয়; এর নমুনা পণ্যও আপনার আসল catalog নয়। Fonts-এর SIL OFL license `public/themes/shop/aponbasket/fonts/`-এ রাখা আছে।
