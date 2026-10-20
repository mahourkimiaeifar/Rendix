<?php

use App\Models\Lead;
use App\Services\ActivityLogger;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')]
    class extends Component {
    #[Url(as: 'id')]
    public ?int $leadId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $service = '';

    public string $message = '';

    public string $status = 'new';

    public string $priority = 'normal';

    public string $nextFollowUpJalali = '';

    public string $lastContactJalali = '';

    public bool $isArchived = false;

    public function mount(): void
    {
        $this->syncFromLead();
    }

    public function updatedLeadId(): void
    {
        $this->syncFromLead();
    }

    public function lead(): ?Lead
    {
        if (!$this->leadId) {
            return null;
        }

        return Lead::query()->find($this->leadId);
    }

    protected function syncFromLead(): void
    {
        $lead = $this->lead();

        if (!$lead) {
            return;
        }

        $this->name = $lead->name;
        $this->email = $lead->email ?? '';
        $this->phone = $lead->phone;
        $this->service = $lead->service;
        $this->message = $lead->message;
        $this->status = $lead->status;
        $this->priority = $lead->priority;
        $this->isArchived = (bool) $lead->is_archived;

        $this->nextFollowUpJalali = Jalali::date($lead->next_follow_up_at);
        $this->lastContactJalali = Jalali::date($lead->last_contact_at);
    }

    public function setJalaliToday(string $property): void
    {
        $this->{$property} = Jalali::today();
    }

    public function clearJalali(string $property): void
    {
        $this->{$property} = '';
    }

    public function save()
    {
        $lead = $this->lead();

        if (!$lead) {
            $this->dispatch('rx-toast', [
                'type' => 'error',
                'message' => 'سرنخ مورد نظر پیدا نشد.',
            ]);

            return redirect()->route('admin.leads.index');
        }

        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:190'],
            'phone' => ['required', 'string', 'max:30'],
            'service' => ['required', 'string'],
            'message' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Lead::statusOptions()))],
            'priority' => ['required', 'string', 'in:' . implode(',', array_keys(Lead::priorityOptions()))],
            'nextFollowUpJalali' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (trim((string) $value) !== '' && !Jalali::parse($value)) {
                        $fail('تاریخ پیگیری بعدی جلالی معتبر نیست.');
                    }
                },
            ],
            'lastContactJalali' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (trim((string) $value) !== '' && !Jalali::parse($value)) {
                        $fail('تاریخ آخرین تماس جلالی معتبر نیست.');
                    }
                },
            ],
            'isArchived' => ['boolean'],
        ], [
            'name.required' => 'نام را وارد کن.',
            'email.email' => 'ایمیل معتبر نیست.',
            'phone.required' => 'تلفن را وارد کن.',
            'service.required' => 'خدمت مورد نظر را انتخاب کن.',
            'message.required' => 'پیام را وارد کن.',
            'status.in' => 'وضعیت انتخابی معتبر نیست.',
            'priority.in' => 'اولویت انتخابی معتبر نیست.',
        ]);

        $lead->name = trim($this->name);
        $lead->email = trim($this->email) !== '' ? trim($this->email) : null;
        $lead->phone = trim($this->phone);
        $lead->service = $this->service;
        $lead->message = trim($this->message);
        $lead->status = $this->status;
        $lead->priority = $this->priority;
        $lead->is_archived = $this->isArchived;

        $lead->next_follow_up_at = Jalali::parse($this->nextFollowUpJalali);
        $lead->last_contact_at = Jalali::parse($this->lastContactJalali);

        $lead->save();

        ActivityLogger::log('lead.updated', $lead);

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'سرنخ با موفقیت به‌روزرسانی شد.',
        ]);

        return redirect()->route('admin.leads.show', ['id' => $lead->id]);
    }
};

?>

@php
    $lead = $this->lead();
@endphp

@if (!$lead)
    <div class="admin-page">
        <div class="admin-panel rx-lead-empty">
            <i class="fa-solid fa-satellite-dish"></i>
            <h2>سرنخی برای ویرایش پیدا نشد</h2>
            <p>ممکن است این سرنخ حذف شده باشد یا لینک درست نباشد.</p>

            <a href="{{ route('admin.leads.index') }}" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به سرنخ‌ها
            </a>
        </div>
    </div>
@else
    <div class="admin-page">
        <div class="admin-page-header">
            <div>
                <a href="{{ route('admin.leads.show', ['id' => $lead->id]) }}" class="rx-back-link">
                    <i class="fa-solid fa-arrow-right"></i>
                    بازگشت به نمایش سرنخ
                </a>

                <h1>ویرایش سرنخ</h1>
                <p>اطلاعات مشتری، وضعیت، اولویت و زمان پیگیری را مدیریت کن.</p>
            </div>
        </div>

        <form wire:submit="save" class="rx-edit-form-grid">
            <div class="admin-panel">
                <div class="admin-panel-header">
                    <h2>اطلاعات اصلی</h2>
                </div>

                <div class="rx-edit-form-inner">
                    <x-admin.form-errors />

                    <div class="admin-field">
                        <label class="admin-label">نام مشتری</label>
                        <input type="text" wire:model.live="name" class="admin-input" required>
                        @error('name')
                        <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field">
                        <label class="admin-label">تلفن</label>
                        <input type="text" wire:model.live="phone" class="admin-input" dir="ltr" required>
                        @error('phone')
                        <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field">
                        <label class="admin-label">ایمیل</label>
                        <input type="email" wire:model.live="email" class="admin-input" dir="ltr">
                        @error('email')
                        <p class="admin-error">{{ $message }}</p> @enderror
                    </div>

                    <x-admin.choice-pills name="service" label="خدمت مورد نظر" :options="\App\Models\Lead::serviceOptions()"
                        :value="$service" required />

                    <div class="admin-field">
                        <label class="admin-label">پیام مشتری</label>
                        <textarea wire:model.live="message" class="admin-input rx-textarea" rows="6" required></textarea>
                        @error('message')
                        <p class="admin-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="admin-panel">
                <div class="admin-panel-header">
                    <h2>مدیریت فروش</h2>
                </div>

                <div class="rx-edit-form-inner">
                    <x-admin.choice-pills name="status" label="وضعیت" :options="\App\Models\Lead::statusOptions()"
                        :value="$status" />

                    <x-admin.choice-pills name="priority" label="اولویت" :options="\App\Models\Lead::priorityOptions()"
                        :value="$priority" />

                    <x-admin.jalali-input name="nextFollowUpJalali" label="زمان پیگیری بعدی" :value="$nextFollowUpJalali"
                        help="مثال: ۱۴۰۴/۰۷/۲۶ یا ۱۴۰۴/۰۷/۲۶ ۱۴:۳۰" />

                    <x-admin.jalali-input name="lastContactJalali" label="آخرین تماس" :value="$lastContactJalali"
                        help="اگر خالی بگذاری، پاک می‌شود." />

                    <label class="admin-checkbox rx-archive-check">
                        <input type="checkbox" wire:model.live="isArchived">
                        <span>این سرنخ بایگانی شود</span>
                    </label>

                    <div class="rx-edit-actions">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <span wire:loading.remove wire:target="save">
                                <i class="fa-solid fa-floppy-disk"></i>
                                ذخیره تغییرات
                            </span>
                            <span wire:loading wire:target="save">
                                در حال ذخیره...
                            </span>
                        </button>

                        <a href="{{ route('admin.leads.show', ['id' => $lead->id]) }}" class="admin-btn admin-btn-ghost">
                            انصراف
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endif