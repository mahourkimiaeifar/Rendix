<?php

use App\Models\Category;
use App\Models\Post;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    #[Url]
    public string $q = '';

    #[Url]
    public string $category = '';

    public function updatingQ(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function categories()
    {
        try {
            return Category::query()
                ->active()
                ->ordered()
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function fetchPosts()
    {
        try {
            return Post::query()
                ->published()
                ->with(['category', 'author'])
                ->when(trim($this->q) !== '', function ($query) {
                    $search = trim($this->q);

                    $query->where(function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%")
                          ->orWhere('excerpt', 'like', "%{$search}%")
                          ->orWhere('content', 'like', "%{$search}%");
                    });
                })
                ->when($this->category !== '', function ($query) {
                    $query->whereHas('category', function ($q) {
                        $q->where('slug', $this->category)
                          ->where('is_active', true);
                    });
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
    $posts = $this->fetchPosts();
    $categories = $this->categories();

    $siteName = setting('site.name', 'رندیکس');
    $hasPagination = is_object($posts) && method_exists($posts, 'links');
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-7xl mx-auto">

            <div class="max-w-3xl space-y-6 mb-14">
                <div class="badge fade-up">
                    <span class="badge-dot"></span>
                    مرکز محتوای {{ $siteName }}
                </div>

                <h1 class="text-[38px] md:text-5xl font-black leading-[1.25] fade-up delay-1">
                    بلاگ رندیکس
                    <br>
                    <span class="gradient-text">دانش دیجیتال برای رشد کسب‌وکار</span>
                </h1>

                <p class="text-lg text-snow/60 leading-9 fade-up delay-2">
                    مقالات کاربردی درباره طراحی وبسایت، سئو، دیجیتال مارکتینگ، تجربه کاربری و رشد آنلاین.
                </p>
            </div>

            <div class="glass p-5 md:p-6 mb-10 space-y-5">
                <div class="grid md:grid-cols-[1fr_auto] gap-4">
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="q"
                        placeholder="جستجو در مقالات..."
                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                    >

                    @if ($this->category !== '')
                        <button type="button" wire:click="$set('category', '')" class="btn btn-primary">
                            حذف فیلتر دسته
                        </button>
                    @endif
                </div>

                @if ($categories->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        <button
                            type="button"
                            wire:click="$set('category', '')"
                            class="text-xs md:text-sm border rounded-xl px-3 py-2 transition-all
                            {{ $this->category === '' ? 'border-sky bg-sky/10 text-sky' : 'border-white/10 bg-white/5 text-snow/60 hover:text-sky' }}"
                        >
                            همه
                        </button>

                        @foreach ($categories as $cat)
                            <button
                                type="button"
                                wire:click="$set('category', '{{ $cat->slug }}')"
                                class="text-xs md:text-sm border rounded-xl px-3 py-2 transition-all
                                {{ $this->category === $cat->slug ? 'border-sky bg-sky/10 text-sky' : 'border-white/10 bg-white/5 text-snow/60 hover:text-sky' }}"
                            >
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($posts->count() === 0)
                <div class="glass p-7 md:p-10 max-w-3xl mx-auto text-center space-y-5">
                    <div class="text-5xl text-sky">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>

                    <h2 class="text-2xl md:text-3xl font-black">
                        مقاله‌ای پیدا نشد
                    </h2>

                    <p class="text-snow/60 leading-8">
                        هنوز محتوایی با این فیلتر منتشر نشده است. به‌زودی مقالات بیشتری اینجا اضافه می‌شود.
                    </p>

                    <a href="{{ route('request-project') }}" class="btn btn-primary">
                        درخواست پروژه
                        <i class="fa-solid fa-arrow-left"></i>
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
                                @if ($post->category)
                                    <a
                                        href="{{ route('category', $post->category->slug) }}"
                                        class="inline-flex text-[11px] font-black text-sky bg-sky/10 border border-sky/20 rounded-full px-3 py-1"
                                    >
                                        {{ $post->category->name }}
                                    </a>
                                @endif

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
                                    {{ Jalali::toPersianDigits((string) $post->readingTime()) }} دقیقه مطالعه
                                </span>
                            </div>

                            <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-primary w-full">
                                خواندن مقاله
                                <i class="fa-solid fa-arrow-left"></i>
                            </a>
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