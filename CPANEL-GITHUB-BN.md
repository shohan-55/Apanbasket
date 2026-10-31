# GitHub থেকে cPanel-এ AponBasket থিম আপডেট

এই repository-তে এখন **Bagisto v2.5.0-এর পূর্ণ upstream source এবং AponBasket থিম** আছে। তবে নিচের helper একটি আলাদা source checkout থেকে **শুধু থিমের তিনটি নির্দিষ্ট folder** অন্য একটি আগে থেকে ইনস্টল করা Bagisto project-এ copy করে; এটি Bagisto core update করে না। আপনার ZIP দিয়ে ইনস্টল করা চালু Bagisto-এর folder-এ এই repository সরাসরি `git clone`, `git pull` বা পুরো source overwrite করবেন না। আগে আলাদা staging, version/customization তুলনা, backup এবং পরিকল্পিত migration দরকার।

> প্রথমে আলাদা staging-এ সম্পূর্ণ পরীক্ষা করুন। আপনার বর্তমান Bagisto version, প্রকৃত project path ও server configuration এখনো জানা নেই। Production-এ প্রয়োগের আগে database, project ও media backup এবং maintenance window প্রয়োজন। এই helper production server-এ এখনো পরীক্ষা করা হয়নি। বিস্তারিত compatibility ও rollback: [README-BN.md](README-BN.md)।

## প্রথমবার: GitHub checkout

cPanel → **Terminal** খুলে দেখুন Git আছে কি না:

```bash
git --version
php -v
```

নিচের উদাহরণে পরীক্ষিত কাজের branch `arena/811990de-apanbasket` ব্যবহার করা হয়েছে। `main`-এ pull request merge হয়েছে কি না আলাদাভাবে GitHub-এ যাচাই করুন। Repository public, তাই code clone করতে GitHub password বা token প্রয়োজন নেই। প্রথমবার:

```bash
cd "$HOME"
git clone --branch arena/811990de-apanbasket --single-branch https://github.com/shohan-55/Apanbasket.git aponbasket-theme-source
cd "$HOME/aponbasket-theme-source"
git status --short --branch
git log -1 --oneline
```

আগেই `aponbasket-theme-source` নামে directory থাকলে সেটি মুছে বা replace করবেন না; আগে সেটি কী এবং কোন repository তা যাচাই করুন। Checkout এবং backups ওয়েব থেকে সরাসরি অ্যাক্সেসযোগ্য directory-র বাইরে রাখুন। কেবল clone করায় live Bagisto বদলায় না।

## প্রকৃত Bagisto root নির্ণয়

যে directory-তে `artisan`, `bootstrap/providers.php` এবং `packages/Webkul/` আছে সেটিই project root। `public_html` সবসময় project root নাও হতে পারে। সঠিক project folder-এ গিয়ে যাচাই করুন:

```bash
pwd
php -v
grep 'BAGISTO_VERSION' packages/Webkul/Core/src/Core.php
```

PHP CLI version ও Bagisto version মিলিয়ে নিন। পরীক্ষা করা হয়েছে Bagisto **2.4.13** ও **2.5.0**-এ; অন্য সংস্করণ বা অন্য package layout-এর জন্য আগে developer দিয়ে compatibility যাচাই করুন। কোনো password, `.env` বা credential কাউকে পাঠাবেন না।

পরবর্তী কমান্ডগুলোতে `BAGISTO_ROOT`-এ `pwd` থেকে পাওয়া **সঠিক staging project path** বসাতে হবে; নিচের path কেবল উদাহরণ, না বদলে চালাবেন না:

```bash
BAGISTO_ROOT='/সঠিক/staging/Bagisto/project/path'
```

## প্রথম installation বা যাচাই করা update

1. Database, media storage, project code ও `bootstrap/providers.php` আলাদাভাবে backup নিন। Helper শুধু থিমের আগের file এবং `bootstrap/providers.php`-এর file backup নেয়; database, uploads, orders বা পুরো project backup নেয় না।
2. cPanel home-এ ওয়েবের বাইরে একটি private backup directory তৈরি করুন। আগে থেকেই একই directory থাকলে সেটির access permission পরীক্ষা করুন।

   ```bash
   mkdir -p -m 700 "$HOME/aponbasket-theme-backups"
   ```

3. Theme checkout থেকে **শুধু read-only পরীক্ষা** চালান। এটি কী যোগ/overwrite হবে দেখায়, copy করে না:

   ```bash
   cd "$HOME/aponbasket-theme-source"
   bash scripts/deploy-theme.sh --target "$BAGISTO_ROOT"
   ```

   লক্ষ্য করুন: আলাদা backup root, সঠিক Bagisto path, দেখানো file list ও version। আগে v1.1-এর `resources/themes/aponbasket/views/components/layouts/index.blade.php` থাকলে helper সতর্ক করবে ও apply বন্ধ রাখবে। ফাইলটি নিজে সরায় না; backup নিয়ে সেটি পুরোনো override কি না ও স্থানীয় custom পরিবর্তন আছে কি না developer দিয়ে যাচাই করুন।
