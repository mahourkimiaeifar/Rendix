<?php

use App\Models\Post;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = trim($slug);
    }

    public function post(): ?Post
    {
        try {
            return Post::query()->active()->where('slug', $this->slug)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function relatedPosts(?Post $post)
    {
        try {
            return Post::query()
                ->active()
                ->where('id', '!=', $post->id)
                ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
                ->ordered()
                ->limit(3)
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
};

?>

@php
    $post = $this->post();

    if (! $post) {
        abort(404);
    }

    $related = $this->relatedPosts($post);
    $siteName = setting('site.name', 'رندیکس');

    $image = null;
    if (method_exists($post, 'coverUrl')) $image = $post->coverUrl();
    elseif (method_exists($post, 'imageUrl')) $image = $post->imageUrl();
    elseif (! empty($post->cover_image)) $image = asset('storage/' . $post->cover_image);
    elseif (! empty($post->image_path)) $image = asset('storage/' . $post->image_path);

    $catName = null;
    if (isset($post->category) && is_object($post->category)) $catName = $post->category->name ?? null;
    elseif (! empty($post->category_name)) $catName = $post->category_name;
    elseif (! empty($post->category) && is_string($post->category)) $catName = $post->category;

    $tags = method_exists($post, 'tagList') ? $post->tagList() : (property_exists($post, 'tags') && $post->tags ? $post->tags->pluck('name')->all() : []);

    $words = str_word_count(strip_tags((string) ($post->content ?? '')));
    $reading = max(1, (int) ceil($words / 200));

    $pageTitle = $post->meta_title ?: ($post->title . ' | ' . $siteName);
    $pageDescription = $post->meta_description ?: ($post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags((string) ($post->content ?? '')), 160));
@endphp

@section('rendix.title', $pageTitle)
@section('rendix.description', $pageDescription)
@section('rendix.canonical', route('blog.show', $post->slug))
@section('rendix.image', $image ?: setting_url('brand.og_image', setting_url('brand.favicon', asset('images/favicon.png?v=6'))))
@section('rendix.type', 'article')

@push('rendix.head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $pageDescription,
            'image' => $image,
            'datePublished' => ($post->published_at ?? $post->created_at)?->toIso8601String(),
            'author' => ['@type' => 'Organization', 'name' => $siteName],
            'publisher' => ['@type' => 'Organization', 'name' => $siteName],
            'mainEntityOfPage' => route('blog.show', $post->slug),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

<div>
    <section class="rx-section tight" style="padding-top: 130px;">
        <div class="rx-container" style="max-width: 980px;">
            <nav class="rx-breadcrumb-public" aria-label="مسیر صفحه">
                <a href="{{ route('home') }}" wire:navigate>خانه</a>
                <i class="fa-solid fa-chevron-left"></i>
                <a href="{{ route('blog') }}" wire:navigate>بلاگ</a>
                <i class="fa-solid fa-chevron-left"></i>
                <strong>{{ \Illuminate\Support\Str::limit($post->title, 40) }}</strong>
            </nav>

            @if ($catName)
                <span class="rx-post-cat large fade-up">{{ $catName }}</span>
            @endif

            <h1 class="fade-up delay-1" style="margin:10px 0 0;font-size:clamp(28px,4.5vw,46px);font-weight:950;line-height:1.3;">
                {{ $post->title }}
            </h1>

            <div class="rx-post-meta fade-up delay-2" style="margin-top:16px;">
                <span><i class="fa-regular fa-calendar"></i> {{ \App\Support\Jalali::date($post->published_at ?? $post->created_at) }}</span>
                <span><i class="fa-regular fa-clock"></i> {{ \App\Support\Jalali::toPersianDigits((string) $reading) }} دقیقه مطالعه</span>
                @if ($post->views ?? null)
                    <span><i class="fa-regular fa-eye"></i> {{ \App\Support\Jalali::toPersianDigits(number_format($post->views)) }} بازدید</span>
                @endif
            </div>

            @if ($image)
                <div class="rx-project-hero rv" style="margin-top:26px;">
                    <img src="{{ $image }}" alt="{{ $post->title }}">
                </div>
            @endif

            <article class="rx-post-content rv">
                {!! $post->content !!}
            </article>

            @if (! empty($tags))
                <div class="rx-tech-tags rv" style="margin-top:22px;">
                    @foreach ($tags as $tag)
                        <span>#{{ $tag }}</span>
                    @endforeach
                </div>
            @endif

            <div class="rx-post-share rv">
                <span>اشتراک‌گذاری:</span>

                @php $shareUrl = urlencode(route('blog.show', $post->slug)); $shareText = urlencode($post->title); @endphp

                <a href="https://t.me/share/url?url={{ $shareUrl }}&text={{ $shareText }}" target="_blank" rel="noopener noreferrer" class="rx-social-link" title="تلگرام">
                    <i class="fa-brands fa-telegram"></i>
                </a>
                <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="rx-social-link" title="واتساپ">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer" class="rx-social-link" title="لینکدین">
                    <i class="fa-brands fa-linkedin-in"></i>
                </a>
                <button type="button" class="rx-social-link" title="کپی لینک"
                    onclick="navigator.clipboard.writeText('{{ route('blog.show', $post->slug) }}').then(function(){ window.rxToast && rxToast({ type:'success', message:'لینک مقاله کپی شد.' }); })">
                    <i class="fa-solid fa-link"></i>
                </button>
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="rx-section tight">
            <div class="rx-container">
                <div class="rx-section-head">
                    <h2>مقالات مرتبط</h2>
                    <p>ادامه‌ی یادگیری از همین جنس مقالات.</p>
                </div>

                <div class="rx-blog-grid">
                    @foreach ($related as $rel)
                        @php
                            $relImage = null;
                            if (method_exists($rel, 'coverUrl')) $relImage = $rel->coverUrl();
                            elseif (! empty($rel->cover_image)) $relImage = asset('storage/' . $rel->cover_image);
                        @endphp

                        <article class="rx-post-card rv">
                            <a href="{{ route('blog.show', $rel->slug) }}" class="rx-post-media" wire:navigate>
                                @if ($relImage)
                                    <img src="{{ $relImage }}" alt="{{ $rel->title }}" loading="lazy">
                                @else
                                    <div class="rx-project-placeholder"><i class="fa-solid fa-newspaper"></i></div>
                                @endif
                            </a>

                            <div class="rx-post-body">
                                <h2 style="font-size:17px;">
                                    <a href="{{ route('blog.show', $rel->slug) }}" wire:navigate>{{ $rel->title }}</a>
                                </h2>
                                <p>{{ \Illuminate\Support\Str::limit($rel->excerpt ?: strip_tags((string) ($rel->content ?? '')), 90) }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-site.cta-band
        title="این مقاله را روی کسب‌وکارت اجرا کن"
        text="خواندن فقط نصف راه است؛ نیمه‌ی دوم را با هم می‌رویم."
    />
</div>