<?php

namespace App\Support;

use Illuminate\Support\Facades\URL;

final class Schema
{
    private static function url(string $path = '/'): string
    {
        return URL::to($path);
    }

    private static function areaServed(): array
    {
        return [
            ['@type' => 'City', 'name' => 'نجف آباد'],
            ['@type' => 'AdministrativeArea', 'name' => 'اصفهان'],
            ['@type' => 'Country', 'name' => 'Iran'],
        ];
    }

    private static function orgNode(): array
    {
        return [
            '@type' => 'Organization',
            '@id' => self::url('/#organization'),
            'name' => 'رندیکس',
            'alternateName' => 'Rendix',
            'url' => self::url('/'),
            'logo' => self::url('/images/logo.png'),
            'image' => self::url('/images/og-image.jpg'),
            'description' => 'رندیکس یک استودیوی فنی-خلاق برای طراحی وبسایت اختصاصی، سئو، دیجیتال مارکتینگ، شبکه‌های اجتماعی و فروشگاه اینترنتی است.',
            'slogan' => 'ما فقط وبسایت نمی‌سازیم؛ سیستم رشد دیجیتال می‌سازیم.',
            'foundingDate' => '2024',
            'areaServed' => self::areaServed(),
            'knowsAbout' => [
                'دیجیتال مارکتینگ',
                'طراحی وبسایت',
                'سئو',
                'تبلیغات آنلاین',
                'مدیریت شبکه‌های اجتماعی',
                'فروشگاه اینترنتی',
                'UI/UX',
                'Conversion Rate Optimization',
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '+989000000000',
                'email' => 'hello@rendix.ir',
                'contactType' => 'customer service',
                'availableLanguage' => ['Persian', 'English'],
                'areaServed' => 'IR',
            ],
            'sameAs' => [
                'https://t.me/RendixDMServices',
            ],
        ];
    }

