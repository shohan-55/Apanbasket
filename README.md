# AponBasket — Organic Storefront Theme for Bagisto

> aponbasket.com এর জন্য বানানো সম্পূর্ণ **Bagisto storefront theme** —
> ১০০% ন্যাচারাল / অর্গানিক প্রোডাক্ট সেল করার জন্য ডিজাইন করা।

A complete, ready-to-install Bagisto theme for natural & organic products: cream + deep-green
palette, serif editorial typography, a 3-slide hero, category tiles, product rails, brand story,
promise badges, testimonials, coupon CTA and a branded footer — all responsive, all server-rendered.

---

## ✨ কী কী আছে (What's included)

| অংশ | বিস্তারিত |
| --- | --- |
| **Homepage** | ৩টি হিরো স্লাইড (Gawa Ghee / Turmeric / Mustard Oil), ৪টি প্রমিজ ব্যাজ, ক্যাটাগরি টাইল, Best Sellers ও New Arrivals প্রোডাক্ট রেইল, ২টি প্রোমো ব্যানার, ব্র্যান্ড স্টোরি, "Why AponBasket", ৩ ধাপের প্রসেস, টেস্টিমোনিয়াল, কুপন CTA |
| **Header** | ঘোষণা বার (মার্কি + ফোন), ব্র্যান্ড লোগো, ডাইনামিক ক্যাটাগরি মেনু (সাব-মেনু সহ), সার্চ, কমপেয়ার, অ্যাকাউন্ট ড্রপডাউন, মিনি-কার্ট |
| **Footer** | নিউজলেটার CTA, কোম্পানি/হেল্প লিংক, যোগাযোগ, সোশ্যাল, bKash/Nagad/Rocket/COD চিপ |
| **Product card** | কাস্টম ডিজাইন করা কার্ড — Sale/New ব্যাজ, উইশলিস্ট, কমপেয়ার, কুইক অ্যাড-টু-কার্ট (Bagisto-র নিজের Vue কম্পোনেন্ট, তাই কার্ট/উইশলিস্ট আগের মতোই কাজ করে) |
| **Product rail** | স্ক্রল-করা প্রোডাক্ট রেইল, eyebrow + title + "View all" |
| **অন্যান্য পেজ** | ক্যাটাগরি, সার্চ, প্রোডাক্ট ডিটেইল, কার্ট, চেকআউট, লগইন — সবই থিমের কালার ও টাইপোগ্রাফি পায় |
| **Extra** | মোবাইল ড্রয়ার মেনু, WhatsApp ফ্লোটিং বাটন, ব্যাক-টু-টপ, স্ক্রল অ্যানিমেশন, কুপন কপি |

---

## 🚀 ইনস্টল (৩ ধাপ)

```bash
# ১. ফাইল কপি করুন (Bagisto রুট থেকে)
bash scripts/install.sh /path/to/your/bagisto
```

**২.** `config/themes.php` এর `'shop' => [ ... ]` অ্যারেতে নতুন এন্ট্রি বসান:

```php
'aponbasket' => [
    'name' => 'AponBasket Organic',
    'assets_path' => 'public/themes/shop/aponbasket',
    'views_path' => 'resources/themes/aponbasket/views',

    'vite' => [
        'hot_file' => 'shop-default-vite.hot',
        'build_directory' => 'themes/shop/default/build',
        'package_assets_directory' => 'src/Resources/assets',
    ],
],
```

**৩.** ক্যাশ ক্লিয়ার করে চ্যানেলে থিমটা সিলেক্ট করুন:

```bash
php artisan optimize:clear
```

Admin → **Settings → Channels → Design/Theme → AponBasket Organic → Save** →
ব্রাউজারে `Ctrl/Cmd + Shift + R`।

ধাপে ধাপে বিস্তারিত: [`docs/INSTALL.md`](docs/INSTALL.md) · কাস্টমাইজ: [`docs/CUSTOMIZATION.md`](docs/CUSTOMIZATION.md)

---

## 🖼 প্রিভিউ

- **লাইভ প্রিভিউ:** `preview/page.html` — পুরো ডিজাইনের স্ট্যাটিক HTML (Bagisto ছাড়াই খোলা যায়)।
- **আসল সাইট:** ইনস্টলের পর `https://aponbasket.com`।

---

## 🎨 নিজের মতো বদলানো

| কী বদলাবেন | কোথায় |
| --- | --- |
| রঙ, ফন্ট, রেডিয়াস | `public/themes/shop/aponbasket/css/theme.css` → `:root` |
| হিরো স্লাইডের লেখা/ছবি/লিংক | `resources/themes/aponbasket/views/home/index.blade.php` → `$slides` |
| স্টোরি, Why, Steps, টেস্টিমোনিয়াল | একই ফাইল, নিচের সেকশনগুলো |
| ফুটারের লিংক ও যোগাযোগ | `views/layouts/footer/index.blade.php` → `$columns`, `$contact` |
| ঘোষণা বার ও ফোন নম্বর | `aponbasket/announcement.blade.php` |
| WhatsApp নম্বর | `aponbasket/fabs.blade.php` |
| ৪টি প্রমিজ | `views/layouts/services.blade.php` |
| প্রোডাক্ট ছবি | `public/themes/shop/aponbasket/images/products/` (একই ফাইলনামে বদলান) |

---

## 📌 টেক নোট

- **Bagisto 2.3.x / 2.4.x** — দুটোতেই কাজ করে (2.5.x-ও একই view resolution ব্যবহার করে)।
- কোনো **npm/Vite build লাগে না**, কোনো composer package নেই, কোনো DB migration নেই।
- Bagisto-র core file (`packages/Webkul/Shop`) **ছোঁয়া হয়নি** — সব override থিমের `views_path` থেকে হয়,
  তাই Bagisto আপডেট করলে ডিজাইন নষ্ট হবে না।
- কার্ট, উইশলিস্ট, কমপেয়ার, লগইন, সার্চ, চেকআউট — Bagisto-র নিজের কম্পোনেন্টই ব্যবহার হয়েছে।
- `images/products/` এর ছবিগুলো ডেমো/প্লেসহোল্ডার — নিজের প্রোডাক্টের আসল ছবি দিয়ে বদলে নিন।
- `images/brand/` এ আপনার দেওয়া ৪টি মার্কেটিং ক্রিয়েটিভ রাখা আছে (প্রোডাক্ট ছবি/সোশ্যাল পোস্ট হিসেবে ব্যবহার করুন)।

---

## 📄 লাইসেন্স

Theme code: MIT. Bagisto itself is licensed separately (OSL-3.0); this theme only adds views,
CSS and images on top of it.
