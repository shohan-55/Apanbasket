# Customisation guide

Every visual decision in this theme lives in three places: the CSS tokens, the two view
directories and the theme images. Nothing is compiled, so you can edit a file and refresh
the browser (run `php artisan view:clear` if a Blade change does not show up).

---

## 1. Colours, type and spacing

`public/themes/shop/aponbasket/css/theme.css`, first block:

```css
:root {
    --ab-green-900: #0b3a21;   /* footer, headings */
    --ab-green:     #1a7a44;   /* primary buttons */
    --ab-leaf:      #7fb03a;   /* accents, icons */
    --ab-gold:      #d9a441;   /* stars, highlights */
    --ab-cream:     #fcf9f2;   /* page background */
    --ab-line:      #e7dfcd;   /* borders */

    --ab-font-body:    "Poppins", ...;
    --ab-font-display: "Fraunces", ...;   /* headings */
    --ab-font-bn:      "Hind Siliguri", ...;  /* Bengali text */
}
```

Change a token and every button, card, badge and section follows. If you replace the
display font, update the Google Fonts `<link>` in `views/layouts/index.blade.php` too.

Layout rhythm:

- `.ab-section` → vertical padding of homepage sections (`--ab-section-y`)
- `.ab-container` → max width (`--ab-shell`, default 1280px) and side padding
- `.ab-card` radius, shadows → `--ab-radius-lg`, `--ab-shadow*`

---

## 2. Homepage content

Open `resources/themes/aponbasket/views/home/index.blade.php`. The `@php` block at the top
holds everything you are most likely to edit:

```php
$slides = [
    [
        'tag'       => '100% Pure & Natural',
        'bengali'   => 'খাঁটি স্বাদের নিশ্চয়তা',
        'title_top' => 'Home Made',
        'title_em'  => 'Gawa Ghee',
        'text'      => '...',
        'price'     => '৳1,450',
        'compare'   => '৳1,750',
        'chip'      => 'Save 17%',
        'image'     => $asset('products/ghee.jpg'),
        'cta'       => 'Shop Gawa Ghee',
        'cta_url'   => route('shop.search.index', ['query' => 'ghee']),
        ...
    ],
    ...
];
```

- Add or remove an array entry to add or remove a hero slide (the dots and arrows adapt).
- `cta_url` can point to a category URL, a search query or a CMS page.
- Keep images square, 1000 × 1000 px or larger.

Category tiles come from your catalogue automatically. The six fallback tiles (shown when
the store has no categories yet) are right below the `$slides` block.

Product rows are live Bagisto data:

```blade
<x-shop::products.carousel
    title="Our best sellers"
    eyebrow="Customer favourites"
    subtitle="Loved by thousands of families across Bangladesh."
    :src="route('shop.api.products.index')"
    :navigation-link="route('shop.search.index')"
/>
```

`src` accepts any product API filter, e.g.:

| Goal | `src` |
| --- | --- |
| Cheapest first | `route('shop.api.products.index', ['sort' => 'price-asc'])` |
| Newest first | `route('shop.api.products.index', ['sort' => 'created_at-desc'])` |
| Products of one category | `route('shop.api.products.index', ['category_id' => 3])` |
| Search result row | `route('shop.api.products.index', ['query' => 'ghee'])` |

---

## 3. Header and footer

| What | File |
| --- | --- |
| Announcement marquee + support phone | `aponbasket/announcement.blade.php` (`$offers`, `$supportPhone`) |
| Category menu depth (max 7 entries) | `aponbasket/menu.blade.php` (`array_slice`) |
| Search placeholder, icons, account menu | `aponbasket/desktop-nav.blade.php` |
| Mobile drawer links | `aponbasket/mobile-nav.blade.php` |
| Footer columns, contact, socials, payments | `layouts/footer/index.blade.php` (`$columns`, `$contact`, `$socials`, `$payments`) |
| Four promise badges | `layouts/services.blade.php` (`$services`) |
| WhatsApp number | `aponbasket/fabs.blade.php` (`$whatsapp`) |

---

## 4. Product card

`views/products/card.blade.php` — two layouts (`grid`, and `list` when `mode == 'list'`).
It must keep:

- the Vue component name `v-product-card` and the template id `v-product-card-template`,
- the props `mode` and `product`,
- the `add-to-cart`, `add-to-wishlist`, `add-to-compare` handlers and the events they emit
  (`add-flash`, `update-mini-cart`).

Inside the template, `@{{ product.name }}` is Vue interpolation and `{{ route(...) }}` is
Blade. Keep that difference when you edit.

Card styles: `.ab-card*` in `theme.css` (section 7 + section 20 for Bagisto's price markup).

---

## 5. Product photography

Replace files in `public/themes/shop/aponbasket/images/products/` keeping the same names:

| File | Used in |
| --- | --- |
| `ghee.jpg` | hero slide 1, story block, demo cards |
| `turmeric.jpg` | hero slide 2, promo banner, demo cards |
| `mustard-oil.jpg` | hero slide 3, promo banner, demo cards |
| `chili.jpg` | demo product card |
| `honey.jpg` | demo product card |
| `dates.jpg` | demo product card |

Real catalogue images come from Bagisto's product media, not from this folder.

---

## 6. Adding a new homepage section

Copy an existing section in `home/index.blade.php` and reuse the building blocks:

```blade
<section class="ab-section">
    <div class="ab-container">
        <div class="ab-section__head ab-section__head--center" data-ab-reveal>
            <span class="ab-eyebrow ab-eyebrow--center">Label</span>
            <h2 class="ab-title">Title with <em>accent</em></h2>
        </div>

        <div class="ab-grid">
            <!-- your cards -->
        </div>
    </div>
</section>
```

Available section modifiers: `.ab-section--tight`, `.ab-section--soft`, `.ab-section--white`.
Add `data-ab-reveal` with `style="--ab-delay: 80ms"` for the fade-up animation.

---

## 7. RTL and Bengali

The layout renders `dir` from the active locale, and the theme's components use logical
properties (`inset-inline-start`, `margin-inline`, …) so Arabic/Urdu stay correct.
Bengali text uses `--ab-font-bn` (Hind Siliguri), loaded from Google Fonts in the layout.
