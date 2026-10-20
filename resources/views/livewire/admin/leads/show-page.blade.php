<?php

use App\Models\Lead;
use App\Models\LeadNote;
use App\Services\ActivityLogger;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')]
    class extends Component {
    #[Url(as: 'id')]
    public ?int $leadId = null;

    public string $status = '';

    public string $priority = '';

    public string $noteType = 'note';

    public string $noteBody = '';

    public string $followUpJalali = '';

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

        return Lead::query()
            ->with(['notes.user'])
            ->find($this->leadId);
    }

    protected function syncFromLead(): void
    {
        $lead = $this->lead();

        if (!$lead) {
            return;
        }

        $this->status = (string) $lead->status;
        $this->priority = (string) $lead->priority;
        $this->followUpJalali = Jalali::date($lead->next_follow_up_at);
    }

    public function updatedStatus(string $value): void
    {
        $lead = $this->lead();

        if (!$lead) {
            return;
        }

        if (!array_key_exists($value, Lead::statusOptions())) {
            $this->status = (string) $lead->status;

            return;
        }

        if ($lead->status === $value) {
            return;
        }

        $oldStatus = $lead->status;

        $lead->status = $value;

        if (
            in_array($value, ['contacted', 'negotiation', 'proposal', 'won'], true)
            && !$lead->last_contact_at
        ) {
            $lead->last_contact_at = now();
        }

        $lead->save();

        ActivityLogger::log('lead.status_updated', $lead, [
            'from' => $oldStatus,
            'to' => $value,
        ]);

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'وضعیت سرنخ تغییر کرد.',
        ]);
    }

    public function updatedPriority(string $value): void
    {
        $lead = $this->lead();

        if (!$lead) {
            return;
        }

        if (!array_key_exists($value, Lead::priorityOptions())) {
            $this->priority = (string) $lead->priority;

            return;
        }

        if ($lead->priority === $value) {
            return;
        }

        $oldPriority = $lead->priority;

        $lead->priority = $value;
        $lead->save();

        ActivityLogger::log('lead.priority_updated', $lead, [
            'from' => $oldPriority,
            'to' => $value,
        ]);

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'اولویت سرنخ تغییر کرد.',
        ]);
    }

    public function setJalaliToday(string $property): void
    {
        $this->{$property} = Jalali::today();
    }

    public function clearJalali(string $property): void
    {
        $this->{$property} = '';
    }

    public function addNote(): void
    {
        $lead = $this->lead();

        if (!$lead) {
            return;
        }

        $this->validate([
            'noteBody' => ['required', 'string', 'min:2', 'max:2000'],
            'noteType' => [
                'required',
                'string',
                'in:' . implode(',', array_keys(LeadNote::typeOptions())),
            ],
        ], [
            'noteBody.required' => 'متن یادداشت را بنویس.',
            'noteBody.min' => 'متن یادداشت خیلی کوتاه است.',
            'noteBody.max' => 'متن یادداشت نمی‌تواند بیشتر از ۲۰۰ کاراکتر باشد.',
            'noteType.in' => 'نوع یادداشت معتبر نیست.',
        ]);

        LeadNote::create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => $this->noteType,
            'body' => trim($this->noteBody),
        ]);

        ActivityLogger::log('lead.note_added', $lead, [
            'type' => $this->noteType,
        ]);

        $this->reset('noteBody');

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'یادداشت داخلی ثبت شد.',
        ]);
    }

    public function saveFollowUp(): void
    {
        $lead = $this->lead();

        if (!$lead) {
            return;
        }

        $value = trim($this->followUpJalali);

        $carbon = null;

        if ($value !== '') {
            $carbon = Jalali::parse($value);

            if (!$carbon) {
                $this->addError('followUpJalali', 'تاریخ جلالی معتبر نیست. مثال: ۱۴۰/۰۷/۶ یا ۱۴۰۴/۰۷/۲۶ ۱۴:۳۰');

                return;
            }
        }

        $lead->next_follow_up_at = $carbon;
        $lead->save();

        ActivityLogger::log('lead.follow_up_updated', $lead, [
            'value' => $value,
        ]);

        $this->followUpJalali = Jalali::date($carbon);

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $carbon ? 'زمان پیگیری بعدی ثبت شد.' : 'زمان پیگیری بعدی پاک شد.',
        ]);
    }

    public function toggleArchive(): void
    {
        $lead = $this->lead();

        if (!$lead) {
            return;
        }

        $lead->is_archived = !$lead->is_archived;
        $lead->save();

        ActivityLogger::log(
            $lead->is_archived ? 'lead.archived' : 'lead.unarchived',
            $lead
        );

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $lead->is_archived ? 'سرنخ بایگانی شد.' : 'سرنخ از بایگانی خارج شد.',
        ]);
    }
};

