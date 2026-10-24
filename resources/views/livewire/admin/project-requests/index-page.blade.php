<?php

use App\Models\ProjectRequest;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
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

    public function fetchRequests()
    {
        return ProjectRequest::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('tracking_code', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('service', 'like', "%{$search}%");
                });
            })
            ->when($this->status !== '', fn ($query) => $query->where('status', $this->status))
            ->ordered()
            ->paginate(15)
            ->withQueryString();
    }
};

?>

@php
    $requests = $this->fetchRequests();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>درخواست‌های پروژه</h1>
            <p>درخواست‌های چندمرحله‌ای وبسایت را بررسی، پیگیری و وضعیت‌دهی کن.</p>
        </div>

        <div class="admin-header-actions">
            <a href="{{ route('request-project') }}" target="_blank" rel="noopener noreferrer" class="admin-btn admin-btn-ghost">
                <i class="fa-solid fa-up-right-from-square"></i>
                فرم عمومی
            </a>
        </div>
    </div>

    <div class="admin-filter-panel">
        <div class="admin-filter-grid">
            <div class="admin-field">
                <label class="admin-label">جستجو</label>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="admin-input"
                    placeholder="کد پیگیری، نام، تلفن، ایمیل، شرکت یا خدمت..."
                >
            </div>

            <x-admin.choice-pills
                name="status"
                label="وضعیت"
                variant="filter"
                :options="\App\Models\ProjectRequest::statusOptions()"
                :value="$status"
                placeholder="همه"
            />
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>فهرست درخواست‌ها</h2>
            <span class="admin-count">
                {{ Jalali::toPersianDigits(number_format($requests->total())) }} درخواست
            </span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>کد پیگیری</th>
                        <th>مشتری</th>
                        <th>خدمت</th>
                        <th>بودجه</th>
                        <th>زمان</th>
                        <th>وضعیت</th>
                        <th>تاریخ</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $request)
                        <tr>
                            <td dir="ltr" class="font-black text-sky">
                                {{ $request->tracking_code }}
                            </td>
                            <td>
                                <div class="admin-table-user">
                                    <span class="admin-avatar small">
                                        {{ mb_substr($request->name, 0, 1) }}
                                    </span>

                                    <div>
                                        <strong>{{ $request->name }}</strong>
                                        <small dir="ltr">{{ $request->phone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $request->service }}</td>
                            <td>{{ $request->budgetLabel() }}</td>
                            <td>{{ $request->timelineLabel() }}</td>
                            <td>
                                <span class="admin-badge {{ $request->status === 'delivered' ? 'status-won' : ($request->status === 'cancelled' || $request->status === 'archived' ? 'status-archived' : 'priority-normal') }}">
                                    {{ $request->statusLabel() }}
                                </span>
                            </td>
                            <td>{{ Jalali::date($request->created_at) }}</td>
                            <td>
                                <div class="admin-row-actions">
                                    <a
                                        href="{{ route('admin.project-requests.show', ['id' => $request->id]) }}"
                                        class="admin-icon-btn"
                                        title="مشاهده و پیگیری"
                                    >
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a
                                        href="{{ route('request-project.show', $request->tracking_code) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="admin-icon-btn"
                                        title="مشاهده صفحه پیگیری"
                                    >
                                        <i class="fa-solid fa-up-right-from-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="admin-empty">
                                هنوز درخواست پروژه‌ای ثبت نشده است.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $requests->links() }}
        </div>
    </div>
</div>