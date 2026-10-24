<?php

use App\Models\Post;
use App\Models\Tag;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = trim($slug);
    }

    public function tag(): ?Tag
    {
        try {
            return Tag::query()->where('slug', $this->slug)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function fetchPosts()
    {
        try {
            return Post::query()
                ->published()
                ->with(['category', 'author'])
                ->whereHas('tags', function ($q) {
                    $q->where('slug', $this->slug);
                })
                ->ordered()
                ->paginate(9)
                ->withQueryString();
        } catch (\Throwable $e) {
            return collect();
        }
    }
};

?>

@php
    $tag = $this->tag();

    if (! $tag) {
        abort(404);
    }

    $posts = $this->fetchPosts();
    $hasPagination = is_object($posts) && method_exists($posts, 'links');
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-7xl mx-auto">

            <a href="{{ route('blog') }}" class="inline-flex items-center gap-2 text-sm font-bold text-snow/55 hover:text-sky transition-colors mb-8">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به بلاگ
            </a>

            <div class="max-w-3xl space-y-5 mb-12">
                <div class="badge">
                    <span class="badge-dot"></span>
                    تگ مقالات
                </div>

                <h1 class="text-3xl md:text-5xl font-black leading-[1.25]">
                    #{{ $tag->name }}
                </h1>
            </div>

            @if ($posts->count() === 0)
                <div class="glass p-7 md:p-10 max-w-3xl mx-auto text-center space-y-5">
                    <div class="text-5xl text-sky">
                        <i class="fa-solid fa-tags"></i>
                    </div>

                    <h2 class="text-2xl md:text-3xl font-black">
                        هنوز مقاله‌ای با این تگ منتشر نشده است
                    </h2>

                    <a href="{{ route('blog') }}" class="btn btn-primary">
                        مشاهده همه مقالات
                    </a>
                </div>
            @else
                <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach ($posts as $post)
                        <article class="glass p-6 space-y-5 hover:border-sky/30 transition-all">
                            <a href="{{ route('blog.show', $post->slug) }}" class="block rounded-2xl overflow-hidden border border-white/10 bg-white/5">
                                @if ($post->image_path)
                                    <img
                                        src="{{ $post->imageUrl() }}"
                                        alt="{{ $post->title }}"
                                        class="w-full h-48 object-cover"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="w-full h-48 grid place-items-center text-sky/45 text-4xl">
                                        <i class="fa-solid fa-article"></i>
                                    </div>
                                @endif
                            </a>

                            <div class="space-y-3">
                                <h2 class="text-xl font-black leading-8">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-sky transition-colors">
                                        {{ $post->title }}
                                    </a>
                                </h2>

                                <p class="text-sm text-snow/58 leading-7">
                                    {{ $post->displayExcerpt() }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between gap-3 text-xs text-snow/45 border-t border-white/10 pt-4">
                                <span>
                                    <i class="fa-solid fa-calendar-days ml-1"></i>
                                    {{ $post->published_at ? Jalali::date($post->published_at) : '—' }}
                                </span>

                                <span>
                                    <i class="fa-solid fa-stopwatch ml-1"></i>
                                    {{ Jalali::toPersianDigits((string) $post->readingTime()) }} دقیقه
                                </span>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($hasPagination)
                    <div class="mt-12 flex justify-center">
                        {{ $posts->links() }}
                    </div>
                @endif
            @endif
        </div>
    </section>
</div>