?>

@php
    $lead = $this->lead();
    $metadata = $lead?->metadata ?? [];
@endphp

@if (!$lead)
    <div class="admin-page">
        <div class="admin-panel rx-lead-empty">
            <i class="fa-solid fa-satellite-dish"></i>
            <h2>سرنخی پیدا نشد</h2>
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
                <a href="{{ route('admin.leads.index') }}" class="rx-back-link">
                    <i class="fa-solid fa-arrow-right"></i>
                    بازگشت به فهرست سرنخ‌ها
                </a>

                <h1>{{ $lead->name }}</h1>

                <p>
                    <span class="admin-badge status-{{ $lead->status }}">
                        {{ $lead->statusLabel() }}
                    </span>

                    <span class="admin-badge priority-{{ $lead->priority }}">
                        {{ $lead->priorityLabel() }}
                    </span>

                    @if ($lead->is_archived)
                        <span class="admin-badge gray">
                            بایگانی شده
                        </span>
                    @endif
                </p>
            </div>

            <div class="admin-header-actions">
                <a href="{{ route('admin.leads.edit', ['id' => $lead->id]) }}" class="admin-btn admin-btn-ghost">
                    <i class="fa-solid fa-pen"></i>
                    ویرایش سرنخ
                </a>

                <button type="button" wire:click="toggleArchive"
                    class="admin-btn {{ $lead->is_archived ? 'admin-btn-primary' : 'admin-btn-ghost' }}">
                    <i class="fa-solid {{ $lead->is_archived ? 'fa-box-archive' : 'fa-rotate-left' }}"></i>
                    {{ $lead->is_archived ? 'بازگردانی' : 'بایگانی' }}
                </button>
            </div>
        </div>

        @if ($lead->is_archived)
            <div class="rx-archive-banner">
                <i class="fa-solid fa-box-archive"></i>
                <span>این سرنخ بایگانی شده است. برای بازگردانی از دکمه بالای صفحه استفاده کن.</span>
            </div>
        @endif

        <div class="rx-lead-layout">
            <div class="rx-lead-main">
                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>اطلاعات مشتری</h2>
                    </div>

                    <div class="rx-info-grid">
                        <div class="rx-info-item">
                            <span>نام</span>
                            <strong>{{ $lead->name }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>تلفن</span>
                            <strong dir="ltr">{{ $lead->phone }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>ایمیل</span>
                            <strong dir="ltr">{{ $lead->email ?: '—' }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>خدمت مورد نظر</span>
                            <strong>{{ $lead->serviceLabel() }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>تاریخ ثبت</span>
                            <strong>{{ Jalali::date($lead->created_at) }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>آخرین تماس</span>
                            <strong>{{ $lead->last_contact_at ? Jalali::date($lead->last_contact_at) : '—' }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>پیگیری بعدی</span>
                            <strong>{{ $lead->next_follow_up_at ? Jalali::date($lead->next_follow_up_at) : 'ثبت نشده' }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>صفحه مبدأ</span>
                            <strong dir="ltr">{{ $lead->source_page ?: '—' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>متن پیام</h2>
                    </div>

                    <div class="rx-message-box">
                        {{ $lead->message }}
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>یادداشت‌های داخلی</h2>
                        <span class="admin-count">
                            {{ Jalali::toPersianDigits(number_format($lead->notes->count())) }} یادداشت
                        </span>
                    </div>

                    <form wire:submit="addNote" class="rx-note-form">
                        <x-admin.form-errors />

                        <div class="rx-note-form-grid">
                            <x-admin.choice-pills name="noteType" label="نوع یادداشت"
                                :options="\App\Models\LeadNote::typeOptions()" :value="$noteType" />

                            <div class="admin-field rx-note-body-field">
                                <label class="admin-label">متن یادداشت</label>
                                <textarea wire:model.live="noteBody" class="admin-input rx-textarea" rows="3"
                                    placeholder="مثلاً: تماس گرفتم، گفت هفته آینده تصمیم نهایی را می‌گیرد."></textarea>
                                @error('noteBody')
                                    <p class="admin-error">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        <span>{{ $message }}</span>
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="admin-btn admin-btn-primary">
                            <span wire:loading.remove wire:target="addNote">
                                <i class="fa-solid fa-plus"></i>
                                ثبت یادداشت
                            </span>
                            <span wire:loading wire:target="addNote">
                                در حال ثبت...
                            </span>
                        </button>
                    </form>

                    <div class="rx-notes-list">
                        @forelse ($lead->notes as $note)
                            <div class="rx-note-item">
                                <div class="rx-note-head">
                                    <span class="rx-note-type">
                                        {{ $note->typeLabel() }}
                                    </span>

                                    <span class="rx-note-meta">
                                        {{ $note->user?->name ?? 'نامشخص' }}
                                        —
                                        {{ Jalali::date($note->created_at) }}
                                    </span>
                                </div>

                                <div class="rx-note-body">
                                    {{ $note->body }}
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty rx-inline-empty">
                                هنوز یادداشتی ثبت نشده است.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <aside class="rx-lead-side">
                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>تغییر وضعیت</h2>
                    </div>

                    <div class="rx-side-pills">
                        <x-admin.choice-pills name="status" label="" :options="\App\Models\Lead::statusOptions()"
                            :value="$status" />
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>اولویت</h2>
                    </div>

                    <div class="rx-side-pills">
                        <x-admin.choice-pills name="priority" label="" :options="\App\Models\Lead::priorityOptions()"
                            :value="$priority" />
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>زمان پیگیری بعدی</h2>
                    </div>

                    <form wire:submit="saveFollowUp" class="rx-followup-form">
                        <x-admin.jalali-input name="followUpJalali" label="تاریخ جلالی" :value="$followUpJalali"
                            help="مثال: ۱۴۰/۰۷/۶ یا ۱۰۴/۷/۲۶ ۱۴:۳۰" />

                        <button type="submit" class="admin-btn admin-btn-primary w-full">
                            <span wire:loading.remove wire:target="saveFollowUp">
                                <i class="fa-solid fa-bell"></i>
                                ثبت پیگیری
                            </span>
                            <span wire:loading wire:target="saveFollowUp">
                                در حال ثبت...
                            </span>
                        </button>
                    </form>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>اطلاعات فنی</h2>
                    </div>

                    <div class="rx-metadata-list">
                        <div>
                            <span>IP</span>
                            <strong dir="ltr">{{ $metadata['ip'] ?? '—' }}</strong>
                        </div>

                        <div>
                            <span>Referer</span>
                            <strong dir="ltr">{{ $metadata['referer'] ?? '—' }}</strong>
                        </div>

                        <div>
                            <span>Device / Browser</span>
                            <strong>{{ $metadata['device'] ?? '—' }}</strong>
                        </div>
                    </div>

                    <div class="rx-metadata-block">
                        <span>User Agent</span>
                        <pre dir="ltr">{{ $metadata['user_agent'] ?? '—' }}</pre>
                    </div>

                    <div class="rx-metadata-block">
                        <span>Query / UTM</span>
                        <pre
                            dir="ltr">{{ json_encode($metadata['query'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endif