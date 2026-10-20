<?php

use App\Models\Faq;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    public string $search = '';

    public ?int $selectedId = null;

    public string $question = '';
    public string $answer = '';
    public string $category = 'general';
    public string $location = 'all';
    public string $sort_order = '0';
    public bool $is_active = true;

    public function mount(): void
    {
        $this->resetForm();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedId(): void
    {
        $this->syncFromSelection();
    }

    public function resetForm(): void
    {
        $this->selectedId = null;
        $this->question = '';
        $this->answer = '';
        $this->category = 'general';
        $this->location = 'all';
        $this->sort_order = '0';
        $this->is_active = true;
    }

    public function newFaq(): void
    {
        $this->resetForm();
    }

    public function editFaq(int $id): void
    {
        $this->selectedId = $id;
        $this->syncFromSelection();
    }

    protected function syncFromSelection(): void
    {
        if (! $this->selectedId) {
            return;
        }

        $faq = Faq::query()->find($this->selectedId);

        if (! $faq) {
            $this->resetForm();

            return;
        }

        $this->question = $faq->question;
        $this->answer = $faq->answer;
        $this->category = $faq->category ?? 'general';
        $this->location = $faq->location ?? 'all';
        $this->sort_order = (string) $faq->sort_order;
        $this->is_active = $faq->is_active;
    }

    public function deleteFaq(int $id): void
    {
        $faq = Faq::query()->find($id);

        if (! $faq) {
            return;
        }

        $faq->delete();

        if ($this->selectedId === $id) {
            $this->resetForm();
        }

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'سوال حذف شد.',
        ]);
    }

    public function toggleActive(int $id): void
    {
        $faq = Faq::query()->find($id);

        if (! $faq) {
            return;
        }

        $faq->is_active = ! $faq->is_active;
        $faq->save();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $faq->is_active
                ? 'سوال فعال شد.'
                : 'سوال غیرفعال شد.',
        ]);
    }

    public function save()
    {
        $this->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'in:' . implode(',', array_keys(Faq::categoryOptions()))],
            'location' => ['required', 'string', 'in:' . implode(',', array_keys(Faq::locationOptions()))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], [
            'question.required' => 'سوال را وارد کن.',
            'answer.required' => 'پاسخ را بنویس.',
            'category.in' => 'دسته‌بندی معتبر نیست.',
            'location.in' => 'محل نمایش معتبر نیست.',
        ]);

        $data = [
            'question' => trim($this->question),
            'answer' => trim($this->answer),
            'category' => trim($this->category) ?: 'general',
            'location' => $this->location,
            'sort_order' => (int) ($this->sort_order ?: 0),
            'is_active' => $this->is_active,
        ];

        if ($this->selectedId) {
            $faq = Faq::query()->find($this->selectedId);

            if (! $faq) {
                $this->dispatch('rx-toast', [
                    'type' => 'error',
                    'message' => 'سوال مورد نظر پیدا نشد.',
                ]);

                return;
            }

            $faq->update($data);
        } else {
            Faq::create($data);
        }

        $this->resetForm();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'سوال متداول ذخیره شد.',
        ]);
    }

    public function fetchFaqs()
    {
        return Faq::query()
            ->when(trim($this->search) !== '', function ($query) {
                $search = trim($this->search);

                $query->where(function ($q) use ($search) {
                    $q->where('question', 'like', "%{$search}%")
                      ->orWhere('answer', 'like', "%{$search}%");
                });
            })
            ->ordered()
            ->paginate(10)
            ->withQueryString();
    }
};

?>

