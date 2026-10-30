<?php

use App\Models\Category;
use App\Models\Post;
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

    protected function applyFilters($query)
    {
        if (trim($this->q) !== '') {
            $search = trim($this->q);

            $query->where(function ($w) use ($search) {
                $w->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        if ($this->category !== '') {
            $query->whereHas('category', fn ($c) => $c->where('slug', $this->category));
        }

        return $query;
    }

    public function spotlight(): ?Post
    {
        $builders = [
            fn () => Post::query()->where('status', 'published')->orderByDesc('is_featured')->orderByDesc('published_at')->orderByDesc('id'),
            fn () => Post::query()->where('status', 'published')->orderByDesc('published_at')->orderByDesc('id'),
            fn () => Post::query()->where('is_active', true)->orderByDesc('id'),
            fn () => Post::query()->orderByDesc('id'),
        ];

        foreach ($builders as $b) {
            try {
                return $b()->first();
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }

    public function posts(?int $excludeId = null)
    {
        $builders = [
            fn () => Post::query()->where('status', 'published')->orderByDesc('published_at')->orderByDesc('id'),
            fn () => Post::query()->where('is_active', true)->orderByDesc('id'),
            fn () => Post::query()->orderByDesc('id'),
        ];

        foreach ($builders as $b) {
            try {
                $query = $b();

                if ($excludeId) {
                    $query->where('id', '!=', $excludeId);
                }

                $this->applyFilters($query);

                return $query->paginate(9)->withQueryString();
            } catch (\Throwable $e) {
                continue;
            }
        }

        return collect();
    }

    public function categories()
    {
        try {
            return Category::query()->orderBy('name')->get(['id', 'name', 'slug']);
        } catch (\Throwable $e) {
            return collect();
        }
    }
};

?>

@php
    $showSpot = ($q === '' && $category === '' && ! request('page'));
    $spot = $showSpot ? $this->spotlight() : null;
    $posts = $this->posts($spot?->id);
    $categories = $this->categories();
    $siteName = setting('site.name', 'رندیکس');
    $telegram = setting('social.telegram');
    $hasPagination = is_object($posts) && method_exists($posts, 'links');

    $postImage = function ($post): ?string {
        try { if (method_exists($post, 'coverUrl') && $post->coverUrl()) return $post->coverUrl(); } catch (\Throwable $e) {}
        try { if (method_exists($post, 'imageUrl') && $post->imageUrl()) return $post->imageUrl(); } catch (\Throwable $e) {}
        if (! empty($post->cover_image)) return asset('storage/' . $post->cover_image);
        if (! empty($post->image_path)) return asset('storage/' . $post->image_path);
        return null;
    };

    $postCategory = function ($post): ?string {
        if (isset($post->category) && is_object($post->category)) return $post->category->name ?? null;
        if (! empty($post->category_name)) return $post->category_name;
        if (! empty($post->category) && is_string($post->category)) return $post->category;
        return null;
    };

    $readingTime = function ($post): int {
        if (! empty($post->reading_time)) return (int) $post->reading_time;
        $words = str_word_count(strip_tags((string) ($post->content ?? $post->excerpt ?? '')));
        return max(1, (int) ceil($words / 200));
    };
@endphp

@once
<style>
.rx3-hero{position:relative;padding:140px 0 36px}
.rx3-hero .glow{position:absolute;top:30px;inset-inline:0;margin:auto;width:min(520px,80%);height:220px;background:radial-gradient(closest-side,var(--rx-glow-3,rgba(69,200,255,.09)),transparent);filter:blur(10px);pointer-events:none}
.rx3-hero h1{margin:0;font-size:clamp(32px,5vw,54px);font-weight:950;line-height:1.25;letter-spacing:-.02em;color:var(--rx-fg,#F7F9FF)}
.rx3-hero .lede{margin:14px 0 0;max-width:700px;color:var(--rx-fg-muted,rgba(247,249,255,.62));font-size:16px;line-height:2.1}
.rx3-spot{display:grid;grid-template-columns:1.05fr .95fr;border-radius:28px;overflow:hidden;border:1px solid var(--rx-border,rgba(255,255,255,.08));background:linear-gradient(135deg,var(--rx-panel,rgba(255,255,255,.055)),var(--rx-panel-2,rgba(255,255,255,.025)));box-shadow:0 24px 70px -34px var(--rx-shadow,rgba(0,0,0,.42));margin-bottom:26px;transition:border-color .3s ease,transform .3s ease}
.rx3-spot:hover{border-color:rgba(69,200,255,.26);transform:translateY(-3px)}
.rx3-spot-media{position:relative;min-height:300px;display:block;overflow:hidden;background:rgba(255,255,255,.035)}
.rx3-spot-media img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .5s ease}
.rx3-spot:hover .rx3-spot-media img{transform:scale(1.05)}
.rx3-spot-ph{position:absolute;inset:0;display:grid;place-items:center;color:rgba(69,200,255,.45);font-size:52px}
.rx3-spot-flag{position:absolute;top:16px;inset-inline-start:16px;display:inline-flex;align-items:center;gap:7px;padding:8px 13px;border-radius:999px;font-size:11px;font-weight:950;color:#fff;background:linear-gradient(90deg,var(--rx-accent-2,#1769FF),var(--rx-accent,#45C8FF));box-shadow:0 10px 26px -12px rgba(23,105,255,.9)}
.rx3-spot-body{padding:30px;display:flex;flex-direction:column;gap:14px;justify-content:center;min-width:0}
.rx3-meta{display:flex;flex-wrap:wrap;align-items:center;gap:12px;color:var(--rx-fg-soft,rgba(247,249,255,.36));font-size:12px;font-weight:850}
.rx3-meta .cat{color:var(--rx-accent-text,#45C8FF);background:rgba(69,200,255,.10);border:1px solid rgba(69,200,255,.20);padding:5px 10px;border-radius:999px;font-size:11px;font-weight:950}
.rx3-meta i{margin-inline-end:4px;opacity:.85}
.rx3-spot-body h2{margin:0;font-size:clamp(21px,2.6vw,31px);font-weight:950;line-height:1.4;color:var(--rx-fg,#F7F9FF)}
.rx3-spot-body h2 a{color:inherit;text-decoration:none;transition:color .22s ease}
.rx3-spot-body h2 a:hover{color:var(--rx-accent-text,#45C8FF)}
.rx3-spot-body p{margin:0;color:var(--rx-fg-muted,rgba(247,249,255,.62));font-size:13.5px;line-height:2.1;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
.rx3-more{display:inline-flex;align-items:center;gap:8px;color:var(--rx-accent-text,#45C8FF);font-size:13px;font-weight:950;text-decoration:none;transition:gap .22s ease}
.rx3-more:hover{gap:13px}
.rx3-bar{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin-bottom:22px}
.rx3-rail{display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;padding-bottom:4px;flex:1;min-width:0;mask-image:linear-gradient(to left,transparent 0,#000 26px)}
.rx3-rail::-webkit-scrollbar{display:none}
.rx3-chip{flex:0 0 auto;display:inline-flex;align-items:center;gap:7px;min-height:38px;padding:8px 15px;border-radius:999px;text-decoration:none;font-size:12.5px;font-weight:900;color:var(--rx-fg-muted,rgba(247,249,255,.62));background:var(--rx-panel-2,rgba(255,255,255,.025));border:1px solid var(--rx-border,rgba(255,255,255,.08));transition:all .22s ease}
.rx3-chip:hover{color:var(--rx-fg,#F7F9FF);border-color:rgba(69,200,255,.3);background:rgba(69,200,255,.08);transform:translateY(-1px)}
.rx3-chip.on{color:#fff;border-color:rgba(69,200,255,.45);background:linear-gradient(90deg,rgba(23,105,255,.35),rgba(69,200,255,.18));box-shadow:0 10px 26px -14px rgba(23,105,255,.9)}
.rx3-chip.clear{color:#fecaca;border-color:rgba(244,63,94,.25);background:rgba(244,63,94,.08)}
.rx3-chip.clear:hover{border-color:rgba(244,63,94,.45);background:rgba(244,63,94,.14)}
.rx3-search{display:flex;align-items:center;gap:8px}
.rx3-search-input{width:0;opacity:0;height:44px;padding:0;border:1px solid transparent;border-radius:999px;background:var(--rx-panel-2,rgba(255,255,255,.025));color:var(--rx-fg,#F7F9FF);outline:none;font-size:13px;transition:all .35s cubic-bezier(.16,1,.3,1)}
.rx3-search-input::placeholder{color:var(--rx-fg-soft,rgba(247,249,255,.36))}
.rx3-search.rx3-open .rx3-search-input{width:min(230px,58vw);opacity:1;padding:0 18px;border-color:var(--rx-border,rgba(255,255,255,.08))}
.rx3-search.rx3-open .rx3-search-input:focus{border-color:rgba(69,200,255,.55);box-shadow:0 0 0 4px rgba(69,200,255,.10)}
.rx3-search-btn{width:44px;height:44px;flex-shrink:0;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;color:var(--rx-fg-muted,rgba(247,249,255,.62));background:var(--rx-panel-2,rgba(255,255,255,.025));border:1px solid var(--rx-border,rgba(255,255,255,.08));transition:all .25s ease;font-size:14px}
.rx3-search-btn:hover{color:var(--rx-accent-text,#45C8FF);border-color:rgba(69,200,255,.35);background:rgba(69,200,255,.08);transform:translateY(-1px)}
.rx3-search.rx3-open .rx3-search-btn{color:#fff;border-color:rgba(69,200,255,.45);background:linear-gradient(135deg,var(--rx-accent-2,#1769FF),var(--rx-accent,#45C8FF))}
.rx3-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
.rx3-card{overflow:hidden;border-radius:24px;background:linear-gradient(135deg,var(--rx-panel,rgba(255,255,255,.055)),var(--rx-panel-2,rgba(255,255,255,.025)));border:1px solid var(--rx-border,rgba(255,255,255,.08));box-shadow:0 24px 70px -34px var(--rx-shadow,rgba(0,0,0,.42));transition:all .3s ease;display:flex;flex-direction:column}
.rx3-card:hover{transform:translateY(-5px);border-color:rgba(69,200,255,.26);box-shadow:0 32px 90px -34px rgba(23,105,255,.5)}
.rx3-card-media{position:relative;display:block;aspect-ratio:16/10;overflow:hidden;background:rgba(255,255,255,.035)}
.rx3-card-media img{width:100%;height:100%;object-fit:cover;transition:transform .45s ease}
.rx3-card:hover .rx3-card-media img{transform:scale(1.05)}
.rx3-card-ph{width:100%;height:100%;display:grid;place-items:center;color:rgba(69,200,255,.45);font-size:36px}
.rx3-card-cat{position:absolute;top:12px;inset-inline-start:12px;padding:6px 10px;border-radius:999px;font-size:10.5px;font-weight:950;color:#fff;background:rgba(7,19,41,.72);border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(12px)}
.rx3-card-body{padding:18px 20px 20px;display:flex;flex-direction:column;gap:10px;flex:1}
.rx3-card-body h3{margin:0;font-size:17px;line-height:1.65;font-weight:950}
.rx3-card-body h3 a{color:var(--rx-fg,#F7F9FF);text-decoration:none;transition:color .22s ease}
.rx3-card-body h3 a:hover{color:var(--rx-accent-text,#45C8FF)}
.rx3-card-body p{margin:0;color:var(--rx-fg-muted,rgba(247,249,255,.62));font-size:12.5px;line-height:2;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.rx3-card-foot{margin-top:auto;padding-top:6px}
.rx3-empty{margin-top:8px;padding:56px 24px;border-radius:26px;text-align:center;background:var(--rx-panel-2,rgba(255,255,255,.025));border:1px dashed var(--rx-border-strong,rgba(255,255,255,.16));display:grid;justify-items:center;gap:10px}
.rx3-empty i{font-size:40px;color:rgba(69,200,255,.45)}
.rx3-empty h3{margin:0;font-size:20px;font-weight:950;color:var(--rx-fg,#F7F9FF)}
.rx3-empty p{margin:0;color:var(--rx-fg-muted,rgba(247,249,255,.62));font-size:13px;line-height:2}
.rx3-cta{position:relative;overflow:hidden;border-radius:30px;padding:34px;display:grid;grid-template-columns:1.15fr .85fr;gap:26px;align-items:center;background:linear-gradient(135deg,rgba(23,105,255,.14),rgba(118,83,246,.10) 45%,rgba(255,107,74,.10));border:1px solid rgba(118,83,246,.22);margin-top:44px}
.rx3-cta::before{content:'';position:absolute;top:-120px;inset-inline-end:-80px;width:340px;height:340px;border-radius:999px;background:radial-gradient(circle,rgba(255,107,74,.18),transparent 65%);pointer-events:none}
.rx3-cta h2{margin:0 0 10px;font-size:clamp(21px,3vw,30px);font-weight:950;color:var(--rx-fg,#F7F9FF)}
.rx3-cta p{margin:0;color:var(--rx-fg-muted,rgba(247,249,255,.62));line-height:2;font-size:14.5px}
.rx3-cta-act{display:flex;flex-wrap:wrap;gap:12px;justify-content:flex-end}
@media(max-width:1100px){.rx3-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:900px){.rx3-spot{grid-template-columns:1fr}.rx3-spot-media{min-height:220px}.rx3-cta{grid-template-columns:1fr;padding:26px}.rx3-cta-act{justify-content:flex-start}}
@media(max-width:760px){.rx3-grid{grid-template-columns:1fr}.rx3-hero{padding:110px 0 30px}.rx3-bar{flex-direction:column;align-items:stretch}.rx3-search{justify-content:flex-end}}
</style>
@endonce

@section('rendix.title', 'بلاگ | ' . $siteName)
@section('rendix.description', 'مقالات رندیکس درباره طراحی وبسایت، سئو، دیجیتال مارکتینگ و رشد کسب‌وکار.')
@section('rendix.canonical', route('blog'))

<div>
    <section class="rx3-hero">
        <div class="glow" aria-hidden="true"></div>
        <div class="rx-container">
            <div class="badge fade-up"><span class="badge-dot"></span> مجله رندیکس</div>
            <h1 class="fade-up delay-1">دانشی که <span class="gradient-text">به رشد تبدیل می‌شود.</span></h1>
            <p class="lede fade-up delay-2">مقالات کاربردی درباره طراحی، سئو، محتوا و دیجیتال مارکتینگ؛ بدون حاشیه، با مثال واقعی.</p>
        </div>
    </section>

    <section style="padding-bottom:20px;">
        <div class="rx-container">

            @if ($spot)
                <article class="rx3-spot rv">
                    <a href="{{ route('blog.show', $spot->slug) }}" class="rx3-spot-media" wire:navigate>
                        @if ($postImage($spot))
                            <img src="{{ $postImage($spot) }}" alt="{{ $spot->title }}">
                        @else
                            <div class="rx3-spot-ph"><i class="fa-solid fa-feather-pointed"></i></div>
                        @endif
                        <span class="rx3-spot-flag"><i class="fa-solid fa-star"></i> پیشنهاد سردبیر</span>
                    </a>

                    <div class="rx3-spot-body">
                        <div class="rx3-meta">
                            @if ($postCategory($spot))<span class="cat">{{ $postCategory($spot) }}</span>@endif
                            <span><i class="fa-regular fa-calendar"></i> {{ \App\Support\Jalali::date($spot->published_at ?? $spot->created_at) }}</span>
                            <span><i class="fa-regular fa-clock"></i> {{ \App\Support\Jalali::toPersianDigits((string) $readingTime($spot)) }} دقیقه</span>
                        </div>

                        <h2><a href="{{ route('blog.show', $spot->slug) }}" wire:navigate>{{ $spot->title }}</a></h2>

                        <p>{{ \Illuminate\Support\Str::limit($spot->excerpt ?: strip_tags((string) ($spot->content ?? '')), 190) }}</p>

                        <a href="{{ route('blog.show', $spot->slug) }}" class="rx3-more" wire:navigate>
                            ادامه مطلب
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>
                </article>
            @endif

            <div class="rx3-bar">
                <div class="rx3-rail">
                    <a href="{{ route('blog', array_filter(['q' => $q])) }}" class="rx3-chip {{ $category === '' ? 'on' : '' }}" wire:navigate>همه</a>

                    @foreach ($categories as $cat)
                        <a
                            href="{{ route('blog', array_filter(['q' => $q, 'category' => $cat->slug])) }}"
                            class="rx3-chip {{ $category === $cat->slug ? 'on' : '' }}"
                            wire:navigate
                        >{{ $cat->name }}</a>
                    @endforeach

                    @if ($q !== '')
                        <a href="{{ route('blog', array_filter(['category' => $category])) }}" class="rx3-chip clear" wire:navigate>
                            <i class="fa-solid fa-xmark"></i> حذف جستجو
                        </a>
                    @endif
                </div>

                <form
                    method="GET"
                    action="{{ route('blog') }}"
                    class="rx3-search {{ $q !== '' ? 'rx3-open' : '' }}"
                    x-data="{ open: {{ $q !== '' ? 'true' : 'false' }} }"
                    :class="open ? 'rx3-open' : ''"
                >
                    @if ($category !== '')
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif

                    <input
                        x-ref="q"
                        type="search"
                        name="q"
                        value="{{ $q }}"
                        :disabled="! open"
                        placeholder="جستجو در مجله..."
                        class="rx3-search-input"
                        autocomplete="off"
                        aria-label="جستجوی مقاله"
                    >

                    <button
                        type="button"
                        class="rx3-search-btn"
                        aria-label="جستجو"
                        @click="
                            if (open) {
                                if ($refs.q.value !== '') {
                                    $refs.q.value = '';
                                    $refs.q.disabled = false;
                                    $el.requestSubmit();
                                }
                                open = false;
                            } else {
                                open = true;
                                $nextTick(() => $refs.q.focus());
                            }
                        "
                    >
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            @if (! $hasPagination || $posts->count() === 0)
                <div class="rx3-empty rv">
                    <i class="fa-solid fa-newspaper"></i>
                    <h3>مقاله‌ای پیدا نشد</h3>
                    <p>عبارت جستجو را عوض کن یا دسته‌بندی دیگری را امتحان کن.</p>
                    <a href="{{ route('blog') }}" class="btn btn-ghost btn-sm" wire:navigate>
                        <i class="fa-solid fa-rotate"></i>
                        <span>حذف فیلترها</span>
                    </a>
                </div>
            @else
                <div class="rx3-grid">
                    @foreach ($posts as $post)
                        <article class="rx3-card rv">
                            <a href="{{ route('blog.show', $post->slug) }}" class="rx3-card-media" wire:navigate>
                                @if ($postImage($post))
                                    <img src="{{ $postImage($post) }}" alt="{{ $post->title }}" loading="lazy">
                                @else
                                    <div class="rx3-card-ph"><i class="fa-solid fa-newspaper"></i></div>
                                @endif

                                @if ($postCategory($post))
                                    <span class="rx3-card-cat">{{ $postCategory($post) }}</span>
                                @endif
                            </a>

                            <div class="rx3-card-body">
                                <div class="rx3-meta">
                                    <span><i class="fa-regular fa-calendar"></i> {{ \App\Support\Jalali::date($post->published_at ?? $post->created_at) }}</span>
                                    <span><i class="fa-regular fa-clock"></i> {{ \App\Support\Jalali::toPersianDigits((string) $readingTime($post)) }} دقیقه</span>
                                </div>

                                <h3><a href="{{ route('blog.show', $post->slug) }}" wire:navigate>{{ $post->title }}</a></h3>

                                <p>{{ \Illuminate\Support\Str::limit($post->excerpt ?: strip_tags((string) ($post->content ?? '')), 100) }}</p>

                                <div class="rx3-card-foot">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="rx3-more" wire:navigate>
                                        ادامه مطلب
                                        <i class="fa-solid fa-arrow-left"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if ($hasPagination)
                    <div class="rx-public-pagination">
                        {{ $posts->links() }}
                    </div>
                @endif
            @endif

            <div class="rx3-cta rv">
                <div>
                    <h2>مقاله خواندن کافی نیست؛ اجرا کردن مهم است</h2>
                    <p>اگر می‌خواهی این دانش را روی کسب‌وکارت پیاده کنی، ما کنار تو هستیم.</p>
                </div>

                <div class="rx3-cta-act">
                    @if (Route::has('request-project'))
                        <a href="{{ route('request-project') }}" class="btn btn-primary" wire:navigate>
                            <i class="fa-solid fa-rocket"></i>
                            <span>درخواست پروژه</span>
                        </a>
                    @endif

                    @if (Route::has('contact'))
                        <a href="{{ route('contact') }}" class="btn btn-ghost" wire:navigate>
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>تماس</span>
                        </a>
                    @endif

                    @if ($telegram)
                        <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="btn btn-ghost">
                            <i class="fa-brands fa-telegram"></i>
                            <span>تلگرام</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>