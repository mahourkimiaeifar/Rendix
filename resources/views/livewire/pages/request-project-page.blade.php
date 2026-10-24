<?php

use App\Models\Lead;
use App\Models\ProjectRequest;
use App\Models\ProjectRequestAttachment;
use App\Models\Service;
use App\Support\ProjectRequestNotifier;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public int $step = 1;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $role = '';

    public string $service = '';
    public string $currentWebsite = '';
    public string $currentWebsiteUrl = '';

    public string $businessDescription = '';
    public string $projectGoals = '';
    public string $competitors = '';
    public string $references = '';
    public string $timeline = '';
    public string $budget = '';
    public string $message = '';

    public $attachments = [];

    public function mount(): void
    {
        if (auth()->check()) {
            $user = auth()->user();

            $this->name = (string) ($user->name ?? '');
            $this->email = (string) ($user->email ?? '');
            $this->phone = (string) ($user->phone ?? '');
        }

        $requested = (string) request('service');

        if ($requested !== '' && array_key_exists($requested, $this->serviceOptions())) {
            $this->service = $requested;
        }
    }

    public function serviceOptions(): array
    {
        try {
            $options = Service::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->pluck('title', 'slug')
                ->all();
        } catch (\Throwable $e) {
            $options = [];
        }

        if ($options === []) {
            try {
                $options = Lead::serviceOptions();
            } catch (\Throwable $e) {
                $options = [];
            }
        }

        $options['other'] = 'سایر / راهنمایی می‌خواهم';

        return $options;
    }

    public function removeAttachment(int $index): void
    {
        unset($this->attachments[$index]);

        $this->attachments = array_values($this->attachments);
    }

    public function nextStep()
    {
        if ($this->step === 1) {
            $this->validate([
                'name' => ['required', 'string', 'min:3', 'max:120'],
                'phone' => ['required', 'string', 'min:10', 'max:20'],
                'email' => ['required', 'email', 'max:190'],
                'company' => ['nullable', 'string', 'max:120'],
                'role' => ['nullable', 'string', 'max:120'],
            ], [
                'name.required' => 'نام خود را وارد کنید.',
                'name.min' => 'نام باید حداقل ۳ کاراکتر باشد.',
                'phone.required' => 'شماره تماس را وارد کنید.',
                'phone.min' => 'شماره تماس معتبر نیست.',
                'email.required' => 'ایمیل را وارد کنید.',
                'email.email' => 'ایمیل معتبر نیست.',
            ]);
        }

        if ($this->step === 2) {
            $this->validate([
                'service' => [
                    'required',
                    'string',
                    'in:' . implode(',', array_keys($this->serviceOptions())),
                ],
                'currentWebsite' => [
                    'required',
                    'string',
                    'in:' . implode(',', array_keys(ProjectRequest::websiteStatusOptions())),
                ],
                'currentWebsiteUrl' => ['nullable', 'url', 'max:255'],
            ], [
                'service.required' => 'خدمت مورد نظر را انتخاب کنید.',
                'service.in' => 'خدمت انتخابی معتبر نیست.',
                'currentWebsite.required' => 'وضعیت وبسایت فعلی را مشخص کنید.',
                'currentWebsite.in' => 'مقدار انتخابی معتبر نیست.',
                'currentWebsiteUrl.url' => 'آدرس سایت معتبر نیست.',
            ]);
        }

        if ($this->step === 3) {
            $this->validate([
                'businessDescription' => ['required', 'string', 'min:20', 'max:5000'],
                'projectGoals' => ['required', 'string', 'min:10', 'max:3000'],
                'timeline' => [
                    'required',
                    'string',
                    'in:' . implode(',', array_keys(ProjectRequest::timelineOptions())),
                ],
                'budget' => [
                    'required',
                    'string',
                    'in:' . implode(',', array_keys(ProjectRequest::budgetOptions())),
                ],
                'competitors' => ['nullable', 'string', 'max:2000'],
                'references' => ['nullable', 'string', 'max:2000'],
                'message' => ['nullable', 'string', 'max:5000'],
            ], [
                'businessDescription.required' => 'درباره کسب‌وکارت بنویس.',
                'businessDescription.min' => 'توضیح کسب‌وکار باید حداقل ۲۰ کاراکتر باشد.',
                'projectGoals.required' => 'هدف پروژه را مشخص کن.',
                'projectGoals.min' => 'هدف پروژه باید حداقل ۱۰ کاراکتر باشد.',
                'timeline.required' => 'زمان‌بندی مورد نظر را انتخاب کن.',
                'budget.required' => 'بودجه تقریبی را انتخاب کن.',
            ]);
        }

        if ($this->step < 4) {
            $this->step++;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit()
    {
        $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'email' => ['required', 'email', 'max:190'],
            'service' => [
                'required',
                'string',
                'in:' . implode(',', array_keys($this->serviceOptions())),
            ],
            'currentWebsite' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(ProjectRequest::websiteStatusOptions())),
            ],
            'businessDescription' => ['required', 'string', 'min:20', 'max:5000'],
            'projectGoals' => ['required', 'string', 'min:10', 'max:3000'],
            'timeline' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(ProjectRequest::timelineOptions())),
            ],
            'budget' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(ProjectRequest::budgetOptions())),
            ],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => [
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png,webp,zip,rar',
                'max:10240',
            ],
        ], [
            'attachments.*.file' => 'فایل‌ها باید فایل معتبر باشند.',
            'attachments.*.mimes' => 'فقط فایل‌های PDF، Word، تصویر، ZIP و RAR مجاز است.',
            'attachments.*.max' => 'حجم هر فایل نباید بیشتر از ۱۰ مگابایت باشد.',
        ]);

        $key = 'rendix-project-request:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('message', 'برای جلوگیری از ثبت درخواست‌های تکراری، چند دقیقه صبر کن و دوباره تلاش کن.');

            return;
        }

        $request = ProjectRequest::create([
            'user_id' => auth()->id(),
            'name' => trim($this->name),
            'email' => trim($this->email),
            'phone' => trim($this->phone),
            'company' => trim($this->company) ?: null,
            'role' => trim($this->role) ?: null,
            'service' => $this->service,
            'current_website' => $this->currentWebsite,
            'current_website_url' => trim($this->currentWebsiteUrl) ?: null,
            'business_description' => trim($this->businessDescription),
            'project_goals' => trim($this->projectGoals),
            'competitors' => trim($this->competitors) ?: null,
            'references' => trim($this->references) ?: null,
            'timeline' => $this->timeline,
            'budget' => $this->budget,
            'message' => trim($this->message) ?: null,
            'status' => 'new',
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'referer' => request()->header('referer'),
                'query' => request()->query(),
                'device' => str_contains((string) request()->userAgent(), 'Mobile') ? 'mobile' : 'desktop',
            ],
        ]);

        foreach ($this->attachments as $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('project-requests/' . $request->id, 'public');

            ProjectRequestAttachment::create([
                'project_request_id' => $request->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        RateLimiter::hit($key, 60 * 15);

        ProjectRequestNotifier::created($request);

        return redirect()->route('request-project.show', [
            'code' => $request->tracking_code,
        ]);
    }
};

?>

@php
    $serviceOptions = $this->serviceOptions();
    $websiteOptions = \App\Models\ProjectRequest::websiteStatusOptions();
    $timelineOptions = \App\Models\ProjectRequest::timelineOptions();
    $budgetOptions = \App\Models\ProjectRequest::budgetOptions();

    $siteName = setting('site.name', 'رندیکس');
    $telegram = setting('social.telegram', 'https://t.me/RendixDMServices');

    $steps = [
        1 => 'اطلاعات شما',
        2 => 'خدمت و وضعیت فعلی',
        3 => 'جزئیات پروژه',
        4 => 'تأیید و ارسال',
    ];

    $pageTitle = 'درخواست پروژه چندمرحله‌ای | ' . $siteName;
    $pageDescription = 'پروژه دیجیتال خود را مرحله‌به‌مرحله تعریف کن؛ خدمت، بودجه، زمان‌بندی، فایل‌ها و اهداف را برای رندیکس ارسال کن.';
    $canonical = route('request-project');
@endphp

@section('rendix.title', $pageTitle)
@section('rendix.description', $pageDescription)
@section('rendix.canonical', $canonical)
@section('rendix.type', 'WebPage')

@push('rendix.head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'چطور درخواست پروژه بدهم؟',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'با پر کردن فرم چندمرحله‌ای درخواست پروژه، اطلاعات تماس، خدمت مورد نظر، جزئیات پروژه، بودجه و زمان‌بندی را برای رندیکس ارسال کنید.',
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'آیا می‌توانم فایل ضمیمه کنم؟',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'بله. می‌توانید لوگو، بریف، تصاویر مرجع یا فایل‌های مرتبط با پروژه را در فرم درخواست پروژه آپلود کنید.',
                    ],
                ],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>