@php
    $faqs = $this->fetchFaqs();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>سوالات متداول</h1>
            <p>سوال‌های تکراری مشتری‌ها را پاسخ بده تا تبدیل بالا برود.</p>
        </div>

        <div class="admin-header-actions">
            <button type="button" wire:click="newFaq" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i>
                سوال جدید
            </button>
        </div>
    </div>

    <div class="rx-manager-grid">
        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>{{ $selectedId ? 'ویرایش سوال' : 'افزودن سوال' }}</h2>
            </div>

            <form wire:submit="save" class="rx-manager-form">
                <x-admin.form-errors />

                <div class="admin-field">
                    <label class="admin-label">سوال</label>
                    <input type="text" wire:model.live="question" class="admin-input" required>
                    @error('question') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <div class="admin-field">
                    <label class="admin-label">پاسخ</label>
                    <textarea wire:model.live="answer" class="admin-input rx-textarea" rows="6" required></textarea>
                    @error('answer') <p class="admin-error">{{ $message }}</p> @enderror
                </div>

                <x-admin.choice-pills
                    name="category"
                    label="دسته‌بندی"
                    :options="\App\Models\Faq::categoryOptions()"
                    :value="$category"
                />

                <x-admin.choice-pills
                    name="location"
                    label="محل نمایش"
                    :options="\App\Models\Faq::locationOptions()"
                    :value="$location"
                />

                <div class="rx-two-col">
                    <div class="admin-field">
                        <label class="admin-label">ترتیب نمایش</label>
                        <input type="number" wire:model.live="sort_order" class="admin-input" min="0" max="9999">
                        @error('sort_order') <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <label class="admin-checkbox rx-switch-card">
                        <input type="checkbox" wire:model.live="is_active">
                        <span>فعال باشد</span>
                    </label>
                </div>

                <div class="rx-edit-actions">
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <span wire:loading.remove wire:target="save">
                            <i class="fa-solid fa-floppy-disk"></i>
                            ذخیره
                        </span>
                        <span wire:loading wire:target="save">
                            در حال ذخیره...
                        </span>
                    </button>

                    <button type="button" wire:click="newFaq" class="admin-btn admin-btn-ghost">
                        فرم خالی
                    </button>
                </div>
            </form>
        </div>

        <div class="admin-panel">
            <div class="admin-panel-header">
                <h2>فهرست سوالات</h2>
                <span class="admin-count">
                    {{ Jalali::toPersianDigits(number_format($faqs->total())) }} سوال
                </span>
            </div>

            <div class="admin-field rx-manager-search">
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="admin-input"
                    placeholder="جستجو در سوال یا پاسخ..."
                >
            </div>

            <div class="rx-manager-list">
                @forelse ($faqs as $faq)
                    <div class="rx-manager-item {{ $selectedId === $faq->id ? 'is-selected' : '' }}">
                        <div class="rx-manager-item-main">
                            <div>
                                <strong>{{ $faq->question }}</strong>
                                <small>
                                    {{ $faq->categoryLabel() }}
                                    —
                                    {{ $faq->locationLabel() }}
                                </small>
                                <p>{{ \Illuminate\Support\Str::limit($faq->answer, 120) }}</p>
                            </div>
                        </div>

                        <div class="rx-manager-actions">
                            <button
                                type="button"
                                wire:click="toggleActive({{ $faq->id }})"
                                class="admin-badge {{ $faq->is_active ? 'status-won' : 'status-archived' }}"
                            >
                                {{ $faq->is_active ? 'فعال' : 'غیرفعال' }}
                            </button>

                            <button type="button" wire:click="editFaq({{ $faq->id }})" class="admin-icon-btn" title="ویرایش">
                                <i class="fa-solid fa-pen"></i>
                            </button>

                            <button
                                type="button"
                                wire:click="deleteFaq({{ $faq->id }})"
                                wire:confirm="مطمئنی این سوال حذف شود؟"
                                class="admin-icon-btn danger"
                                title="حذف"
                            >
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="admin-empty rx-inline-empty">
                        هنوز سوالی ثبت نشده است.
                    </div>
                @endforelse
            </div>

            <div class="admin-pagination">
                {{ $faqs->links() }}
            </div>
        </div>
    </div>
</div>