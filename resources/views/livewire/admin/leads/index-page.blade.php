<?php

use App\Livewire\Concerns\HandlesJalaliInputs;
use App\Models\Lead;
use App\Support\Jalali;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

#[Layout('components.layouts.admin')]
new class extends Component {
    use WithPagination;
    use HandlesJalaliInputs;

    public string $search = '';
    public string $status = '';
    public string $service = '';

    public string $date_from_jalali = '';
    public string $date_to_jalali = '';

    public ?Carbon $date_from = null;
    public ?Carbon $date_to = null;

    public string $sort = 'created_at';
    public string $direction = 'desc';

    public function mount(): void
    {
        $this->syncAllJalaliProperties();
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

    public function sortBy(string $column): void
    {
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
        $this->reset(['search', 'status', 'service', 'date_from_jalali', 'date_to_jalali']);
        $this->date_from = null;
        $this->date_to = null;
        $this->resetPage();
    }

    public function getLeadsProperty()
    {
        return Lead::query()
            ->when($this->search, function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->when($this->service, fn ($query) => $query->where('service', $this->service))
            ->when($this->date_from, fn ($query) => $query->where('created_at', '>=', $this->date_from->copy()->startOfDay()))
            ->when($this->date_to, fn ($query) => $query->where('created_at', '<=', $this->date_to->copy()->endOfDay()))
            ->orderBy($this->sort, $this->direction)
            ->paginate(15)
            ->withQueryString();
    }
};

?>

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
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="admin-input"
                    placeholder="نام، تلفن، ایمیل یا متن پیام..."
                >
            </div>

            <div class="admin-field">
                <label class="admin-label">وضعیت</label>
                <select wire:model.live="status" class="admin-input">
                    <option value="">همه وضعیت‌ها</option>
                    @foreach (Lead::statusOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="admin-field">
                <label class="admin-label">خدمت</label>
                <select wire:model.live="service" class="admin-input">
                    <option value="">همه خدمات</option>
                    @foreach (Lead::serviceOptions() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <x-admin.jalali-input
                name="date_from_jalali"
                label="از تاریخ"
                :value="$date_from_jalali"
            />

            <x-admin.jalali-input
                name="date_to_jalali"
                label="تا تاریخ"
                :value="$date_to_jalali"
            />
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>فهرست سرنخ‌ها</h2>
            <span class="admin-count">{{ $leads->total() }} مورد</span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th wire:click="sortBy('name')" class="sortable">مشتری</th>
                        <th>خدمت</th>
                        <th wire:click="sortBy('status')" class="sortable">وضعیت</th>
                        <th>اولویت</th>
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
                            <td>{{ Jalali::dateTime($lead->created_at) }}</td>
                            <td>
                                <div class="admin-row-actions">
                                    <a href="#" class="admin-icon-btn" title="مشاهده">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="#" class="admin-icon-btn" title="ویرایش">
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