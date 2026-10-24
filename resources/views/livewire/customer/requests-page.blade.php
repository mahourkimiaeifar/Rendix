<?php

use App\Models\ProjectRequest;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function requestsQuery()
    {
        $user = auth()->user();

        return ProjectRequest::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($q) use ($user) {
                          $q->whereNull('user_id')
                            ->where('email', $user->email);
                      });
            });
    }

    public function fetchRequests()
    {
        return $this->requestsQuery()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('tracking_code', 'like', "%{$search}%")
                      ->orWhere('service', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('business_description', 'like', "%{$search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->ordered()
            ->paginate(10)
            ->withQueryString();
    }
};

?>

@php
    $requests = $this->fetchRequests();
    $siteName = setting('site.name', 'رندیکس');
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-7xl mx-auto">

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-10">
                <div class="space-y-3">
                    <div class="badge">
                        <span class="badge-dot"></span>
                        درخواست‌های پروژه
                    </div>

                    <h1 class="text-3xl md:text-5xl font-black leading-[1.25]">
                        همه درخواست‌های {{ $siteName }}
                    </h1>
                </div>

                <a href="{{ route('request-project') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i>
                    درخواست جدید
                </a>
            </div>

            <div class="glass p-5 md:p-6 mb-8 grid md:grid-cols-[1fr_auto] gap-4">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    placeholder="جستجو در کد پیگیری، خدمت، شرکت یا توضیحات..."
                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm placeholder:text-snow/25 focus:outline-none focus:border-sky/60 transition-colors"
                >

                <select
                    wire:model.live="status"
                    class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-sm text-snow/70 focus:outline-none focus:border-sky/60 transition-colors"
                >
                    <option value="">همه وضعیت‌ها</option>
                    @foreach (\App\Models\ProjectRequest::statusOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @if ($requests->isEmpty())
                <div class="glass p-7 md:p-10 text-center space-y-5">
                    <div class="text-5xl text-sky/60">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>

                    <h2 class="text-2xl font-black">درخواستی پیدا نشد</h2>

                    <p class="text-sm text-snow/55 leading-7">
                        با این فیلترها درخواستی وجود ندارد.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($requests as $request)
                        <a
                            href="{{ route('customer.requests.show', $request->tracking_code) }}"
                            class="block glass p-6 hover:border-sky/30 transition-all space-y-4"
                        >
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <strong dir="ltr" class="text-sky font-black text-lg">
                                            {{ $request->tracking_code }}
                                        </strong>

                                        <span class="text-[11px] font-black rounded-full px-3 py-1 border
                                            {{ $request->status === 'delivered' ? 'text-emerald-300 border-emerald-400/25 bg-emerald-400/10' : ($request->status === 'cancelled' || $request->status === 'archived' ? 'text-rose-300 border-rose-400/25 bg-rose-400/10' : 'text-sky border-sky/25 bg-sky/10') }}">
                                            {{ $request->statusLabel() }}
                                        </span>
                                    </div>

                                    <div class="text-sm text-snow/65 leading-7">
                                        {{ $request->service }}
                                        @if ($request->company)
                                            — {{ $request->company }}
                                        @endif
                                    </div>
                                </div>

                                <div class="text-xs text-snow/45 font-bold">
                                    {{ Jalali::date($request->created_at) }}
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-3 gap-3 text-xs">
                                <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                                    <span class="text-snow/45">بودجه:</span>
                                    <strong class="text-snow/75">{{ $request->budgetLabel() }}</strong>
                                </div>

                                <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                                    <span class="text-snow/45">زمان:</span>
                                    <strong class="text-snow/75">{{ $request->timelineLabel() }}</strong>
                                </div>

                                <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                                    <span class="text-snow/45">سایت فعلی:</span>
                                    <strong class="text-snow/75">{{ $request->websiteStatusLabel() }}</strong>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10 flex justify-center">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </section>
</div>