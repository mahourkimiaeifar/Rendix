<?php

use App\Models\Service;
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

    public function toggleActive(int $serviceId): void
    {
        $service = Service::query()->find($serviceId);

        if (! $service) {
            return;
        }

        $service->is_active = ! $service->is_active;
        $service->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $service->is_active
                ? 'خدمت فعال شد.'
                : 'خدمت غیرفعال شد.',
        ]);
    }

    public function fetchServices()
    {
        return Service::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->when($this->status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();
    }
};

?>

@php
    $services = $this->fetchServices();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>خدمات</h1>
            <p>خدمات رندیکس را از پنل مدیریت کن؛ صفحه اصلی و فرم تماس خودکار به‌روز می‌شوند.</p>
        </div>

        <div class="admin-header-actions">
            <a href="{{ route('admin.services.form') }}" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i>
                خدمت جدید
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
                    placeholder="نام خدمت، اسلاگ یا توضیح کوتاه..."
                >
            </div>

            <x-admin.choice-pills
                name="status"
                label="وضعیت"
                variant="filter"
                :options="['active' => 'فعال', 'inactive' => 'غیرفعال']"
                :value="$status"
                placeholder="همه"
            />
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>فهرست خدمات</h2>
            <span class="admin-count">
                {{ Jalali::toPersianDigits(number_format($services->total())) }} خدمت
            </span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>خدمت</th>
                        <th>اسلاگ</th>
                        <th>وضعیت</th>
                        <th>ویژه</th>
                        <th>ترتیب</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $service)
                        <tr>
                            <td>
                                <div class="admin-table-user">
                                    @if ($service->image_path)
                                        <img
                                            src="{{ $service->imageUrl() }}"
                                            alt="{{ $service->title }}"
                                            class="rx-service-thumb"
                                        >
                                    @else
                                        <span class="admin-avatar small">
                                            {{ mb_substr($service->title, 0, 1) }}
                                        </span>
                                    @endif

                                    <div>
                                        <strong>{{ $service->title }}</strong>
                                        <small>{{ $service->short_description ?: 'بدون توضیح کوتاه' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td dir="ltr">{{ $service->slug }}</td>
                            <td>
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $service->id }})"
                                    class="admin-badge {{ $service->is_active ? 'status-won' : 'status-archived' }}"
                                >
                                    {{ $service->is_active ? 'فعال' : 'غیرفعال' }}
                                </button>
                            </td>
                            <td>
                                <span class="admin-badge {{ $service->is_featured ? 'priority-high' : 'gray' }}">
                                    {{ $service->is_featured ? 'بله' : 'خیر' }}
                                </span>
                            </td>
                            <td>{{ Jalali::toPersianDigits((string) $service->sort_order) }}</td>
                            <td>
                                <div class="admin-row-actions">
                                    <a
                                        href="{{ route('admin.services.form', ['id' => $service->id]) }}"
                                        class="admin-icon-btn"
                                        title="ویرایش"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="admin-empty">
                                هنوز خدمتی ثبت نشده است. اولین خدمت را اضافه کن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $services->links() }}
        </div>
    </div>
</div>