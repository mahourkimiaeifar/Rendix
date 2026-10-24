<?php

use App\Models\ProjectRequest;
use App\Models\ProjectRequestNote;
use App\Services\ActivityLogger;
use App\Support\ProjectRequestNotifier;
use App\Support\Jalali;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')]
    class extends Component {
    #[Url(as: 'id')]
    public ?int $requestId = null;

    public string $status = '';

    public string $note = '';

    public function mount(): void
    {
        $this->syncFromRequest();
    }

    public function updatedRequestId(): void
    {
        $this->syncFromRequest();
    }

    public function request(): ?ProjectRequest
    {
        if (!$this->requestId) {
            return null;
        }

        return ProjectRequest::query()
            ->with('notes.user')
            ->find($this->requestId);
    }

    protected function syncFromRequest(): void
    {
        $request = $this->request();

        if (!$request) {
            return;
        }

        $this->status = (string) $request->status;
    }

    public function updatedStatus(string $value): void
    {
        $request = $this->request();

        if (!$request) {
            return;
        }

        if (!array_key_exists($value, ProjectRequest::statusOptions())) {
            $this->status = (string) $request->status;

            return;
        }

        if ($request->status === $value) {
            return;
        }

        $oldStatus = $request->status;

        $request->status = $value;

        if (
            in_array($value, ['reviewing', 'meeting_scheduled', 'proposal_sent', 'in_progress', 'delivered'], true)
            && !$request->responded_at
        ) {
            $request->responded_at = now();
        }

        $request->save();

        ProjectRequestNotifier::statusChanged($request, $oldStatus);

        ActivityLogger::log('project_request.status_updated', $request, [
            'from' => $oldStatus,
            'to' => $value,
        ]);

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'وضعیت درخواست پروژه تغییر کرد.',
        ]);
    }

    public function addNote(): void
    {
        $request = $this->request();

        if (!$request) {
            return;
        }

        $this->validate([
            'note' => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            'note.required' => 'متن یادداشت را بنویس.',
            'note.min' => 'متن یادداشت خیلی کوتاه است.',
            'note.max' => 'متن یادداشت نمی‌تواند بیشتر از ۲۰۰ کاراکتر باشد.',
        ]);

        ProjectRequestNote::create([
            'project_request_id' => $request->id,
            'user_id' => auth()->id(),
            'body' => trim($this->note),
        ]);

        ActivityLogger::log('project_request.note_added', $request, [
            'body_length' => mb_strlen(trim($this->note)),
        ]);

        $this->reset('note');

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => 'یادداشت داخلی ثبت شد.',
        ]);
    }
};

?>

@php
    $request = $this->request();
@endphp

@if (!$request)
    <div class="admin-page">
        <div class="admin-panel rx-lead-empty">
            <i class="fa-solid fa-satellite-dish"></i>
            <h2>درخواستی پیدا نشد</h2>
            <p>ممکن است این درخواست حذف شده باشد یا لینک درست نباشد.</p>

            <a href="{{ route('admin.project-requests.index') }}" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-arrow-right"></i>
                بازگشت به درخواست‌ها
            </a>
        </div>
    </div>
