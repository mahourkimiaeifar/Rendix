<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            [
                'name' => 'Rendix Admin Dashboard Pro',
                'slug' => 'rendix-admin-dashboard-pro',
                'description' => 'پنل ادمین کامل با React 19، Tailwind و کامپوننت‌های آماده؛ شامل ۱۲۰+ کامپوننت، داشبورد چندصفحه‌ای، چارت‌های تعاملی، تقویم، سیستم احراز هویت و مدیریت کاربران.',
                'features' => [
                    '۱۲۰+ کامپوننت React آماده',
                    'سیستم احراز هویت کامل (Login / Register / Forgot Password)',
                    'داشبورد با ۸ نوع چارت تعاملی',
                    'پشتیبانی کامل از RTL و فارسی',
                    'Dark/Light mode با ذخیره در localStorage',
                    'Responsive از موبایل تا دسکتاپ',
                ],
                'price' => 2500000,
                'is_fullstack' => true,
                'has_support' => true,
                'has_trial' => true,
                'has_guarantee' => true,
                'languages' => ['React', 'TypeScript', 'Tailwind', 'Laravel'],
            ],
            [
                'name' => 'E-Commerce Next.js Template',
                'slug' => 'ecommerce-nextjs-template',
                'description' => 'قالب فروشگاهی مدرن با Next.js 14 App Router، سبد خرید، پرداخت آنلاین و پنل ادمین. طراحی مینیمال و سرعت فوق‌العاده.',
                'features' => [
                    'Next.js 14 با App Router',
                    'Server-Side Rendering برای SEO',
                    'سبد خرید و پرداخت آنلاین',
                    'جستجوی پیشرفته محصولات',
                    'پنل ادمین برای مدیریت محصولات',
                ],
                'price' => 1800000,
                'is_fullstack' => true,
                'has_support' => true,
                'has_trial' => false,
                'has_guarantee' => true,
                'languages' => ['Next.js', 'TypeScript', 'Tailwind', 'Node.js'],
            ],
            [
                'name' => 'Landing Page Collection',
                'slug' => 'landing-page-collection',
                'description' => 'مجموعه ۸ لندینگ پیج حرفه‌ای با Tailwind CSS برای انواع کسب‌وکارها (SaaS، آژانس، اپلیکیشن، فروشگاه). هر لندینگ با انیمیشن‌های نرم و آماده استفاده.',
                'features' => [
                    '۸ لندینگ پیج مستقل',
                    'Tailwind CSS بدون وابستگی اضافی',
                    'انیمیشن‌های AOS و GSAP',
                    'فرم تماس با اعتبارسنجی',
                    'SEO بهینه‌شده',
                    'فونت Vazirmatn داخلی',
                ],
                'price' => 950000,
                'is_fullstack' => false,
                'has_support' => true,
                'has_trial' => true,
                'has_guarantee' => false,
                'languages' => ['HTML', 'CSS', 'Tailwind', 'JavaScript'],
            ],
            [
                'name' => 'Vue Dashboard Starter',
                'slug' => 'vue-dashboard-starter',
                'description' => 'استارتر کیت Vue 3 + Pinia برای ساخت سریع داشبورد. شامل سایدبار، جدول داده، چارت، مدال، توست و بیش از ۴۰ کامپوننت.',
                'features' => [
                    'Vue 3 Composition API',
                    'Pinia برای مدیریت state',
                    'Vue Router با lazy loading',
                    '۴۰+ کامپوننت آماده',
                    'داشبورد نمونه کامل',
                ],
                'price' => 1200000,
                'is_fullstack' => false,
                'has_support' => true,
                'has_trial' => true,
                'has_guarantee' => true,
                'languages' => ['Vue', 'TypeScript', 'Tailwind'],
            ],
            [
                'name' => 'Django Blog Full-Stack',
                'slug' => 'django-blog-fullstack',
                'description' => 'وبلاگ کامل با Django 5، React front-end و PostgreSQL. شامل سیستم نظردهی، تگ، دسته‌بندی، جستجو و پنل ادمین Django.',
                'features' => [
                    'Django 5 با DRF',
                    'React 19 front-end',
                    'PostgreSQL + Redis cache',
                    'SEO کامل با sitemap',
                    'پنل ادمین Django',
                    'سیستم کامنت با احراز هویت',
                ],
                'price' => 2200000,
                'is_fullstack' => true,
                'has_support' => true,
                'has_trial' => false,
                'has_guarantee' => true,
                'languages' => ['Django', 'Python', 'React', 'PostgreSQL'],
            ],
            [
                'name' => 'Svelte Portfolio Kit',
                'slug' => 'svelte-portfolio-kit',
                'description' => 'قالب نمونه‌کار Svelte برای برنامه‌نویسان و طراحان. سرعت فوق‌العاده، انیمیشن نرم و طراحی خاص.',
                'features' => [
                    'Svelte 5 بدون virtual DOM',
                    'حجم باندل کمتر از ۵۰ کیلوبایت',
                    'انیمیشن‌های نرم',
                    'چندین تم رنگی',
                    'RTL آماده',
                ],
                'price' => 680000,
                'is_fullstack' => false,
                'has_support' => false,
                'has_trial' => true,
                'has_guarantee' => false,
                'languages' => ['Svelte', 'JavaScript', 'CSS'],
            ],
        ];

        foreach ($samples as $sample) {
            $languages = $sample['languages'];
            unset($sample['languages']);

            $template = Template::firstOrCreate(
                ['slug' => $sample['slug']],
                $sample
            );

            $template->languages()->delete();
            foreach ($languages as $lang) {
                $template->languages()->create([
                    'language' => $lang,
                    'color' => $this->getLanguageColor($lang),
                ]);
            }
        }
    }

    protected function getLanguageColor(string $language): string
    {
        $colors = [
            'React' => 'rgba(97,218,251,.18)', 'Vue' => 'rgba(65,184,131,.18)',
            'Angular' => 'rgba(221,0,49,.18)', 'Laravel' => 'rgba(255,45,32,.18)',
            'PHP' => 'rgba(119,123,180,.18)', 'JavaScript' => 'rgba(247,223,30,.18)',
            'TypeScript' => 'rgba(49,120,198,.18)', 'HTML' => 'rgba(227,79,38,.18)',
            'CSS' => 'rgba(86,61,124,.18)', 'Tailwind' => 'rgba(56,189,248,.18)',
            'Bootstrap' => 'rgba(121,82,179,.18)', 'Python' => 'rgba(53,114,165,.18)',
            'Node.js' => 'rgba(104,159,56,.18)', 'Next.js' => 'rgba(255,255,255,.18)',
            'Nuxt' => 'rgba(0,220,130,.18)', 'Svelte' => 'rgba(255,62,0,.18)',
            'Django' => 'rgba(9,76,56,.18)', 'PostgreSQL' => 'rgba(51,103,146,.18)',
        ];
        return $colors[$language] ?? 'rgba(69,200,255,.15)';
    }
}