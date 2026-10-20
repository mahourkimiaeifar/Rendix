<?php

use App\Models\Lead;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')]
    class extends Component {
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $service = '';

    public string $date_from_jalali = '';
    public string $date_to_jalali = '';

    public string $sort = 'created_at';
    public string $direction = 'desc';

    protected array $sortableColumns = [
        'name',
        'status',
        'service',
        'priority',
        'created_at',
    ];

    public function setJalaliToday(string $property): void
    {
        $this->{$property} = Jalali::today();
        $this->resetPage();
    }

    public function clearJalali(string $property): void
    {
        $this->{$property} = '';
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingService(): void
    {
        $this->resetPage();
    }

    public function updatedDateFromJalali(): void
    {
        $this->resetPage();
    }

    public function updatedDateToJalali(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if (!in_array($column, $this->sortableColumns, true)) {
            return;
        }

        if ($this->sort === $column) {
            $this->direction = $this->direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort = $column;
            $this->direction = 'desc';
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'status',
            'service',
            'date_from_jalali',
            'date_to_jalali',
        ]);

        $this->sort = 'created_at';
        $this->direction = 'desc';

        $this->resetPage();
    }

    public function fetchLeads()
    {
        $sort = in_array($this->sort, $this->sortableColumns, true)
            ? $this->sort
            : 'created_at';

        $direction = in_array($this->direction, ['asc', 'desc'], true)
            ? $this->direction
            : 'desc';

        $from = Jalali::parse($this->date_from_jalali);
        $to = Jalali::parse($this->date_to_jalali);

        return Lead::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($this->status !== '', fn($query) => $query->where('status', $this->status))
            ->when($this->service !== '', fn($query) => $query->where('service', $this->service))
            ->when($from, fn($query) => $query->where('created_at', '>=', $from->copy()->startOfDay()))
            ->when($to, fn($query) => $query->where('created_at', '<=', $to->copy()->endOfDay()))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();
    }
};

?>

@php
    $leads = $this->fetchLeads();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>سرنخ‌ها</h1>
            <p>همه درخواست‌های فرم تماس، با فیلتر جلالی، وضعیت و خدمت.</p>
        </div>

        <div class="admin-header-actions">
            <button type="button" wire:click="resetFilters" class="admin-btn admin-btn-ghost">
                <i class="fa-solid fa-rotate-left"></i>
                پاک کردن فیلترها
            </button>
        </div>
    </div>

    <div class="admin-filter-panel">
        <div class="admin-filter-grid">
            <div class="admin-field">
                <label class="admin-label">جستجو</label>
                <input type="text" wire:model.live.debounce.400ms="search" class="admin-input"
                    placeholder="نام، تلفن، ایمیل یا متن پیام...">
            </div>

            <x-admin.choice-pills name="status" label="وضعیت" variant="filter"
                :options="\App\Models\Lead::statusOptions()" :value="$status" placeholder="همه" />
            <x-admin.choice-pills name="service" label="خدمت" variant="filter"
                :options="\App\Models\Lead::serviceOptions()" :value="$service" placeholder="همه" />
            <x-admin.jalali-input name="date_from_jalali" label="از تاریخ" :value="$date_from_jalali" />

            <x-admin.jalali-input name="date_to_jalali" label="تا تاریخ" :value="$date_to_jalali" />
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>فهرست سرنخ‌ها</h2>
            <span class="admin-count">
                {{ \App\Support\Jalali::toPersianDigits(number_format($leads->total())) }} مورد
            </span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" class="sortable">مشتری</th>
                        <th wire:click="sortBy('service')" class="sortable">خدمت</th>
                        <th wire:click="sortBy('status')" class="sortable">وضعیت</th>
                        <th wire:click="sortBy('priority')" class="sortable">اولویت</th>
                        <th wire:click="sortBy('created_at')" class="sortable">ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $lead)
                        <tr>
                            <td>
                                <div class="admin-table-user">
                                    <span class="admin-avatar small">
                                        {{ mb_substr($lead->name, 0, 1) }}
                                    </span>
                                    <div>
                                        <strong>{{ $lead->name }}</strong>
                                        <small dir="ltr">{{ $lead->phone }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $lead->serviceLabel() }}</td>
                            <td>
                                <span class="admin-badge status-{{ $lead->status }}">
                                    {{ $lead->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge priority-{{ $lead->priority }}">
                                    {{ $lead->priorityLabel() }}
                                </span>
                            </td>
                            <td>
                                <x-admin.time :value="$lead->created_at" />
                            </td>
                            <td>
                                <div class="admin-row-actions">
                                    <a href="{{ route('admin.leads.show', ['id' => $lead->id]) }}" class="admin-icon-btn"
                                        title="مشاهده">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.leads.edit', ['id' => $lead->id]) }}" class="admin-icon-btn"
                                        title="ویرایش">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="admin-empty">
                                سرنخی با این فیلترها پیدا نشد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $leads->links() }}
        </div>
    </div>
</div>