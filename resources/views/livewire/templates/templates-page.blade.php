<?php

use App\Models\Template;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    #[Url]
    public string $q = '';

    #[Url]
    public string $type = '';

    #[Url]
    public string $lang = '';

    public function templates()
    {
        try {
            return Template::query()
                ->where('is_active', true)
                ->with('languages')
                ->when($this->q !== '', fn ($query) => $query->where('name', 'like', '%' . $this->q . '%'))
                ->when($this->type === 'fullstack', fn ($query) => $query->where('is_fullstack', true))
                ->when($this->type === 'frontend', fn ($query) => $query->where('is_fullstack', false))
                ->when($this->lang !== '', fn ($query) => $query->whereHas('languages', fn ($l) => $l->where('language', $this->lang)))
                ->orderByDesc('created_at')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function allLanguages(): array
    {
        try {
            return \App\Models\TemplateLanguage::query()->distinct()->orderBy('language')->pluck('language')->all();
        } catch (\Throwable $e) {
            return [];
        }
    }
};

?>

@php
    $templates = $this->templates();
    $languages = $this->allLanguages();
    $siteName = setting('site.name', 'رندیکس');
@endphp

@once
<style>
.rx-shop-hero{position:relative;padding:150px 0 60px;overflow:hidden;text-align:center}
.rx-shop-hero::before{content:'';position:absolute;top:-80px;inset-inline-start:15%;width:480px;height:480px;border-radius:999px;background:radial-gradient(circle,rgba(69,200,255,.18),transparent 65%);filter:blur(70px);pointer-events:none;animation:rxShopOrb1 14s ease-in-out infinite}
.rx-shop-hero::after{content:'';position:absolute;top:30%;inset-inline-end:5%;width:380px;height:380px;border-radius:999px;background:radial-gradient(circle,rgba(118,83,246,.18),transparent 65%);filter:blur(70px);pointer-events:none;animation:rxShopOrb2 18s ease-in-out infinite}
@keyframes rxShopOrb1{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(40px,25px) scale(1.08)}}
@keyframes rxShopOrb2{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(-35px,-20px) scale(1.06)}}
.rx-shop-hero-inner{position:relative;z-index:1;max-width:820px;margin-inline:auto}
.rx-shop-badge{display:inline-flex;align-items:center;gap:9px;padding:9px 18px;border-radius:999px;font-size:13px;font-weight:900;color:var(--rx-accent-text);background:rgba(69,200,255,.08);border:1px solid rgba(69,200,255,.22);margin-bottom:18px}
.rx-shop-title{margin:0;font-size:clamp(36px,6vw,64px);line-height:1.15;font-weight:950;letter-spacing:-.025em}
.rx-shop-subtitle{margin:20px auto 0;max-width:640px;color:var(--rx-fg-muted);font-size:16.5px;line-height:2.1}
.rx-shop-toolbar{max-width:980px;margin:44px auto 0;display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:12px}
.rx-shop-search{position:relative;flex:0 1 320px;width:min(320px,100%)}
.rx-shop-search>i{position:absolute;inset-inline-start:18px;top:50%;transform:translateY(-50%);color:var(--rx-fg-soft);font-size:14px;pointer-events:none;transition:color .22s}
.rx-shop-search:focus-within>i{color:var(--rx-accent-text)}
.rx-shop-search input{width:100%;min-height:48px;padding:12px 46px;border-radius:999px;background:var(--rx-panel);border:1px solid var(--rx-border);color:var(--rx-fg);outline:none;font-size:14px;transition:all .25s}
.rx-shop-search input::placeholder{color:var(--rx-fg-soft)}
.rx-shop-search input:focus{border-color:rgba(69,200,255,.5);box-shadow:0 0 0 4px rgba(69,200,255,.1)}
.rx-shop-filters{display:flex;gap:8px;flex-wrap:wrap;justify-content:center}
.rx-shop-filter{display:inline-flex;align-items:center;gap:7px;min-height:44px;padding:9px 16px;border-radius:999px;background:var(--rx-panel-2);border:1px solid var(--rx-border);color:var(--rx-fg-muted);font-size:13px;font-weight:850;cursor:pointer;transition:all .22s;text-decoration:none;white-space:nowrap}
.rx-shop-filter:hover{color:var(--rx-fg);border-color:rgba(69,200,255,.3);background:rgba(69,200,255,.08)}
.rx-shop-filter.is-active{color:#fff;border-color:rgba(69,200,255,.45);background:linear-gradient(90deg,rgba(23,105,255,.35),rgba(69,200,255,.18));box-shadow:0 10px 26px -14px rgba(23,105,255,.9)}
.rx-shop-filter i{font-size:12px}
.rx-templates-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px;margin-top:50px}
.rx-template-card{position:relative;overflow:hidden;border-radius:26px;background:linear-gradient(135deg,var(--rx-panel),var(--rx-panel-2));border:1px solid var(--rx-border);box-shadow:0 24px 70px -34px var(--rx-shadow);transition:all .35s var(--rx-ease);display:flex;flex-direction:column}
.rx-template-card:hover{transform:translateY(-6px);border-color:rgba(69,200,255,.28);box-shadow:0 36px 90px -34px rgba(23,105,255,.5)}
.rx-template-preview{position:relative;aspect-ratio:16/10;background:linear-gradient(135deg,rgba(23,105,255,.14),rgba(118,83,246,.10) 50%,rgba(69,200,255,.08));overflow:hidden;display:grid;place-items:center;border-bottom:1px solid var(--rx-border)}
.rx-template-demo-thumb{position:relative;z-index:1;display:flex;flex-direction:column;align-items:center;gap:8px;color:#fff}
.rx-template-demo-thumb i{width:64px;height:64px;border-radius:999px;display:grid;place-items:center;background:linear-gradient(135deg,var(--rx-accent-2),var(--rx-accent));font-size:24px;box-shadow:0 14px 36px -12px rgba(23,105,255,.95);transition:transform .3s var(--rx-ease)}
.rx-template-card:hover .rx-template-demo-thumb i{transform:scale(1.12)}
.rx-template-demo-thumb span{font-size:11px;font-weight:900;letter-spacing:.08em;opacity:.9}
.rx-template-placeholder{font-size:52px;color:rgba(69,200,255,.35)}
.rx-template-badges{position:absolute;top:14px;inset-inline-start:14px;display:flex;gap:6px;flex-wrap:wrap;z-index:2}
.rx-template-badge{display:inline-flex;align-items:center;gap:6px;padding:6px 11px;border-radius:999px;font-size:11px;font-weight:950;color:#fff;backdrop-filter:blur(12px)}
.rx-template-badge.fullstack{background:linear-gradient(90deg,rgba(23,105,255,.9),rgba(69,200,255,.9))}
.rx-template-badge.frontend{background:linear-gradient(90deg,rgba(118,83,246,.9),rgba(69,200,255,.9))}
.rx-template-body{padding:22px;display:flex;flex-direction:column;gap:12px;flex:1}
.rx-template-languages{display:flex;flex-wrap:wrap;gap:6px}
.rx-lang-pill{display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;font-size:11px;font-weight:950;color:var(--rx-fg);border:1px solid var(--rx-border)}
.rx-template-title{margin:0;font-size:19px;line-height:1.55;font-weight:950}
.rx-template-title a{color:var(--rx-fg);text-decoration:none;transition:color .22s}
.rx-template-title a:hover{color:var(--rx-accent-text)}
.rx-template-excerpt{margin:0;color:var(--rx-fg-muted);font-size:13px;line-height:2;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:52px}
.rx-template-features{display:flex;flex-wrap:wrap;gap:8px;margin-top:2px}
.rx-feature{display:inline-flex;align-items:center;gap:5px;padding:4px 9px;border-radius:999px;font-size:11px;font-weight:850;color:var(--rx-fg-muted);background:var(--rx-panel-2);border:1px solid var(--rx-border)}
.rx-feature i{color:var(--rx-accent-text);font-size:10px}
.rx-template-footer{margin-top:auto;padding-top:14px;border-top:1px solid var(--rx-border);display:flex;align-items:center;justify-content:space-between;gap:12px}
.rx-template-price strong{font-size:17px;font-weight:950;background:linear-gradient(90deg,var(--rx-accent),var(--rx-accent-2) 55%,var(--rx-accent-3));-webkit-background-clip:text;background-clip:text;color:transparent}
.rx-template-price small{display:block;color:var(--rx-fg-soft);font-size:10px;font-weight:800;margin-top:2px}
.rx-shop-empty{grid-column:1/-1;padding:70px 24px;border-radius:28px;text-align:center;background:var(--rx-panel-2);border:1px dashed var(--rx-border-strong)}
.rx-shop-empty i{font-size:48px;color:rgba(69,200,255,.4)}
.rx-shop-empty h3{margin:14px 0 0;font-size:20px;font-weight:950}
.rx-shop-empty p{margin:8px 0 0;color:var(--rx-fg-muted)}
@media(max-width:1100px){.rx-templates-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:760px){.rx-templates-grid{grid-template-columns:1fr}.rx-shop-hero{padding:120px 0 40px}}
</style>
@endonce

@section('rendix.title', 'فروشگاه قالب برنامه‌نویسی | ' . $siteName)
@section('rendix.description', 'قالب‌های برنامه‌نویسی حرفه‌ای با دمو زنده، کد تمیز و پشتیبانی — فروشگاه قالب رندیکس')
@section('rendix.canonical', route('templates.index'))

<div>
    <section class="rx-shop-hero">
        <div class="rx-container rx-shop-hero-inner">
            <span class="rx-shop-badge fade-up"><i class="fa-solid fa-code"></i> فروشگاه قالب رندیکس</span>
            <h1 class="rx-shop-title fade-up delay-1">قالب‌های <span class="gradient-text">برنامه‌نویسی</span></h1>
            <p class="rx-shop-subtitle fade-up delay-2">کدهای تمیز، طراحی حرفه‌ای و دمو زنده؛ برای توسعه‌دهندگانی که وقت ندارند از صفر شروع کنند.</p>

            <div class="rx-shop-toolbar fade-up delay-3">
                <form method="GET" action="{{ route('templates.index') }}" class="rx-shop-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="search" name="q" value="{{ $q }}" placeholder="جستجوی قالب..." autocomplete="off">
                    @if ($type !== '')<input type="hidden" name="type" value="{{ $type }}">@endif
                    @if ($lang !== '')<input type="hidden" name="lang" value="{{ $lang }}">@endif
                </form>

                <div class="rx-shop-filters">
                    <a href="{{ route('templates.index', array_filter(['q' => $q, 'lang' => $lang])) }}" class="rx-shop-filter {{ $type === '' ? 'is-active' : '' }}"><i class="fa-solid fa-layer-group"></i> همه</a>
                    <a href="{{ route('templates.index', array_filter(['q' => $q, 'type' => 'fullstack', 'lang' => $lang])) }}" class="rx-shop-filter {{ $type === 'fullstack' ? 'is-active' : '' }}"><i class="fa-solid fa-server"></i> Full-Stack</a>
                    <a href="{{ route('templates.index', array_filter(['q' => $q, 'type' => 'frontend', 'lang' => $lang])) }}" class="rx-shop-filter {{ $type === 'frontend' ? 'is-active' : '' }}"><i class="fa-solid fa-palette"></i> Front-End</a>
                </div>
            </div>
        </div>
    </section>

    <section style="padding-bottom:40px;">
        <div class="rx-container">
            @if (! empty($languages))
                <div class="rx-shop-filters" style="margin-bottom:8px;">
                    <a href="{{ route('templates.index', array_filter(['q' => $q, 'type' => $type])) }}" class="rx-shop-filter {{ $lang === '' ? 'is-active' : '' }}">همه زبان‌ها</a>
                    @foreach ($languages as $l)
                        <a href="{{ route('templates.index', array_filter(['q' => $q, 'type' => $type, 'lang' => $l])) }}" class="rx-shop-filter {{ $lang === $l ? 'is-active' : '' }}">{{ $l }}</a>
                    @endforeach
                </div>
            @endif

            @if ($templates->isEmpty())
                <div class="rx-shop-empty">
                    <i class="fa-solid fa-code-branch"></i>
                    <h3>قالبی با این فیلتر پیدا نشد</h3>
                    <p>عبارت جستجو یا فیلترها را عوض کن.</p>
                </div>
            @else
                <div class="rx-templates-grid">
                    @foreach ($templates as $template)
                        <article class="rx-template-card">
                            <a href="{{ route('templates.show', $template->slug) }}" class="rx-template-preview" wire:navigate>
                                <div class="rx-template-badges">
                                    <span class="rx-template-badge {{ $template->is_fullstack ? 'fullstack' : 'frontend' }}">
                                        <i class="fa-solid {{ $template->is_fullstack ? 'fa-layer-group' : 'fa-palette' }}"></i>
                                        {{ $template->is_fullstack ? 'Full-Stack' : 'Front-End' }}
                                    </span>
                                </div>
                                @if ($template->demoFile())
                                    <div class="rx-template-demo-thumb"><i class="fa-solid fa-play"></i><span>دمو زنده</span></div>
                                @else
                                    <div class="rx-template-placeholder"><i class="fa-solid fa-code"></i></div>
                                @endif
                            </a>

                            <div class="rx-template-body">
                                <div class="rx-template-languages">
                                    @foreach ($template->languages as $lng)
                                        <span class="rx-lang-pill" style="background:{{ $lng->color ?? 'rgba(69,200,255,.12)' }};">{{ $lng->language }}</span>
                                    @endforeach
                                </div>

                                <h2 class="rx-template-title"><a href="{{ route('templates.show', $template->slug) }}" wire:navigate>{{ $template->name }}</a></h2>

                                @if ($template->description)
                                    <p class="rx-template-excerpt">{{ \Illuminate\Support\Str::limit($template->description, 120) }}</p>
                                @endif

                                <div class="rx-template-features">
                                    @if ($template->has_support)<span class="rx-feature"><i class="fa-solid fa-headset"></i> پشتیبانی</span>@endif
                                    @if ($template->has_trial)<span class="rx-feature"><i class="fa-solid fa-clock"></i> مهلت تست</span>@endif
                                    @if ($template->has_guarantee)<span class="rx-feature"><i class="fa-solid fa-shield-halved"></i> ضمانت</span>@endif
                                </div>

                                <div class="rx-template-footer">
                                    <div class="rx-template-price">
                                        <strong>{{ number_format($template->price, 0, '.', ',') }}</strong>
                                        <small>تومان</small>
                                    </div>
                                    <a href="{{ route('templates.show', $template->slug) }}" class="btn btn-primary btn-sm" wire:navigate>
                                        <span>مشاهده</span><i class="fa-solid fa-arrow-left"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>