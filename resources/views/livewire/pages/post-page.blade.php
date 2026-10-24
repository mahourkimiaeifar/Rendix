<?php

use App\Models\Post;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = trim($slug);

        $post = $this->post();

        if ($post) {
            try {
                $post->increment('views');
            } catch (\Throwable $e) {
                //
            }
        }
    }

    public function post(): ?Post
    {
        try {
            return Post::query()
                ->published()
                ->with(['category', 'author', 'tags'])
                ->where('slug', $this->slug)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function relatedPosts(?Post $post)
    {
        if (! $post) {
            return collect();
        }

        return $post->relatedPosts(3);
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
    $telegram = setting('social.telegram', 'https://t.me/RendixDMServices');

    $shareUrl = route('blog.show', $post->slug);
    $shareText = $post->title . ' | ' . $siteName;

    $content = function_exists('render_rich_content')
        ? render_rich_content((string) $post->content)
        : nl2br(e($post->content));
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-5xl mx-auto">

            <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm font-bold text-snow/55 hover:text-sky transition-colors mb-8">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به بلاگ
            </a>

            <article class="glass p-7 md:p-10 space-y-8">
                <header class="space-y-5">
                    @if ($post->category)
                        <a
                            href="{{ route('category', $post->category->slug) }}"
                            class="inline-flex text-xs font-black text-sky bg-sky/10 border border-sky/20 rounded-full px-4 py-2"
                        >
                            {{ $post->category->name }}
                        </a>
                    @endif

                    <h1 class="text-3xl md:text-5xl font-black leading-[1.25]">
                        {{ $post->title }}
                    </h1>

                    @if ($post->excerpt)
                        <p class="text-lg text-snow/65 leading-9">
                            {{ $post->excerpt }}
                        </p>
                    @endif

                    <div class="flex flex-wrap items-center gap-4 text-xs md:text-sm text-snow/45 border-t border-white/10 pt-5">
                        <span>
                            <i class="fa-solid fa-calendar-days ml-1"></i>
                            {{ $post->published_at ? Jalali::date($post->published_at) : '—' }}
                        </span>

                        <span>
                            <i class="fa-solid fa-stopwatch ml-1"></i>
                            {{ Jalali::toPersianDigits((string) $post->readingTime()) }} دقیقه مطالعه
                        </span>

                        <span>
                            <i class="fa-solid fa-eye ml-1"></i>
                            {{ Jalali::toPersianDigits(number_format($post->views)) }} بازدید
                        </span>

                        @if ($post->author)
                            <span>
                                <i class="fa-solid fa-user-pen ml-1"></i>
                                {{ $post->author->name }}
                            </span>
                        @endif
                    </div>
                </header>

                @if ($post->image_path)
                    <div class="rounded-3xl overflow-hidden border border-white/10 bg-white/5">
                        <img
                            src="{{ $post->imageUrl() }}"
                            alt="{{ $post->title }}"
                            class="w-full aspect-[16/9] object-cover"
                        >
                    </div>
                @endif

                <div class="rx-post-content">
                    {!! $content !!}
                </div>

                @if ($post->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-2 border-t border-white/10 pt-6">
                        @foreach ($post->tags as $tag)
                            <a
                                href="{{ route('tag', $tag->slug) }}"
                                class="text-xs font-bold text-snow/65 bg-white/5 border border-white/10 rounded-full px-3 py-2 hover:text-sky hover:border-sky/30 transition-all"
                            >
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="rounded-2xl bg-white/5 border border-white/10 p-6 space-y-4">
                    <h3 class="text-lg font-black">
                        این مقاله را به اشتراک بگذار
                    </h3>

                    <div class="flex flex-wrap gap-3">
                        <a
                            href="https://t.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareText) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-primary"
                        >
                            <i class="fa-brands fa-telegram"></i>
                            تلگرام
                        </a>

                        <a
                            href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareText) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-primary"
                        >
                            <i class="fa-brands fa-x-twitter"></i>
                            X
                        </a>

                        <button
                            type="button"
                            class="btn btn-primary"
                            onclick="navigator.clipboard.writeText('{{ $shareUrl }}').then(() => alert('لینک کپی شد.'))"
                        >
                            <i class="fa-solid fa-link"></i>
                            کپی لینک
                        </button>
                    </div>
                </div>

                <div class="rounded-2xl bg-gradient-to-l from-blue-600/15 to-cyan-400/10 border border-sky/20 p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-5">
                    <div class="space-y-2">
                        <h3 class="text-xl font-black">
                            پروژه بعدی‌ات را با {{ $siteName }} شروع کن
                        </h3>

                        <p class="text-sm text-snow/65 leading-7">
                            اگر این مقاله برایت مفید بود، تیم رندیکس آماده است وبسایت، سئو یا کمپین دیجیتال تو را حرفه‌ای بسازد.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('request-project') }}" class="btn btn-primary">
                            <i class="fa-solid fa-rocket"></i>
                            درخواست پروژه
                        </a>

                        @if ($telegram)
                            <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                                <i class="fa-brands fa-telegram"></i>
                                تلگرام
                            </a>
                        @endif
                    </div>
                </div>
            </article>

            @if ($related->isNotEmpty())
                <section class="mt-16 space-y-6">
                    <h2 class="text-2xl font-black">
                        مقالات مرتبط
                    </h2>

                    <div class="grid md:grid-cols-3 gap-5">
                        @foreach ($related as $rel)
                            <article class="glass p-5 space-y-4 hover:border-sky/30 transition-all">
                                <a href="{{ route('blog.show', $rel->slug) }}" class="block rounded-2xl overflow-hidden border border-white/10 bg-white/5">
                                    @if ($rel->image_path)
                                        <img
                                            src="{{ $rel->imageUrl() }}"
                                            alt="{{ $rel->title }}"
                                            class="w-full h-36 object-cover"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-36 grid place-items-center text-sky/45 text-3xl">
                                            <i class="fa-solid fa-article"></i>
                                        </div>
                                    @endif
                                </a>

                                <div class="space-y-2">
                                    <h3 class="text-base font-black leading-7">
                                        <a href="{{ route('blog.show', $rel->slug) }}" class="hover:text-sky transition-colors">
                                            {{ $rel->title }}
                                        </a>
                                    </h3>

                                    <p class="text-xs text-snow/55 leading-6">
                                        {{ \Illuminate\Support\Str::limit($rel->displayExcerpt(), 90) }}
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
</div>