    private static function localNode(): array
    {
        return [
            '@type' => 'ProfessionalService',
            '@id' => self::url('/#localbusiness'),
            'name' => 'رندیکس | دیجیتال مارکتینگ، طراحی وبسایت و سئو',
            'image' => self::url('/images/og-image.jpg'),
            'url' => self::url('/'),
            'telephone' => '+989000000000',
            'email' => 'hello@rendix.ir',
            'priceRange' => '$$',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'نجف‌آباد، خیابان نمونه، پلاک نمونه',
                'addressLocality' => 'نجف آباد',
                'addressRegion' => 'اصفهان',
                'postalCode' => '8518888888',
                'addressCountry' => 'IR',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 32.6333,
                'longitude' => 51.3667,
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday'],
                    'opens' => '09:00',
                    'closes' => '18:00',
                ],
            ],
            'areaServed' => self::areaServed(),
            'serviceType' => [
                'طراحی وبسایت',
                'سئو',
                'دیجیتال مارکتینگ',
                'شبکه‌های اجتماعی',
                'فروشگاه اینترنتی',
                'پشتیبانی سایت',
            ],
            'provider' => [
                '@id' => self::url('/#organization'),
            ],
        ];
    }

    private static function websiteNode(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => self::url('/#website'),
            'url' => self::url('/'),
            'name' => 'رندیکس',
            'description' => 'دروازه‌ی ورود به دنیای دیجیتال؛ طراحی وبسایت، سئو و دیجیتال مارکتینگ برای کسب‌وکارهای نجف آباد، اصفهان و سراسر ایران.',
            'inLanguage' => 'fa-IR',
            'publisher' => [
                '@id' => self::url('/#organization'),
            ],
        ];
    }

    private static function faqItems(): array
    {
        return [
            [
                'name' => 'دیجیتال مارکتینگ در نجف آباد چطور به فروش کمک می‌کند؟',
                'text' => 'با ترکیب سئو محلی، تبلیغات هدفمند، محتوای درست و وبسایت سریع، مشتریانی که در نجف آباد و اطراف آن جست‌وجو می‌کنند راحت‌تر شما را پیدا می‌کنند و به سرنخ فروش تبدیل می‌شوند.',
            ],
            [
                'name' => 'طراحی وبسایت اختصاصی چه مزیتی نسبت به قالب آماده دارد؟',
                'text' => 'وبسایت اختصاصی سریع‌تر لود می‌شود، برای سئو تمیزتر است، امن‌تر است و دقیقاً حول هدف تجاری شما طراحی می‌شود؛ نه محدود به ساختار قالب‌های آماده.',
            ],
            [
                'name' => 'سئو در نجف آباد چقدر زمان می‌برد؟',
                'text' => 'سئو فرایند زمان‌بر است؛ معمولاً اولین نتیجه‌های قابل توجه بین ۳ تا ۶ ماه دیده می‌شود. اما زیرساخت فنی، سرعت و محتوا از همان ابتدا روی دیده‌شدن اثر می‌گذارد.',
            ],
            [
                'name' => 'آیا مدیریت شبکه‌های اجتماعی و تبلیغات آنلاین هم انجام می‌دهید؟',
                'text' => 'بله. استراتژی محتوا، تقویم انتشار، کمپین‌های تبلیغاتی و گزارش‌دهی عملکرد بخشی از خدمات دیجیتال مارکتینگ رندیکس است.',
            ],
            [
                'name' => 'پشتیبانی بعد از راه‌اندازی سایت شامل چه مواردی است؟',
                'text' => 'مانیتورینگ، به‌روزرسانی، رفع خطا، بهینه‌سازی سرعت، راهنمایی محتوایی و بررسی دوره‌ای شاخص‌های سئو و تبدیل از جمله خدمات پشتیبانی رندیکس است.',
            ],
        ];
    }

    private static function faqNode(): array
    {
        return [
            '@type' => 'FAQPage',
            '@id' => self::url('/#faq'),
            'mainEntity' => array_map(fn($item) => [
                '@type' => 'Question',
                'name' => $item['name'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $item['text'],
                ],
            ], self::faqItems()),
        ];
    }

    private static function serviceNode(
        string $name,
        string $description,
        string $path,
        array $types = []
    ): array {
        return [
            '@type' => 'Service',
            '@id' => self::url($path . '#service'),
            'name' => $name,
            'description' => $description,
            'url' => self::url($path),
            'serviceType' => $types ?: [$name],
            'provider' => [
                '@id' => self::url('/#organization'),
            ],
            'areaServed' => self::areaServed(),
        ];
    }

    private static function aboutNode(): array
    {
        return [
            '@type' => 'AboutPage',
            '@id' => self::url('/about#aboutpage'),
            'url' => self::url('/about'),
            'name' => 'درباره رندیکس',
            'description' => 'داستان، ارزش‌ها، ماموریت و توانمندی‌های رندیکس؛ استودیوی فنی-خلاق با تمرکز اولیه بر نجف آباد و خدمات سراسری.',
            'inLanguage' => 'fa-IR',
            'mainEntity' => [
                '@id' => self::url('/#organization'),
            ],
        ];
    }

    private static function contactNode(): array
    {
        return [
            '@type' => 'ContactPage',
            '@id' => self::url('/contact#contactpage'),
            'url' => self::url('/contact'),
            'name' => 'تماس با رندیکس',
            'description' => 'راه‌های ارتباطی با رندیکس برای مشاوره رایگان، شروع پروژه، طراحی وبسایت، سئو و دیجیتال مارکتینگ.',
            'inLanguage' => 'fa-IR',
            'mainEntity' => [
                '@id' => self::url('/#localbusiness'),
            ],
        ];
    }

    public static function home(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::orgNode(),
                self::localNode(),
                self::websiteNode(),
                self::faqNode(),
                self::serviceNode(
                    'دیجیتال مارکتینگ در نجف آباد',
                    'استراتژی رشد آنلاین، تبلیغات هدفمند، تولید محتوا و بهینه‌سازی قیف فروش برای کسب‌وکارهای نجف آباد و سراسر ایران.',
                    '/#digital-marketing',
                    ['DigitalMarketing', 'دیجیتال مارکتینگ']
                ),
                self::serviceNode(
                    'طراحی وبسایت اختصاصی در نجف آباد',
                    'طراحی و توسعه وبسایت سریع، امن، سئوپدیده و متناسب با هویت برند و هدف تجاری.',
                    '/#web-design',
                    ['WebDesign', 'طراحی وبسایت']
                ),
                self::serviceNode(
                    'سئو و بازاریابی محتوایی در نجف آباد',
                    'سئوی تکنیکال، محتوایی و محلی برای افزایش دیده‌شدن در گوگل و جذب مشتری هدفمند.',
                    '/#seo',
                    ['SEO', 'سئو']
                ),
                self::serviceNode(
                    'مدیریت شبکه‌های اجتماعی و تبلیغات آنلاین',
                    'برنامه‌ریزی، تولید محتوا، کمپین‌های تبلیغاتی و گزارش‌دهی عملکرد در پلتفرم‌های دیجیتال.',
                    '/#social-media',
                    ['SocialMediaMarketing', 'PaidAdvertising']
                ),
                self::serviceNode(
                    'فروشگاه اینترنتی و بهینه‌سازی نرخ تبدیل',
                    'ساخت فروشگاه آنلاین با تجربه خرید روان، پرداخت امن و بهینه‌سازی مسیر تبدیل.',
                    '/#ecommerce',
                    ['Ecommerce', 'ConversionRateOptimization']
                ),
                self::serviceNode(
                    'پشتیبانی سایت و بهینه‌سازی مستمر',
                    'مانیتورینگ، امنیت، به‌روزرسانی، رفع خطا و بهبود مداوم سرعت و عملکرد وبسایت.',
                    '/#support',
                    ['WebsiteSupport', 'Maintenance']
                ),
            ],
        ];
    }

    public static function about(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::orgNode(),
                self::localNode(),
                self::aboutNode(),
            ],
        ];
    }

    public static function contact(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                self::orgNode(),
                self::localNode(),
                self::contactNode(),
            ],
        ];
    }

    public static function faqForBlade(): array
    {
        return self::faqItems();
    }
}