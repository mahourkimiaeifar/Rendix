<?php

use App\Models\Template;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {
    public function templates()
    {
        return Template::query()
            ->with('languages')
            ->withCount(['files', 'reports'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function toggleActive(int $id): void
    {
        $template = Template::findOrFail($id);
        $template->update(['is_active' => ! $template->is_active]);

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $template->is_active ? 'قالب فعال شد.' : 'قالب غیرفعال شد.',
        ]);
    }

    public function delete(int $id): void
    {
        $template = Template::findOrFail($id);

        foreach ($template->files as $file) {
            try {
                if (Storage::disk('public')->exists($file->path)) {
                    Storage::disk('public')->delete($file->path);
                }
            } catch (\Throwable $e) {}
        }

        $template->delete();

        $this->dispatch('rx-toast', ['type' => 'success', 'message' => 'قالب و فایل‌هایش حذف شدند.']);
    }
};

?>

@php
    $templates = $this->templates();
@endphp

<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <h1>فروشگاه قالب</h1>
            <p>مدیریت قالب‌های برنامه‌نویسی، فایل‌ها و دموهای زنده</p>
        </div>

        <div class="admin-header-actions">
            <a href="{{ route('templates.index') }}" target="_blank" class="admin-btn admin-btn-ghost">
                <i class="fa-solid fa-up-right-from-square"></i> مشاهده فروشگاه
            </a>
            <a href="{{ route('admin.templates.form') }}" class="admin-btn admin-btn-primary">
                <i class="fa-solid fa-plus"></i> افزودن قالب
            </a>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-header">
            <h2>لیست قالب‌ها</h2>
            <span class="admin-count">{{ \App\Support\Jalali::toPersianDigits((string) $templates->count()) }} قالب</span>
        </div>

        @if ($templates->isEmpty())
            <div class="rx-lead-empty">
                <i class="fa-solid fa-code"></i>
                <h2>هنوز قالبی ثبت نشده</h2>
                <p>اولین قالب برنامه‌نویسی خود را برای فروش اضافه کن.</p>
            </div>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>قالب</th>
                            <th>قیمت</th>
                            <th>زبان‌ها</th>
                            <th>فایل / دمو</th>
                            <th>بازدید / فروش</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $template)
                            <tr>
                                <td>
                                    <div class="admin-table-user">
                                        <span class="admin-avatar small"><i class="fa-solid fa-code" style="font-size:13px;"></i></span>
                                        <div>
                                            <strong>{{ $template->name }}</strong>
                                            <small>{{ $template->is_fullstack ? 'Full-Stack' : 'Front-End' }} · {{ \App\Support\Jalali::date($template->created_at) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><strong style="color:var(--rx-sky);">{{ number_format($template->price, 0, '.', ',') }}</strong> <small style="color:var(--rx-soft);">تومان</small></td>
                                <td>
                                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                        @foreach ($template->languages->take(3) as $lng)
                                            <span class="admin-badge" style="background:{{ $lng->color ?? 'rgba(69,200,255,.12)' }};">{{ $lng->language }}</span>
                                        @endforeach
                                        @if ($template->languages->count() > 3)
                                            <span class="admin-badge gray">+{{ $template->languages->count() - 3 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {{ \App\Support\Jalali::toPersianDigits((string) $template->files_count) }} فایل
                                    @if ($template->demoFile())
                                        <small style="display:block;color:#86efac;font-size:10px;margin-top:2px;"><i class="fa-solid fa-circle-check"></i> دمو دارد</small>
                                    @else
                                        <small style="display:block;color:#fecaca;font-size:10px;margin-top:2px;"><i class="fa-solid fa-circle-exclamation"></i> بدون دمو</small>
                                    @endif
                                </td>
                                <td>
                                    {{ \App\Support\Jalali::toPersianDigits(number_format($template->views_count)) }} /
                                    {{ \App\Support\Jalali::toPersianDigits(number_format($template->downloads_count)) }}
                                </td>
                                <td>
                                    <span class="admin-badge {{ $template->is_active ? 'success' : 'gray' }}">{{ $template->is_active ? 'فعال' : 'غیرفعال' }}</span>
                                    @if ($template->reports_count > 0)
                                        <span class="admin-badge danger">{{ \App\Support\Jalali::toPersianDigits((string) $template->reports_count) }} گزارش</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-row-actions">
                                        <a href="{{ route('templates.show', $template->slug) }}" target="_blank" class="admin-icon-btn" title="مشاهده در سایت"><i class="fa-solid fa-eye"></i></a>
                                        <a href="{{ route('admin.templates.form', ['id' => $template->id]) }}" class="admin-icon-btn" title="ویرایش"><i class="fa-solid fa-pen"></i></a>
                                        <button wire:click="toggleActive({{ $template->id }})" class="admin-icon-btn" title="فعال/غیرفعال"><i class="fa-solid fa-power-off"></i></button>
                                        <button wire:click="delete({{ $template->id }})" wire:confirm="قالب و همه فایل‌هایش حذف شوند؟" class="admin-icon-btn danger" title="حذف"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>