4. Staging-এ পরিবর্তন অনুমোদিত হলে এবং তিনটি theme folder-এর কোনো স্থানীয় custom code overwrite হচ্ছে না নিশ্চিত হলে তবেই:

   ```bash
   bash scripts/deploy-theme.sh --target "$BAGISTO_ROOT" \
     --backup-root "$HOME/aponbasket-theme-backups" --apply
   ```

   প্রতিবার পরিবর্তনের আগে helper একটি unique backup folder বানায়। শুধু `app/AponBasket`, `resources/themes/aponbasket` এবং `public/themes/shop/aponbasket`-এর নতুন/ভিন্ন ফাইল copy করে। অন্য ফাইল মুছে না, database স্পর্শ করে না। Existing extra files-ও রেখে দেয়, তাই কোনো পুরোনো obsolete file সরানো দরকার কি না আলাদাভাবে পর্যালোচনা করতে হবে। Copy মাঝপথে ব্যর্থ হলে আউটপুটে দেওয়া backup path দেখে partial deployment পরীক্ষা করুন।
5. **প্রথম installation-এ**, [README-BN.md](README-BN.md)-এর নির্দেশনা অনুযায়ী `bootstrap/providers.php`-এ provider লাইন নিজে যোগ করতে হবে। Helper এটি করে না। যথাযথ backup-এর পরে সঠিক PHP CLI ব্যবহার করে শুধু থিম migration ও প্রতি-channel installer চালান:

   ```bash
   cd "$BAGISTO_ROOT"
   php artisan optimize:clear
   php artisan migrate --path=app/AponBasket/Theme/Database/Migrations --force
   php artisan aponbasket:install --channel=default
   php artisan optimize:clear
   ```

   `default` উদাহরণ; Admin → Settings → Channels থেকে আসল channel **code** বসান। একাধিক channel হলে installer প্রতিটির জন্য আলাদা চালান। `optimize:clear` পুরো application-এর caches-এ প্রভাব ফেলতে পারে। নতুন থিম activation এবং logo সেটআপও admin থেকে নিজে করতে হবে।
6. Staging-এ Appearance editor, preview/publish, homepage, product, search, cart, checkout, mobile ও আপনার extension পরীক্ষা করুন। সব ঠিক থাকলে এবং পূর্ণ backup থাকার পর maintenance window-তে production-এ একই পদ্ধতি প্রয়োগ করুন।

## পরবর্তী উন্নয়নের পর নিয়মিত update

প্রথমে source checkout-এ যান, GitHub থেকে একই branch-এর সর্বশেষ commit নিন ও পর্যালোচনা করুন:

```bash
cd "$HOME/aponbasket-theme-source"
git status --short --branch
git pull --ff-only origin arena/811990de-apanbasket
git log -1 --oneline
```

তারপর সঠিক staging path দিয়ে উপরের read-only check ও `--apply` ধাপ পুনরাবৃত্তি করুন। শুধু `git pull` করলে Bagisto site আপডেট হয় না—তা কেবল আলাদা source checkout বদলায়। প্রয়োগের পর উপযুক্ত cache/OPcache refresh করুন এবং নতুন release instructions-এ migration প্রয়োজন হলে backup ও পর্যালোচনার পর চালান।

`git pull --ff-only` যদি local change বা divergent history-র কারণে থামে, error পড়ুন এবং developer দিয়ে review করান; `git reset --hard`, `git clean -fd` বা জোর করে merge চালাবেন না। Helper-ও local/uncommitted checkout ব্যবহার করে apply করতে স্বাভাবিকভাবে অস্বীকার করে। Admin-এ করা theme content ও draft database-এ থাকে—Git pull সেগুলো সম্পাদনা করে না। তবে code/API পরিবর্তনের কারণে সাইটের আচরণ বদলাতে পারে, তাই প্রতিটি update staging-এ পরীক্ষা করুন।

## Rollback

আগের theme admin থেকে পুনরায় activate করা সাধারণত সবচেয়ে সহজ। Helper যে backup directory দেখায় সেখানে আগের তিনটি theme folder ও provider file-এর কপি এবং একটি manifest থাকে; database/media নেই। পুরোনো code restore বা নতুন file সরানোর আগে maintenance window-তে backup-এর সঙ্গে বর্তমান ফাইল তুলনা করে developer দিয়ে পরিকল্পিত restore করুন। অন্ধভাবে পুরো `app`, `resources`, `public` বা database replace করলে অন্যান্য পরিবর্তন/নতুন order হারাতে পারে।

## নিরাপত্তা

- GitHub-এ `.env`, passwords, database dump, customer data বা uploads রাখবেন না। Public repository-তে committed যেকোনো তথ্য সবাই দেখতে পারে।
- এই পদ্ধতি auto-deploy নয়: `pull`, review, backup ও apply প্রতিটি ধাপ আপনার নিয়ন্ত্রণে থাকে। সার্ভারে কোনো webhook, credential বা scheduler তৈরি করা হয় না।
- `--allow-dirty` বা `--allow-custom-layout` safeguards bypass করে; কারণ সম্পূর্ণ বুঝে review না করে ব্যবহার করবেন না।
