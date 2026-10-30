<?php

use App\Models\ProjectRequest;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.customer')]
    class extends Component {
    use WithPagination;

    #[Url]
    public string $status = '';

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function fetchRequests()
    {
        $user = auth()->user();

        return ProjectRequest::query()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere(function ($q) use ($user) {
                        $q->whereNull('user_id')->where('email', $user->email);
                    });
            })
            ->when($this->status !== '', fn($q) => $q->where('status', $this->status))
            ->ordered()
            ->paginate(8)
            ->withQueryString();
    }
};

?>

@php
    $requests = $this->fetchRequests();
    $filters = ['' => 'همه'] + ProjectRequest::statusOptions();

    $statusClass = function (string $status): string {
        return match (true) {
            $status === 'delivered' => 'st-done',
            in_array($status, ['cancelled', 'archived'], true) => 'st-cancel',
            in_array($status, ['waiting_client', 'proposal_sent'], true) => 'st-wait',
            $status === 'new' => 'st-new',
            default => 'st-progress',
        };
    };
@endphp

@section('cust.title', 'درخواست‌های من')
@section('cust.icon', 'fa-solid fa-clipboard-list')
<div>
    <div class="rx-filter-row" style="justify-content:flex-start;margin-bottom:18px;">
        @foreach ($filters as $value => $label)
            <a href="{{ route('customer.requests', ['status' => $value]) }}"
                class="rx-filter-pill {{ $status === $value ? 'is-active' : '' }}" wire:navigate>
                {{ $label }}
            </a>
        @endforeach
    </div>

    @if ($requests->count() === 0)
        <x-site.empty-state icon="fa-solid fa-folder-open" title="درخواستی با این فیلتر نیست"
            text="فیلتر را عوض کن یا یک درخواست جدید ثبت کن.">
            <a href="{{ route('request-project') }}" class="btn btn-primary btn-sm" style="margin-top:14px;" wire:navigate>
                <i class="fa-solid fa-rocket"></i>
                <span>درخواست جدید</span>
            </a>
        </x-site.empty-state>
    @else
        <div class="rx-cust-card" style="padding:18px;display:grid;gap:10px;">
            @foreach ($requests as $request)
                <div class="rx-cust-row">
                    <div style="display:grid;gap:4px;min-width:0;">
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <span class="rx-cust-code" dir="ltr">{{ $request->tracking_code }}</span>
                            <span class="st-pill {{ $statusClass($request->status) }}">{{ $request->statusLabel() }}</span>
                        </div>
                        <span class="rx-cust-muted">{{ $request->service }} — {{ Jalali::date($request->created_at) }}</span>
                    </div>

                    <div style="display:flex;align-items:center;gap:9px;flex-wrap:wrap;">
                        <button type="button" class="btn btn-ghost btn-sm btn-icon" title="کپی کد پیگیری"
                            onclick="navigator.clipboard.writeText('{{ $request->tracking_code }}').then(function(){ window.rxToast && rxToast({ type:'success', message:'کد پیگیری کپی شد.' }); })">
                            <i class="fa-solid fa-copy"></i>
                        </button>

                        <a href="{{ route('customer.requests.show', $request->tracking_code) }}" class="btn btn-primary btn-sm"
                            wire:navigate>
                            جزئیات
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="rx-public-pagination">
            {{ $requests->links() }}
        </div>
    @endif
</div>