<?php

namespace App\AponBasket\Theme;

/**
 * The original ZIP's copy, artwork and order. These values are imported only on
 * first installation; after that the merchant's published/draft data is authoritative.
 */
final class Defaults
{
    public const THEME = 'aponbasket';

    public const VERSION = '2.0.1';

    private const IMAGES = 'themes/shop/aponbasket/images/';

    /** @return list<array{type: string, name: string, options: array}> */
    public static function sections(): array
    {
        return [
            [
                'type' => 'apon_announcement',
                'name' => 'ঘোষণা বার',
                'options' => [
                    'text' => 'প্রকৃতির কাছ থেকে আপনার পরিবারের জন্য খাঁটি খাবার',
                    'link_text' => 'পণ্য দেখুন',
                    'link_url' => 'search',
                ],
            ],
            [
                'type' => 'apon_hero',
                'name' => 'হিরো ব্যানার',
                'options' => [
                    'eyebrow' => 'APONBASKET · NATURAL & ORGANIC',
                    'title_start' => 'প্রকৃতির খাঁটি স্বাদ,',
                    'title_highlight' => 'আপনার পরিবারের',
                    'title_end' => 'জন্য',
                    'description' => 'খাঁটি গাওয়া ঘি, ঘানি ভাঙা সরিষার তেল আর যত্নে তৈরি মসলা—বিশ্বস্ত উৎস থেকে বেছে নেওয়া প্রতিদিনের প্রয়োজনীয় পণ্য।',
                    'image' => self::IMAGES.'gawa-ghee-promo.jpg',
                    'image_alt' => 'AponBasket home made gাওয়া ghee',
                    'primary_text' => 'সব পণ্য দেখুন',
                    'primary_url' => 'search',
                    'secondary_text' => 'আমাদের অঙ্গীকার',
                    'secondary_url' => '#ab-our-promise',
                    'proof_title' => 'প্রতিদিনের খাবারে আস্থার সঙ্গী',
                    'proof_subtitle' => 'Natural choices. Honest goodness.',
                    'note_top_title' => 'Pure & Natural',
                    'note_top_subtitle' => 'প্রকৃতির কাছাকাছি',
                    'note_bottom_title' => 'ভালোবাসা দিয়ে বাছাই',
                    'note_bottom_subtitle' => 'Everyday pantry essentials',
                ],
            ],
            [
                'type' => 'apon_benefits',
                'name' => 'আস্থার বার',
                'options' => [
                    'items' => [
                        ['symbol' => '♧', 'title' => 'প্রাকৃতিক পছন্দ', 'subtitle' => 'Nature-inspired essentials'],
                        ['symbol' => '♡', 'title' => 'যত্নে বাছাই করা', 'subtitle' => 'Quality comes first'],
                        ['symbol' => '⌂', 'title' => 'ঘরে পৌঁছে দিন', 'subtitle' => 'Convenient home delivery'],
                        ['symbol' => '✓', 'title' => 'বিশ্বস্ত কেনাকাটা', 'subtitle' => 'A simple, secure checkout'],
                    ],
                ],
            ],
            [
                'type' => 'apon_promos',
                'name' => 'প্রচারণার কার্ড',
                'options' => [
                    'kicker' => 'THE EVERYDAY PANTRY',
                    'title_start' => 'আপনার পছন্দের',
                    'title_highlight' => 'খাঁটি পণ্য',
                    'description' => 'রান্নাঘরের পরিচিত উপকরণ, যত্নে বেছে নিন এক জায়গা থেকেই।',
                    'link_text' => 'সব পণ্য দেখুন',
                    'link_url' => 'search',
                    'items' => [
                        [
                            'image' => self::IMAGES.'gawa-ghee-promo.jpg',
                            'image_alt' => 'খাঁটি গাওয়া ঘি',
                            'kicker' => 'RICH & TRADITIONAL',
                            'title' => 'খাঁটি গাওয়া ঘি',
                            'link_text' => 'পণ্য দেখুন',
                            'url' => 'search?query=ghee',
                        ],
                        [
                            'image' => self::IMAGES.'turmeric-promo.jpg',
                            'image_alt' => 'হলুদ গুঁড়া',
                            'kicker' => 'GOLDEN GOODNESS',
                            'title' => 'হলুদ গুঁড়া',
                            'link_text' => 'পণ্য দেখুন',
                            'url' => 'search?query=turmeric',
                        ],
                        [
                            'image' => self::IMAGES.'chilli-powder-promo.jpg',
                            'image_alt' => 'মরিচ গুঁড়া',
                            'kicker' => 'PURE SPICE, FULL FLAVOUR',
                            'title' => 'মরিচ গুঁড়া',
                            'link_text' => 'পণ্য দেখুন',
                            'url' => 'search?query=chili%20powder',
                        ],
                        [
                            'image' => self::IMAGES.'mustard-oil-promo.jpg',
                            'image_alt' => 'ঘানি ভাঙা সরিষার তেল',
                            'kicker' => 'FROM SEED TO BOTTLE',
                            'title' => 'সরিষার তেল',
                            'link_text' => 'পণ্য দেখুন',
                            'url' => 'search?query=mustard%20oil',
                        ],
                    ],
                ],
            ],
            [
                'type' => 'apon_categories',
                'name' => 'ক্যাটাগরি ক্যারোসেল',
                'options' => [
                    'kicker' => 'SHOP BY CATEGORY',
                    'title_start' => 'ক্যাটাগরি অনুযায়ী',
                    'title_highlight' => 'কেনাকাটা',
                    'link_text' => 'সব ক্যাটাগরি',
                    'link_url' => 'search',
                    'filters' => [],
                ],
            ],
            [
                'type' => 'apon_products',
                'name' => 'পণ্যের ক্যারোসেল',
                'options' => [
                    'kicker' => 'HANDPICKED FOR YOUR HOME',
                    'title_start' => 'আজকের পছন্দ,',
                    'title_highlight' => 'আপনার রান্নাঘরের জন্য',
                    'description' => 'আপনার স্টোরের ক্যাটালগ থেকে নির্বাচিত পণ্য এখানে দেখানো হবে।',
                    'link_text' => 'সব পণ্য',
                    'link_url' => 'search',
                    'filters' => [],
                ],
            ],
            [
                'type' => 'apon_story',
                'name' => 'আমাদের গল্প',
                'options' => [
                    'image' => self::IMAGES.'mustard-oil-promo.jpg',
                    'image_alt' => 'AponBasket pure mustard oil',
                    'stamp_first' => 'প্রকৃতির',
                    'stamp_second' => 'বিশুদ্ধ ছোঁয়া',
                    'stamp_caption' => 'APONBASKET',
                    'kicker' => 'A LITTLE CLOSER TO NATURE',
                    'title_start' => 'খাবারের প্রতিটি বাছাইয়ে থাকুক',
                    'title_highlight' => 'আস্থার ছোঁয়া',
                    'description' => 'AponBasket-এ আমরা চাই, আপনার প্রতিদিনের রান্নাঘরের প্রয়োজনীয় পণ্য খুঁজে পাওয়া হোক সহজ। ঘি, সরিষার তেল ও মসলার মতো পরিচিত উপকরণ—পরিষ্কার তথ্য ও সহজ কেনাকাটার অভিজ্ঞতাসহ এক জায়গায়।',
                    'points' => [
                        ['text' => 'পণ্যের স্পষ্ট বিবরণ'],
                        ['text' => 'সহজ অর্ডার প্রক্রিয়া'],
                        ['text' => 'পরিবারের কথা ভেবে বাছাই'],
                    ],
                    'link_text' => 'কেনাকাটা শুরু করুন',
                    'link_url' => 'search',
                ],
            ],
            [
                'type' => 'apon_cta',
                'name' => 'নিচের ব্যানার',
                'options' => [
                    'kicker' => 'WELCOME TO APONBASKET',
                    'title_start' => 'আপনার রান্নাঘরের ভালো পছন্দগুলো',
                    'title_highlight' => 'শুরু হোক এখানেই।',
                    'description' => 'প্রয়োজনীয় পণ্য খুঁজুন, আপনার পছন্দের জিনিস কার্টে যোগ করুন, অর্ডার সম্পন্ন করুন সহজেই।',
                    'link_text' => 'এখনই পণ্য দেখুন',
                    'link_url' => 'search',
                ],
            ],
            [
                'type' => 'apon_services',
                'name' => 'সব পৃষ্ঠার সেবা বার',
                'options' => [
                    'items' => [
                        ['symbol' => '♧', 'title' => 'প্রকৃতির কাছাকাছি পণ্য', 'subtitle' => 'Natural pantry essentials, all in one place'],
                        ['symbol' => '☷', 'title' => 'স্পষ্ট পণ্যের বিবরণ', 'subtitle' => 'Choose with the product details in view'],
                        ['symbol' => '⌂', 'title' => 'সহজ অনলাইন অর্ডার', 'subtitle' => 'Browse, add to cart and order from home'],
                    ],
                ],
            ],
        ];
    }
}
