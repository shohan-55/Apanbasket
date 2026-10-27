# AponBasket — Bagisto Storefront Theme

A complete organic / natural products storefront theme for **Bagisto 2.x**, built for
[aponbasket.com](https://aponbasket.com). Cream + deep-green palette, serif display type,
editorial homepage sections and a product card designed for food and grocery catalogues.

- **Theme code:** `aponbasket`
- **Compatible with:** Bagisto 2.3.x, 2.4.x (2.5.x shares the same view resolution)
- **Requires:** nothing extra — no Node build step, no composer package, no database migration

---

## 1. What is inside

```
public/themes/shop/aponbasket/
├── css/
│   ├── theme.css        design tokens, header, footer, cards, catalog pages, forms
│   └── home.css         hero, categories, product rails, story, testimonials, CTA
├── js/
│   └── theme.js         hero slider, scroll reveal, sticky header, back-to-top, coupon copy
└── images/
    ├── brand/           your four marketing creatives + logo / favicon
    ├── products/        clean product photography used by the demo content
    ├── logo.svg         horizontal logo (for admin upload, invoices, emails)
    ├── logo-light.svg   logo for dark backgrounds
    ├── favicon.svg
    └── product-placeholder.svg

resources/themes/aponbasket/views/
├── layouts/
│   ├── index.blade.php                      the storefront layout (fonts + theme CSS)
│   ├── header/index.blade.php               announcement bar + branded header
│   ├── footer/index.blade.php               branded footer with newsletter + payments
│   └── services.blade.php                   the four "promise" badges
├── home/index.blade.php                     the homepage (hero → categories → products → …)
├── products/
│   ├── card.blade.php                       product card (same Vue component as core)
│   └── carousel.blade.php                   product rail with eyebrow + "view all"
└── aponbasket/
    ├── icons.blade.php         inline SVG icon sprite
    ├── brand.blade.php         logo lockup (channel logo or built-in wordmark)
    ├── announcement.blade.php  the marquee above the header
    ├── menu.blade.php          builds the category menu once per request
    ├── desktop-nav.blade.php   desktop header
    ├── mobile-nav.blade.php    mobile header + drawer menu
    └── fabs.blade.php          floating WhatsApp + back-to-top buttons

preview/page.html              static, framework-free preview of the whole design
docs/                          install + customisation guides
scripts/install.sh             copies the theme into a Bagisto installation
```

Bagisto resolves every Blade view in `views_path` **before** the package copy, so the
theme overrides the core header, footer, homepage and product card without editing
`packages/Webkul/Shop` (edits there are lost on every update).

---

## 2. Installation

### 2.1 Copy the files

From this repository:

```bash
bash scripts/install.sh /path/to/your/bagisto
```

or manually copy these two folders into your Bagisto root:

```
public/themes/shop/aponbasket     →  <bagisto>/public/themes/shop/aponbasket
resources/themes/aponbasket       →  <bagisto>/resources/themes/aponbasket
```

### 2.2 Register the theme

Open `config/themes.php` and add this entry **inside** the `'shop' => [ ... ]` array,
after the `default` theme:

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

> The `vite` block points at the default theme's compiled build. That is intentional —
> it keeps Bagisto's core CSS, icon font and Vue application working while this theme
> supplies the design layer on top. Do **not** set `shop-default` to `aponbasket`.

### 2.3 Clear the cache

```bash
php artisan optimize:clear
```

### 2.4 Activate it on your channel

Admin → **Settings → Channels → (your channel) → Design / Theme → AponBasket Organic → Save**.

Then hard-refresh the storefront (`Ctrl/Cmd + Shift + R`). The homepage, header, footer and
product cards are now the AponBasket design.

### 2.5 Upload your logo (optional)

Admin → **Settings → Channels → Logo**. Until a logo is uploaded the theme renders its
built-in AponBasket wordmark. Ready-made files: `images/logo.svg` and `images/logo-light.svg`.

---

## 3. Recommended content setup

The theme is designed for a food / organic catalogue. To make the homepage look its best:

1. **Categories** — create at least four (e.g. *Ghee & Butter, Spices & Masala, Cold Pressed Oil,
   Honey*). They drive the header menu and the "Shop by category" tiles automatically.
2. **Products** — add products with square images (1000 × 1000 px). The card crops to a square.
3. **CMS pages** — the footer links to these slugs; create them in Admin → **CMS → Pages**
   (or edit the footer): `about-us`, `why-organic`, `shipping-and-delivery`,
   `returns-and-refunds`, `privacy-policy`, `terms-of-service`.
4. **Channel logo + favicon** — upload your final artwork.
5. **Newsletter** — enable Admin → **Configuration → Customers → Newsletter** for the
   footer and homepage signup forms to appear.

---

## 4. How the theme works (for developers)

| Piece | Where |
| --- | --- |
| Colour, type, radius, shadows | `theme.css` → section 1 (`:root` tokens) |
| Homepage content (hero slides, story, testimonials…) | `home/index.blade.php` → `@php` block at the top |
| Footer columns, contact info, payment chips | `layouts/footer/index.blade.php` → `@php` block |
| Announcement messages, phone number | `aponbasket/announcement.blade.php` |
| WhatsApp number | `aponbasket/fabs.blade.php` |
| Four promise badges | `layouts/services.blade.php` |
| Product photography | `public/themes/shop/aponbasket/images/products/*.jpg` |
| Cart / wishlist / search / checkout | untouched core Bagisto components |

The header and card keep Bagisto's own routes, API calls and Vue component names
(`v-product-card`, `v-products-carousel`, `v-mini-cart`, `x-shop::dropdown`,
`x-shop::drawer`), so cart, wishlist, compare, login and search keep working exactly
as in the default theme.

The full stylesheet is loaded as a plain `<link>` from the theme layout — there is no
Vite build to run and no npm step on your server.

---

## 5. Notes & known behaviour

- **Appearance → Sections** (Bagisto 2.4+) is not used by this theme: the homepage is
  composed in `home/index.blade.php`. When the admin opens the Appearance preview, the
  theme still renders the sections so the editor keeps working.
- **WebMCP tool declarations** (`x-shop::layouts.webmcp`) are not part of the theme
  layout; everything else the storefront relies on is kept (render events, meta/currency
  tags, `@bagistoVite`, `@stack` hooks, `#app` mount, flash group, confirm modal).
- The layout is a copy of the core layout. After a **major** Bagisto upgrade, compare it
  with `packages/Webkul/Shop/src/Resources/views/components/layouts/index.blade.php`.
- Demo product images shipped in `images/products/` are AI-generated placeholders —
  replace them with photographs of your own products.

---

## 6. Uninstall

Switch the channel theme back to **Default** (Admin → Channels), then delete
`resources/themes/aponbasket`, `public/themes/shop/aponbasket` and the entry in
`config/themes.php`, and run `php artisan optimize:clear`.