@else
    <div class="admin-page">
        <div class="admin-page-header">
            <div>
                <a href="{{ route('admin.project-requests.index') }}" class="rx-back-link">
                    <i class="fa-solid fa-arrow-right"></i>
                    بازگشت به درخواست‌های پروژه
                </a>

                <h1>{{ $request->name }}</h1>

                <p>
                    <span class="admin-badge priority-normal" dir="ltr">
                        {{ $request->tracking_code }}
                    </span>

                    <span
                        class="admin-badge {{ $request->status === 'delivered' ? 'status-won' : ($request->status === 'cancelled' || $request->status === 'archived' ? 'status-archived' : 'priority-normal') }}">
                        {{ $request->statusLabel() }}
                    </span>
                </p>
            </div>

            <div class="admin-header-actions">
                <a href="{{ route('request-project.show', $request->tracking_code) }}" target="_blank"
                    rel="noopener noreferrer" class="admin-btn admin-btn-ghost">
                    <i class="fa-solid fa-up-right-from-square"></i>
                    صفحه پیگیری
                </a>
            </div>
        </div>

        <div class="rx-lead-layout">
            <div class="rx-lead-main">
                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>اطلاعات مشتری</h2>
                    </div>

                    <div class="rx-info-grid">
                        <div class="rx-info-item">
                            <span>نام</span>
                            <strong>{{ $request->name }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>تلفن</span>
                            <strong dir="ltr">{{ $request->phone }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>ایمیل</span>
                            <strong dir="ltr">{{ $request->email ?: '—' }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>شرکت</span>
                            <strong>{{ $request->company ?: '—' }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>سمت</span>
                            <strong>{{ $request->role ?: '—' }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>تاریخ ثبت</span>
                            <strong>{{ Jalali::date($request->created_at) }}</strong>
                        </div>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>جزئیات پروژه</h2>
                    </div>

                    <div class="rx-info-grid">
                        <div class="rx-info-item">
                            <span>خدمت</span>
                            <strong>{{ $request->service }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>وبسایت فعلی</span>
                            <strong>{{ $request->websiteStatusLabel() }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>آدرس سایت فعلی</span>
                            <strong dir="ltr">{{ $request->current_website_url ?: '—' }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>زمان‌بندی</span>
                            <strong>{{ $request->timelineLabel() }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>بودجه</span>
                            <strong>{{ $request->budgetLabel() }}</strong>
                        </div>

                        <div class="rx-info-item">
                            <span>آخرین پاسخ</span>
                            <strong>{{ $request->responded_at ? Jalali::date($request->responded_at) : '—' }}</strong>
                        </div>
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>توضیحات مشتری</h2>
                    </div>

                    <div class="rx-edit-form-inner space-y-5">
                        @if ($request->business_description)
                            <div>
                                <h3 class="admin-label">درباره کسب‌وکار</h3>
                                <div class="rx-message-box">
                                    {{ $request->business_description }}
                                </div>
                            </div>
                        @endif

                        @if ($request->project_goals)
                            <div>
                                <h3 class="admin-label">هدف پروژه</h3>
                                <div class="rx-message-box">
                                    {{ $request->project_goals }}
                                </div>
                            </div>
                        @endif

                        @if ($request->competitors)
                            <div>
                                <h3 class="admin-label">رقبا / نمونه‌ها</h3>
                                <div class="rx-message-box">
                                    {{ $request->competitors }}
                                </div>
                            </div>
                        @endif

                        @if ($request->references)
                            <div>
                                <h3 class="admin-label">فایل‌ها / محتوا / توضیحات</h3>
                                <div class="rx-message-box">
                                    {{ $request->references }}
                                </div>
                            </div>
                        @endif

                        @if ($request->message)
                            <div>
                                <h3 class="admin-label">توضیحات تکمیلی</h3>
                                <div class="rx-message-box">
                                    {{ $request->message }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($request->attachments->isNotEmpty())
                    <div class="admin-panel">
                        <div class="admin-panel-header">
                            <h2>فایل‌های ضمیمه</h2>
                            <span class="admin-count">
                                {{ Jalali::toPersianDigits(number_format($request->attachments->count())) }} فایل
                            </span>
                        </div>

                        <div class="rx-edit-form-inner">
                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach ($request->attachments as $attachment)
                                    <div
                                        class="rounded-2xl border border-white/10 bg-white/5 p-4 flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3 min-w-0">
                                            @if ($attachment->isImage())
                                                <img src="{{ $attachment->url() }}" alt="{{ $attachment->original_name }}"
                                                    class="w-14 h-14 rounded-xl object-cover border border-white/10">
                                            @else
                                                <div
                                                    class="w-14 h-14 rounded-xl grid place-items-center bg-sky/10 text-sky border border-sky/20 text-xl">
                                                    <i class="fa-solid fa-file"></i>
                                                </div>
                                            @endif

                                            <div class="min-w-0">
                                                <strong
                                                    class="block text-sm font-black truncate">{{ $attachment->original_name }}</strong>
                                                <small class="block text-xs text-snow/45 mt-1" dir="ltr">
                                                    {{ $attachment->humanSize() }}
                                                </small>
                                            </div>
                                        </div>

                                        <a href="{{ route('admin.project-requests.attachments.download', $attachment->id) }}"
                                            class="admin-btn admin-btn-ghost !px-3 !py-2" target="_blank" rel="noopener noreferrer">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>یادداشت‌های داخلی</h2>
                        <span class="admin-count">
                            {{ Jalali::toPersianDigits(number_format($request->notes->count())) }} یادداشت
                        </span>
                    </div>

                    <form wire:submit="addNote" class="rx-note-form">
                        <x-admin.form-errors />

                        <div class="admin-field">
                            <label class="admin-label">متن یادداشت</label>
                            <textarea wire:model.live="note" class="admin-input rx-textarea" rows="3"
                                placeholder="مثلاً: تماس گرفتم، گفت هفته آینده بودجه را قطعی می‌کند."></textarea>

                            @error('note')
                                <p class="admin-error">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
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
                        @forelse ($request->notes->sortByDesc('created_at') as $note)
                            <div class="rx-note-item">
                                <div class="rx-note-head">
                                    <span class="rx-note-type">
                                        یادداشت داخلی
                                    </span>

                                    <span class="rx-note-meta">
                                        {{ $note->user?->name ?? 'سیستم' }}
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
                        <x-admin.choice-pills name="status" label="" :options="\App\Models\ProjectRequest::statusOptions()"
                            :value="$status" />
                    </div>
                </div>

                <div class="admin-panel">
                    <div class="admin-panel-header">
                        <h2>اطلاعات فنی</h2>
                    </div>

                    <div class="rx-metadata-list">
                        <div>
                            <span>IP</span>
                            <strong dir="ltr">{{ $request->metadata['ip'] ?? '—' }}</strong>
                        </div>

                        <div>
                            <span>Device</span>
                            <strong>{{ $request->metadata['device'] ?? '—' }}</strong>
                        </div>

                        <div>
                            <span>Referer</span>
                            <strong dir="ltr">{{ $request->metadata['referer'] ?? '—' }}</strong>
                        </div>
                    </div>

                    <div class="rx-metadata-block">
                        <span>User Agent</span>
                        <pre dir="ltr">{{ $request->metadata['user_agent'] ?? '—' }}</pre>
                    </div>

                    <div class="rx-metadata-block">
                        <span>Query / UTM</span>
                        <pre
                            dir="ltr">{{ json_encode($request->metadata['query'] ?? [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endif