<?php

use App\Models\Project;
use App\Models\Service;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function service(): ?Service
    {
        try {
            return Service::query()
                ->active()
                ->where('slug', $this->slug)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function relatedProjects(?Service $service)
    {
        try {
            $query = Project::query()->active();

            $categoryExists = $service && array_key_exists($service->slug, Project::categoryOptions());

            if ($categoryExists) {
                $query->where('category', $service->slug);
            } else {
                $query->where('is_featured', true);
            }

            $projects = $query
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            if ($projects->isEmpty() && $categoryExists) {
                $projects = Project::query()
                    ->active()
                    ->where('is_featured', true)
                    ->orderBy('sort_order')
                    ->orderByDesc('id')
                    ->limit(3)
                    ->get();
            }

            return $projects;
        } catch (\Throwable $e) {
            return collect();
        }
    }
};

?>

@php
    $service = $this->service();

    if (! $service) {
        abort(404);
    }

    $projects = $this->relatedProjects($service);

    $siteName = setting('site.name', 'رندیکس');
    $telegram = setting('social.telegram', 'https://t.me/RendixDMServices');
    $contactPhone = setting('contact.phone');
    $contactPhoneTel = preg_replace('/\D/', '', (string) $contactPhone);
    $contactEmail = setting('contact.email');

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service->title,
        'description' => $service->short_description ?: $service->title,
        'provider' => [
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => url('/'),
        ],
        'url' => route('services.show', $service->slug),
    ];
@endphp

<div>
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-7xl mx-auto">

            <a href="{{ route('services') }}" class="inline-flex items-center gap-2 text-sm font-bold text-snow/55 hover:text-sky transition-colors mb-8">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به همه خدمات
            </a>

            <div class="grid lg:grid-cols-5 gap-10">
                <div class="lg:col-span-3 space-y-8">
                    <div class="space-y-6">
                        <div class="badge">
                            <span class="badge-dot"></span>
                            خدمت تخصصی {{ $siteName }}
                        </div>

                        <h1 class="text-[38px] md:text-5xl font-black leading-[1.25]">
                            {{ $service->title }}
                        </h1>

                        @if ($service->short_description)
                            <p class="text-lg text-snow/65 leading-9">
                                {{ $service->short_description }}
                            </p>
                        @endif
                    </div>

                    @if ($service->image_path)
                        <div class="rounded-3xl overflow-hidden border border-white/10 bg-white/5">
                            <img
                                src="{{ $service->imageUrl() }}"
                                alt="{{ $service->title }}"
                                class="w-full aspect-[16/9] object-cover"
                            >
                        </div>
                    @endif

                    @if ($service->description)
                        <div class="glass p-7 md:p-9 space-y-5">
                            <h2 class="text-2xl font-black">
                                درباره این خدمت
                            </h2>

                            <div class="text-sm md:text-base text-snow/70 leading-9 whitespace-pre-line">
                                {{ $service->description }}
                            </div>
                        </div>
                    @endif

                    @if ($projects->isNotEmpty())
                        <div class="space-y-6">
                            <div class="flex items-end justify-between gap-4">
                                <h2 class="text-2xl font-black">
                                    نمونه‌کارهای مرتبط
                                </h2>

                                <a href="{{ route('projects') }}" class="text-sm font-bold text-sky hover:text-sky/80 transition-colors">
                                    همه نمونه‌کارها
                                </a>
                            </div>

                            <div class="grid md:grid-cols-3 gap-5">
                                @foreach ($projects as $project)
                                    <article class="glass p-5 space-y-4 hover:border-sky/30 transition-all">
                                        <a href="{{ route('projects.show', $project->slug) }}" class="block rounded-2xl overflow-hidden border border-white/10 bg-white/5">
                                            @if ($project->image_path)
                                                <img
                                                    src="{{ $project->imageUrl() }}"
                                                    alt="{{ $project->title }}"
                                                    class="w-full h-36 object-cover"
                                                    loading="lazy"
                                                >
                                            @else
                                                <div class="w-full h-36 grid place-items-center text-sky/50 text-3xl">
                                                    <i class="fa-solid fa-briefcase"></i>
                                                </div>
                                            @endif
                                        </a>

                                        <div class="space-y-2">
                                            <h3 class="text-base font-black leading-7">
                                                <a href="{{ route('projects.show', $project->slug) }}" class="hover:text-sky transition-colors">
                                                    {{ $project->title }}
                                                </a>
                                            </h3>

                                            <p class="text-xs text-snow/55 leading-6">
                                                {{ \Illuminate\Support\Str::limit($project->short_description ?: $project->categoryLabel(), 90) }}
                                            </p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <x-site.faq location="services" />
                </div>

                <aside class="lg:col-span-2 space-y-6">
                    <div class="glass p-7 space-y-6 sticky top-28">
                        <h3 class="text-lg font-black">
                            درخواست این خدمت
                        </h3>

                        <p class="text-sm text-snow/60 leading-7">
                            اگر می‌خواهی {{ $service->title }} را برای کسب‌وکارت فعال کنی، همین حالا پیام بده. ما ظرف ۲۴ ساعت پاسخ می‌دهیم.
                        </p>

                        @if ($service->metric_value || $service->metric_label)
                            <div class="rounded-xl bg-white/5 border border-white/10 px-4 py-3 flex items-center justify-between gap-3">
                                <strong class="text-sky text-lg">
                                    {{ $service->metric_value }}
                                </strong>

                                <span class="text-xs text-snow/50 font-bold">
                                    {{ $service->metric_label }}
                                </span>
                            </div>
                        @endif

                        <a href="{{ route('contact', ['service' => $service->slug]) }}" class="btn btn-primary w-full">
                            <i class="fa-solid fa-paper-plane"></i>
                            ثبت درخواست
                        </a>

                        <div class="space-y-4 pt-2">
                            @if ($contactPhone)
                                <a href="tel:{{ $contactPhoneTel }}" class="flex items-center gap-4 group">
                                    <div class="icon-tile !w-12 !h-12 !text-lg">
                                        <i class="fa-solid fa-phone"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-snow/40 mb-0.5">تلفن</div>
                                        <div class="text-sm font-bold group-hover:text-sky transition-colors">
                                            {{ $contactPhone }}
                                        </div>
                                    </div>
                                </a>
                            @endif

                            @if ($contactEmail)
                                <a href="mailto:{{ $contactEmail }}" class="flex items-center gap-4 group">
                                    <div class="icon-tile !w-12 !h-12 !text-lg">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-snow/40 mb-0.5">ایمیل</div>
                                        <div class="text-sm font-bold group-hover:text-sky transition-colors" dir="ltr">
                                            {{ $contactEmail }}
                                        </div>
                                    </div>
                                </a>
                            @endif

                            @if ($telegram)
                                <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 group">
                                    <div class="icon-tile icon-tile-violet !w-12 !h-12 !text-lg">
                                        <i class="fa-brands fa-telegram"></i>
                                    </div>
                                    <div>
                                        <div class="text-xs text-snow/40 mb-0.5">تلگرام</div>
                                        <div class="text-sm font-bold group-hover:text-sky transition-colors">
                                            پیام مستقیم
                                        </div>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>

                    <x-site.testimonials :limit="2" title="نظر مشتریان" subtitle="بخشی از بازخوردهای همکاری‌های اخیر." />
                </aside>
            </div>
        </div>
    </section>
</div>