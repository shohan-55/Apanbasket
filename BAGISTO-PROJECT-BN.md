# AponBasket-এর পূর্ণ Bagisto source project

এই repository-তে অফিসিয়াল [Bagisto v2.5.0](https://github.com/bagisto/bagisto/releases/tag/v2.5.0) source snapshot এবং AponBasket theme 2.0.3 একসঙ্গে আছে। Upstream commit: `4e7f08621904a665b378112e1a39b837c27a4c3f`। Bagisto-এর MIT লাইসেন্স [LICENSE](LICENSE)-এ অক্ষত আছে। এটি **ফাইলের snapshot**; অফিসিয়াল repository-এর সম্পূর্ণ Git history আমদানি করা হয়নি এবং upstream update স্বয়ংক্রিয়ভাবে আসে না।

## কোন ফাইল কোথায়

| Path | কাজ |
| --- | --- |
| `packages/Webkul/`, `config/`, `artisan`, `composer.json`, `composer.lock` | Bagisto v2.5.0 core source ও নির্ভরতার তালিকা |
| `app/AponBasket/Theme/` | থিমের provider, editor section, installer ও migration |
| `resources/themes/aponbasket/views/` | storefront-এর Blade views |
| `public/themes/shop/aponbasket/` | থিমের CSS, ছবি ও fonts |
| `bootstrap/providers.php` | এই source project-এ থিম provider আগে থেকেই নিবন্ধিত |
| `docs/upstream/bagisto-v2.5.0/` | upstream README এবং নিরাপত্তার জন্য নিষ্ক্রিয় রাখা upstream GitHub workflows |

থিম নিবন্ধিত থাকলেও **স্বয়ংক্রিয়ভাবে install বা activate হয় না**। পূর্ণ Bagisto installation ও core migration-এর পরে আলাদাভাবে থিম installer চালাতে হয়; channel-এ থিম activate করতে হয়। এতে আগে থেকে থাকা merchant content জোর করে বদলায় না।

## Source code ও চালু সাইট এক জিনিস নয়

Upstream-এর মতো এখানে `vendor/`, `node_modules/`, বাস্তব `.env`, database, logs এবং ব্যবহারকারীর upload রাখা হয়নি। Composer ও সংশ্লিষ্ট tooling দিয়ে dependencies ইনস্টল করতে হয়; শুধু GitHub source download বা `git pull` একটি পূর্ণ চালু application বানায় না। Bagisto 2.5.0-এর জন্য **PHP 8.4 বা তার বেশি** এবং upstream-এর প্রয়োজনীয় extensions/database লাগবে।

`.env.example` হলো upstream-এর নমুনা, production configuration নয়: সেখানে `APP_DEBUG=true` ও উদাহরণ URL আছে। বাস্তব `.env`, passwords, keys, customer data, orders, database dump এবং media কখনো GitHub-এ commit করবেন না। Checkout ও `.git` directory ওয়েব থেকে সরাসরি অ্যাক্সেসযোগ্য রাখবেন না; web server-এর document root সঠিকভাবে `public/`-এ সীমাবদ্ধ করতে হবে।

## আলাদা staging-এ শুরু করুন

1. Production থেকে আলাদা directory, URL, **আলাদা disposable database**, credentials ও media storage ব্যবহার করুন। আপনার বর্তমান site-এর database বা `.env` এখানে যুক্ত করবেন না।
2. এই repository-এর নির্দিষ্ট branch/commit checkout করুন। PHP 8.4+, Composer, প্রয়োজনীয় extensions, web-server document root এবং database-এর শর্ত যাচাই করুন।
3. [Upstream Bagisto installation guide](https://devdocs.bagisto.com/getting-started/installation.html) অনুযায়ী নতুন staging environment configure করুন, dependencies install করুন এবং শুধু সেই খালি test database-এ Bagisto install/migrate করুন। Installation বা seed command বিদ্যমান production database-এ চালাবেন না।
4. Core installation সম্পন্ন হলে [README-BN.md](README-BN.md)-এর নির্দেশনায় শুধু theme marker migration ও উপযুক্ত channel-এর `aponbasket:install` চালান, তারপর Admin → **Appearance → Themes** থেকে থিম activate করুন। এই repo-এর provider আগেই নিবন্ধিত; দ্বিতীয়বার যোগ করবেন না।
5. Staging-এ admin editor, draft/preview/publish, logo, product/catalog, search, cart, checkout, mobile ও আপনার extensions পরীক্ষা করুন। Database, media, code ও configuration backup এবং restore পরীক্ষা ছাড়া production-এ যাবেন না।

## আগে থেকে ZIP দিয়ে ইনস্টল করা cPanel site

এই repository clone করে পুরোনো live folder-এর ওপর পুরো project overwrite বা সরাসরি `git pull` করবেন না। আগে site-এর **সঠিক Bagisto/PHP version**, application path, local customization, `vendor`, `.env`, media ও database কীভাবে পরিচালিত হচ্ছে তা তুলনা করুন। পুরোনো site-কে পূর্ণ Git-managed project-এ নেওয়া আলাদা migration/deployment কাজ; এটির জন্য backup, version-specific upgrade plan, staging এবং rollback পরীক্ষা দরকার। শুধু source যোগ করে বা একবার pull করে সেটি সম্পন্ন হয় না।

শুধু থিম নিরাপদে পরীক্ষা করে বিদ্যমান application-এ কপি করার আলাদা workflow [CPANEL-GITHUB-BN.md](CPANEL-GITHUB-BN.md)-এ আছে। এর helper কেবল তিনটি theme folder কপি করে—Bagisto core, Composer dependencies, database migration, provider registration বা activation আপডেট করে না।

## Upstream-এর সঙ্গে ইচ্ছাকৃত পার্থক্য

- `bootstrap/providers.php`-এ AponBasket provider যোগ করা হয়েছে; `packages/Webkul/`-এর upstream source বদলানো হয়নি।
- Root README-তে AponBasket তথ্য যোগ করা হয়েছে; অবিকৃত upstream README `docs/upstream/bagisto-v2.5.0/README.md`-এ আছে। `.gitignore`-এ local environment ও SQLite database-এর বাড়তি সুরক্ষা যোগ করা হয়েছে।
- Upstream-এর পাঁচটি GitHub workflow অবিকৃত অবস্থায় `docs/upstream/bagisto-v2.5.0/disabled-workflows/`-এ রাখা হয়েছে; সেগুলো এই repository-তে **সক্রিয় নয়**। Upstream workflow-গুলোর একটি `webkul/bagisto` Docker image publish করতে পারে এবং অন্যগুলো বড় CI matrix চালায়, তাই অনুমতি/কনফিগারেশন যাচাই ছাড়া সেগুলো স্বয়ংক্রিয়ভাবে চালু করা হয়নি। বাকি `.github` metadata ও upstream `CHANGELOG.md` সংরক্ষিত।
## পরীক্ষার ফল ও সীমাবদ্ধতা

- এই source-এর একটি বিচ্ছিন্ন checkout ও আলাদা SQLite-adapted test database-এ AponBasket integration suite **১১টি test / ১১৬টি assertion** পাস করেছে। একই checkout-এ upstream cross-package unit suite **৩৭টি test / ৬০টি assertion** এবং ThemeStorage unit suite **৫০টি test / ৫৯টি assertion** পাস করেছে।
- `php tests/standalone/project.php` source version, provider registration ও upstream Vite manifest-এর asset উপস্থিতি পরীক্ষা করে। থিমের standalone contract, Blade এবং deployment-helper safety tests-ও পাস করেছে।
- SQLite compatibility পরিবর্তন শুধু test database প্রস্তুতের জন্য ব্যবহার করা হয়েছিল, এই source-এ নয়। পূর্ণ upstream test suite, MySQL/MariaDB, প্রকৃত checkout/payment এবং আপনার cPanel/live site এখানে পরীক্ষা করা হয়নি।
