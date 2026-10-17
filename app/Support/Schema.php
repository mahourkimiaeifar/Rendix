<?php

namespace App\Support;

final class Schema
{
    public static function organization(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => url('/#organization'),
            'name' => 'رندیکس',
            'alternateName' => 'Rendix',
            'url' => url('/'),
            'logo' => asset('/images/favicon.png'),
            'description' => 'آژانس دیجیتال مارکتینگ، طراحی وبسایت اختصاصی و سئو با تمرکز بر رشد پایدار کسب‌وکارها.',
            'areaServed' => [
                ['@type' => 'City', 'name' => 'نجف آباد'],
                ['@type' => 'State', 'name' => 'اصفهان'],
                ['@type' => 'Country', 'name' => 'Iran'],
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'email' => 'info@rendix.ir',
                'contactType' => 'customer service',
                'availableLanguage' => ['Persian'],
            ],
        ];
    }

    public static function localBusiness(): array
    {
        return [
            '@type' => 'ProfessionalService',
            '@id' => url('/#localbusiness'),
            'name' => 'رندیکس',
            'image' => asset('/images/og-image.jpg'),
            'url' => url('/'),
            'email' => 'info@rendix.ir',
            'priceRange' => '$$',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'نجف آباد',
                'addressRegion' => 'اصفهان',
                'addressCountry' => 'IR',
            ],
            'areaServed' => 'نجف آباد',
            'makesOffer' => [
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'دیجیتال مارکتینگ']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'طراحی وبسایت اختصاصی']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'سئو']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'مدیریت شبکه‌های اجتماعی']],
                ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'فروشگاه اینترنتی']],
            ],
        ];
    }

    public static function faq(array $items): array
    {
        return [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn($item) => [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['a'],
                ],
            ], $items),
        ];
    }

    public static function home(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::localBusiness(),
                [
                    '@type' => 'WebSite',
                    '@id' => url('/#website'),
                    'url' => url('/'),
                    'name' => 'رندیکس',
                    'inLanguage' => 'fa',
                    'publisher' => ['@id' => url('/#organization')],
                ],
            ],
        ];
    }

    public static function about(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                [
                    '@type' => 'AboutPage',
                    'url' => url('/about'),
                    'name' => 'درباره رندیکس',
                    'inLanguage' => 'fa',
                ],
            ],
        ];
    }

    public static function contact(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::organization(),
                self::localBusiness(),
                [
                    '@type' => 'ContactPage',
                    'url' => url('/contact'),
                    'name' => 'تماس با رندیکس',
                    'inLanguage' => 'fa',
                ],
            ],
        ];
    }
}