@endpush

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-5xl mx-auto">

            <div class="max-w-3xl space-y-6 mb-12">
                <div class="badge fade-up">
                    <span class="badge-dot"></span>
                    درخواست پروژه {{ $siteName }}
                </div>

                <h1 class="text-[38px] md:text-5xl font-black leading-[1.25] fade-up delay-1">
                    پروژه‌ات را مرحله‌به‌مرحله
                    <br>
                    <span class="gradient-text">برای ما تعریف کن.</span>
                </h1>

                <p class="text-lg text-snow/60 leading-9 fade-up delay-2">
                    هرچه اطلاعات دقیق‌تر باشد، پیشنهاد ما شفاف‌تر، سریع‌تر و نزدیک‌تر به نیاز واقعی کسب‌وکارت خواهد بود.
                </p>
            </div>

            <div class="glass p-6 md:p-9 space-y-8">

                {{-- Progress --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ($steps as $number => $label)
                        <div
                            class="rounded-2xl border px-4 py-3 text-center transition-all
                            {{ $step === $number ? 'border-sky bg-sky/10 text-sky' : ($step > $number ? 'border-emerald-400/25 bg-emerald-400/10 text-emerald-300' : 'border-white/10 bg-white/5 text-snow/45') }}"
                        >
                            <div class="text-xs font-black mb-1">
                                مرحله {{ $number }}
                            </div>
                            <div class="text-[11px] md:text-xs font-bold leading-6">
                                {{ $label }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <form wire:submit="submit" class="space-y-8">

                    {{-- Step 1 --}}
                    @if ($step === 1)
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <h2 class="text-2xl font-black">اطلاعات تماس</h2>
                                <p class="text-sm text-snow/55 leading-7">
                                    اول بگو با کی در ارتباطیم. اطلاعاتت فقط برای پیگیری پروژه استفاده می‌شود.
                                </p>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-snow/70">نام و نام خانوادگی *</label>
                                    <input
                                        type="text"
                                        wire:model="name"
                                        placeholder="مثلاً: علی رضایی"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                                    >
                                    @error('name') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-snow/70">شماره تماس *</label>
                                    <input
                                        type="tel"
                                        wire:model="phone"
                                        placeholder="09xxxxxxxxx"
                                        dir="ltr"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                                    >
                                    @error('phone') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-snow/70">ایمیل *</label>
                                    <input
                                        type="email"
                                        wire:model="email"
                                        placeholder="you@example.com"
                                        dir="ltr"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                                    >
                                    @error('email') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-snow/70">نام شرکت / برند</label>
                                    <input
                                        type="text"
                                        wire:model="company"
                                        placeholder="مثلاً: فروشگاه نمونه"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                                    >
                                    @error('company') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-sm font-bold text-snow/70">سمت شما</label>
                                    <input
                                        type="text"
                                        wire:model="role"
                                        placeholder="مثلاً: مدیرعامل، مسئول بازاریابی، صاحب کسب‌وکار"
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                                    >
                                    @error('role') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Step 2 --}}
                    @if ($step === 2)
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <h2 class="text-2xl font-black">خدمت مورد نظر</h2>
                                <p class="text-sm text-snow/55 leading-7">
                                    کدام خدمت برای کسب‌وکارت اولویت دارد؟ اگر مطمئن نیستی، گزینه «سایر» را انتخاب کن.
                                </p>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach ($serviceOptions as $value => $label)
                                    <label class="cursor-pointer">
                                        <input
                                            type="radio"
                                            name="service"
                                            value="{{ $value }}"
                                            wire:model="service"
                                            class="hidden peer"
                                        >
                                        <div
                                            class="text-center text-xs md:text-sm border border-white/10 rounded-xl px-3 py-4 text-snow/60 peer-checked:border-sky peer-checked:bg-sky/10 peer-checked:text-sky transition-all"
                                        >
                                            {{ $label }}
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            @error('service') <span class="text-xs text-red-400">{{ $message }}</span> @enderror

                            <div class="space-y-4 pt-4 border-t border-white/10">
                                <h3 class="text-lg font-black">وضعیت وبسایت فعلی</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    @foreach ($websiteOptions as $value => $label)
                                        <label class="cursor-pointer">
                                            <input
                                                type="radio"
                                                name="currentWebsite"
                                                value="{{ $value }}"
                                                wire:model="currentWebsite"
                                                class="hidden peer"
                                            >
                                            <div
                                                class="text-center text-sm border border-white/10 rounded-xl px-4 py-4 text-snow/60 peer-checked:border-sky peer-checked:bg-sky/10 peer-checked:text-sky transition-all"
                                            >
                                                {{ $label }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('currentWebsite') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-snow/70">آدرس وبسایت فعلی</label>
                                <input
                                    type="url"
                                    wire:model="currentWebsiteUrl"
                                    placeholder="https://example.com"
                                    dir="ltr"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                                >
                                <p class="text-xs text-snow/40 leading-6">
                                    اگر سایت داری، آدرسش را بنویس تا بررسی اولیه انجام دهیم.
                                </p>
                                @error('currentWebsiteUrl') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    {{-- Step 3 --}}
                    @if ($step === 3)
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <h2 class="text-2xl font-black">جزئیات پروژه</h2>
                                <p class="text-sm text-snow/55 leading-7">
                                    هرچه دقیق‌تر بنویسی، بهتر می‌توانیم مسیر، زمان و هزینه را تخمین بزنیم.
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-snow/70">درباره کسب‌وکارت بنویس *</label>
                                <textarea
                                    wire:model="businessDescription"
                                    rows="5"
                                    placeholder="کسب‌وکارت چه کاری انجام می‌دهد؟ مشتریان اصلی‌ات چه کسانی هستند؟ الان چه چالشی داری؟"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors resize-none leading-7"
                                ></textarea>
                                @error('businessDescription') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-snow/70">هدف اصلی پروژه چیست؟ *</label>
                                <textarea
                                    wire:model="projectGoals"
                                    rows="4"
                                    placeholder="مثلاً: فروش بیشتر، گرفتن لید باکیفیت، برندسازی، بهبود رتبه گوگل، معرفی خدمات جدید"
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors resize-none leading-7"
                                ></textarea>
                                @error('projectGoals') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-snow/70">رقبا یا نمونه‌های مورد علاقه</label>
                                    <textarea
                                        wire:model="competitors"
                                        rows="4"
                                        placeholder="اگر سایت یا برندی هست که دوست داری، نام یا لینک آن را بنویس."
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors resize-none leading-7"
                                    ></textarea>
                                    @error('competitors') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-snow/70">محتوا، لوگو یا فایل‌های موجود</label>
                                    <textarea
                                        wire:model="references"
                                        rows="4"
                                        placeholder="آیا لوگو، بریف، تصاویر یا محتوای آماده داری؟ توضیح بده."
                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors resize-none leading-7"
                                    ></textarea>
                                    @error('references') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-white/10">
                                <h3 class="text-lg font-black">زمان‌بندی مورد نظر</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    @foreach ($timelineOptions as $value => $label)
                                        <label class="cursor-pointer">
                                            <input
                                                type="radio"
                                                name="timeline"
                                                value="{{ $value }}"
                                                wire:model="timeline"
                                                class="hidden peer"
                                            >
                                            <div
                                                class="text-center text-xs md:text-sm border border-white/10 rounded-xl px-3 py-4 text-snow/60 peer-checked:border-sky peer-checked:bg-sky/10 peer-checked:text-sky transition-all"
                                            >
                                                {{ $label }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('timeline') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-4 pt-4 border-t border-white/10">
                                <h3 class="text-lg font-black">بودجه تقریبی</h3>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    @foreach ($budgetOptions as $value => $label)
                                        <label class="cursor-pointer">
                                            <input
                                                type="radio"
                                                name="budget"
                                                value="{{ $value }}"
                                                wire:model="budget"
                                                class="hidden peer"
                                            >
                                            <div
                                                class="text-center text-xs md:text-sm border border-white/10 rounded-xl px-3 py-4 text-snow/60 peer-checked:border-sky peer-checked:bg-sky/10 peer-checked:text-sky transition-all"
                                            >
                                                {{ $label }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                @error('budget') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2 pt-4 border-t border-white/10">
                                <label class="text-sm font-bold text-snow/70">توضیحات تکمیلی</label>
                                <textarea
                                    wire:model="message"
                                    rows="4"
                                    placeholder="هر نکته دیگری که فکر می‌کنی مهم است را اینجا بنویس..."
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3.5 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors resize-none leading-7"
                                ></textarea>
                                @error('message') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-3 pt-4 border-t border-white/10">
                                <label class="text-sm font-bold text-snow/70">آپلود فایل‌های پروژه</label>
                                <p class="text-xs text-snow/45 leading-6">
                                    می‌توانی لوگو، بریف، تصاویر مرجع، فایل محتوایی یا压缩包 مرتبط را آپلود کنی. حداکثر حجم هر فایل: ۱۰ مگابایت.
                                </p>

                                <input
                                    type="file"
                                    wire:model="attachments"
                                    multiple
                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm file:ml-3 file:rounded-lg file:border-0 file:bg-sky/15 file:px-3 file:py-2 file:text-sky file:font-bold"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp,.zip,.rar"
                                >

                                @error('attachments') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                                @error('attachments.*') <span class="text-xs text-red-400">{{ $message }}</span> @enderror

                                @if (! empty($attachments))
                                    <div class="grid gap-3">
                                        @foreach ($attachments as $index => $file)
                                            <div wire:key="attachment-{{ $index }}" class="rounded-xl border border-white/10 bg-white/5 p-3 flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    @if (str_starts_with((string) $file->getMimeType(), 'image/'))
                                                        <img src="{{ $file->temporaryUrl() }}" alt="{{ $file->getClientOriginalName() }}" class="w-12 h-12 rounded-lg object-cover border border-white/10">
                                                    @else
                                                        <div class="w-12 h-12 rounded-lg grid place-items-center bg-sky/10 text-sky border border-sky/20">
                                                            <i class="fa-solid fa-file"></i>
                                                        </div>
                                                    @endif

                                                    <div class="min-w-0">
                                                        <div class="text-sm font-bold truncate">{{ $file->getClientOriginalName() }}</div>
                                                        <div class="text-xs text-snow/45" dir="ltr">
                                                            {{ round($file->getSize() / 1024, 1) }} KB
                                                        </div>
                                                    </div>
                                                </div>

                                                <button
                                                    type="button"
                                                    wire:click="removeAttachment({{ $index }})"
                                                    class="text-rose-300 hover:text-rose-200 transition-colors"
                                                    title="حذف فایل"
                                                >
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Step 4 --}}
                    @if ($step === 4)
                        @php
                            $serviceLabel = $serviceOptions[$service] ?? $service;
                            $websiteLabel = $websiteOptions[$currentWebsite] ?? $currentWebsite;
                            $timelineLabel = $timelineOptions[$timeline] ?? $timeline;
                            $budgetLabel = $budgetOptions[$budget] ?? $budget;
                        @endphp

                        <div class="space-y-6">
                            <div class="space-y-2">
                                <h2 class="text-2xl font-black">تأیید نهایی</h2>
                                <p class="text-sm text-snow/55 leading-7">
                                    یک نگاه نهایی بنداز. اگر چیزی لازم داشت، با دکمه مرحله قبل برگرد.
                                </p>
                            </div>

                            <div class="grid md:grid-cols-2 gap-5">
                                <div class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-3">
                                    <h3 class="text-base font-black text-sky">اطلاعات تماس</h3>

                                    <div class="space-y-2 text-sm">
                                        <p><span class="text-snow/45">نام:</span> {{ $name }}</p>
                                        <p><span class="text-snow/45">تلفن:</span> <span dir="ltr">{{ $phone }}</span></p>
                                        <p><span class="text-snow/45">ایمیل:</span> <span dir="ltr">{{ $email }}</span></p>
                                        @if ($company)
                                            <p><span class="text-snow/45">شرکت:</span> {{ $company }}</p>
                                        @endif
                                        @if ($role)
                                            <p><span class="text-snow/45">سمت:</span> {{ $role }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-3">
                                    <h3 class="text-base font-black text-sky">خدمت و وضعیت فعلی</h3>

                                    <div class="space-y-2 text-sm">
                                        <p><span class="text-snow/45">خدمت:</span> {{ $serviceLabel }}</p>
                                        <p><span class="text-snow/45">وبسایت فعلی:</span> {{ $websiteLabel }}</p>
                                        @if ($currentWebsiteUrl)
                                            <p><span class="text-snow/45">آدرس سایت:</span> <span dir="ltr">{{ $currentWebsiteUrl }}</span></p>
                                        @endif
                                    </div>
                                </div>

                                <div class="rounded-2xl bg-white/5 border border-white/10 p-5 space-y-3 md:col-span-2">
                                    <h3 class="text-base font-black text-sky">جزئیات پروژه</h3>

                                    <div class="space-y-3 text-sm leading-7">
                                        <p>
                                            <span class="text-snow/45">کسب‌وکار:</span><br>
                                            {{ $businessDescription }}
                                        </p>

                                        <p>
                                            <span class="text-snow/45">هدف پروژه:</span><br>
                                            {{ $projectGoals }}
                                        </p>

                                        @if ($competitors)
                                            <p>
                                                <span class="text-snow/45">رقبا / نمونه‌ها:</span><br>
                                                {{ $competitors }}
                                            </p>
                                        @endif

                                        @if ($references)
                                            <p>
                                                <span class="text-snow/45">فایل‌ها / محتوا:</span><br>
                                                {{ $references }}
                                            </p>
                                        @endif

                                        <p><span class="text-snow/45">زمان‌بندی:</span> {{ $timelineLabel }}</p>
                                        <p><span class="text-snow/45">بودجه:</span> {{ $budgetLabel }}</p>

                                        @if ($message)
                                            <p>
                                                <span class="text-snow/45">توضیحات تکمیلی:</span><br>
                                                {{ $message }}
                                            </p>
                                        @endif

                                        @if (! empty($attachments))
                                            <p>
                                                <span class="text-snow/45">فایل‌های ضمیمه:</span><br>
                                                <span dir="ltr">{{ collect($attachments)->pluck('originalName')->implode('، ') }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-amber-400/10 border border-amber-400/20 p-5 text-sm text-amber-200 leading-7">
                                بعد از ارسال، یک <strong>کد پیگیری</strong> دریافت می‌کنی. با آن کد می‌توانی وضعیت درخواستت را در سایت دنبال کنی.
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-col-reverse md:flex-row items-stretch md:items-center justify-between gap-4 pt-6 border-t border-white/10">
                        <button
                            type="button"
                            wire:click="prevStep"
                            @disabled($step === 1)
                            class="btn btn-primary {{ $step === 1 ? 'opacity-40 pointer-events-none' : '' }}"
                        >
                            <i class="fa-solid fa-arrow-right"></i>
                            مرحله قبل
                        </button>

                        <div class="flex flex-col md:flex-row gap-3">
                            @if ($step < 4)
                                <button type="button" wire:click="nextStep" class="btn btn-primary">
                                    مرحله بعد
                                    <i class="fa-solid fa-arrow-left"></i>
                                </button>
                            @else
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        ارسال درخواست پروژه
                                        <i class="fa-solid fa-paper-plane"></i>
                                    </span>
                                    <span wire:loading>
                                        در حال ارسال...
                                    </span>
                                </button>
                            @endif
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="rounded-2xl bg-red-500/10 border border-red-500/25 p-4 text-sm text-red-300 leading-7">
                            <strong>لطفاً این موارد را اصلاح کن:</strong>
                            <ul class="mt-2 space-y-1 list-disc pr-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            </div>

            <div class="mt-10 glass p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-5">
                <div class="space-y-2">
                    <h3 class="text-lg font-black">عجله داری؟</h3>
                    <p class="text-sm text-snow/55 leading-7">
                        می‌توانی مستقیم در تلگرام پیام بدهی؛ ما سریع‌تر پاسخ می‌دهیم.
                    </p>
                </div>

                @if ($telegram)
                    <a href="{{ $telegram }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                        <i class="fa-brands fa-telegram"></i>
                        تلگرام {{ $siteName }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>