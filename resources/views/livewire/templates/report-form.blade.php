<?php

use App\Models\TemplateReport;
use Livewire\Volt\Component;

new class extends Component {
    public int $templateId = 0;

    public string $reporter_email = '';
    public string $report_description = '';

    public bool $open = false;
    public bool $sent = false;

    public function submit(): void
    {
        $this->validate([
            'reporter_email' => ['required', 'email', 'max:190'],
            'report_description' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'reporter_email.required' => 'ایمیلت را بنویس تا بتوانیم پاسخ بدهیم.',
            'reporter_email.email' => 'ایمیل معتبر نیست.',
            'report_description.required' => 'توضیح بده چه چیزی خراب است.',
            'report_description.min' => 'توضیح باید حداقل ۱۰ کاراکتر باشد.',
        ]);

        try {
            TemplateReport::create([
                'template_id' => $this->templateId,
                'reporter_email' => strtolower(trim($this->reporter_email)),
                'description' => trim($this->report_description),
                'status' => 'pending',
            ]);
        } catch (\Throwable $e) {
            $this->dispatch('rx-toast', ['type' => 'error', 'message' => 'ثبت گزارش ممکن نشد؛ دوباره تلاش کن.']);

            return;
        }

        $this->reset(['reporter_email', 'report_description']);
        $this->open = false;
        $this->sent = true;

        $this->dispatch('rx-toast', ['type' => 'success', 'message' => 'گزارشت ثبت شد؛ ممنون که کمک می‌کنی!']);
    }
};

?>

<div class="rx-tem-report">
    <style>
        .rx-tem-report{padding:18px;border-radius:18px;background:rgba(244,63,94,.05);border:1px solid rgba(244,63,94,.15);display:grid;gap:12px}
        .rx-tem-report-btn{width:100%;padding:10px;border-radius:12px;background:transparent;border:1px solid rgba(244,63,94,.25);color:#fda4af;font-size:13px;font-weight:850;cursor:pointer;transition:all .22s ease;display:inline-flex;align-items:center;justify-content:center;gap:8px;font-family:inherit}
        .rx-tem-report-btn:hover{background:rgba(244,63,94,.10);border-color:rgba(244,63,94,.45);transform:translateY(-1px)}
        .rx-tem-report-hint{margin:0;color:var(--rx-fg-soft,rgba(247,249,255,.36));font-size:11.5px;line-height:1.9}
        .rx-tem-report-form{display:grid;gap:10px}
        .rx-tem-report-form input,.rx-tem-report-form textarea{width:100%;min-height:42px;padding:10px 13px;border-radius:12px;background:var(--rx-panel-2,rgba(255,255,255,.025));border:1px solid var(--rx-border,rgba(255,255,255,.08));color:var(--rx-fg,#F7F9FF);outline:none;font-size:13px;font-family:inherit;transition:border-color .2s ease,box-shadow .2s ease}
        .rx-tem-report-form textarea{min-height:92px;resize:vertical;line-height:1.9}
        .rx-tem-report-form input:focus,.rx-tem-report-form textarea:focus{border-color:rgba(69,200,255,.5);box-shadow:0 0 0 3px rgba(69,200,255,.10)}
        .rx-tem-report-err{color:#fda4af;font-size:11.5px;font-weight:800}
        .rx-tem-report-row{display:flex;gap:8px;flex-wrap:wrap}
        .rx-tem-report-send{padding:10px 16px;border-radius:12px;border:0;background:linear-gradient(90deg,#1769FF,#45C8FF);color:#fff;font-weight:900;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:8px;font-family:inherit;transition:all .22s ease}
        .rx-tem-report-send:hover{transform:translateY(-1px);box-shadow:0 14px 30px -14px rgba(23,105,255,.9)}
        .rx-tem-report-cancel{padding:10px 14px;border-radius:12px;background:transparent;border:1px solid var(--rx-border,rgba(255,255,255,.08));color:var(--rx-fg-muted,rgba(247,249,255,.62));font-size:13px;font-weight:800;cursor:pointer;font-family:inherit;transition:all .2s ease}
        .rx-tem-report-cancel:hover{color:var(--rx-fg,#F7F9FF);border-color:rgba(255,255,255,.2)}
        .rx-tem-report-success{padding:12px;border-radius:12px;background:rgba(34,197,94,.10);border:1px solid rgba(34,197,94,.25);color:#bbf7d0;font-size:13px;font-weight:850;text-align:center;line-height:1.9}
    </style>

    @if ($sent)
        <div class="rx-tem-report-success">
            <i class="fa-solid fa-circle-check"></i>
            گزارشت ثبت شد؛ تیم رندیکس به‌زودی لینک دمو را بررسی می‌کند.
        </div>

        <button type="button" class="rx-tem-report-btn" wire:click="$set('sent', false)">
            <i class="fa-solid fa-plus"></i>
            <span>ثبت یک گزارش دیگر</span>
        </button>
    @elseif ($open)
        <form wire:submit="submit" class="rx-tem-report-form">
            <input type="email" wire:model="reporter_email" placeholder="ایمیل شما" dir="ltr" autocomplete="email">
            <textarea wire:model="report_description" placeholder="دقیقاً چه چیزی خراب است؟ مثلاً: دمو سفید است / خطا می‌دهد / استایل‌ها لود نمی‌شوند"></textarea>

            @error('reporter_email')<small class="rx-tem-report-err">{{ $message }}</small>@enderror
            @error('report_description')<small class="rx-tem-report-err">{{ $message }}</small>@enderror

            <div class="rx-tem-report-row">
                <button type="submit" class="rx-tem-report-send">
                    <i class="fa-solid fa-paper-plane"></i>
                    ارسال گزارش
                </button>
                <button type="button" class="rx-tem-report-cancel" wire:click="$set('open', false)">
                    انصراف
                </button>
            </div>
        </form>
    @else
        <button type="button" class="rx-tem-report-btn" wire:click="$set('open', true)">
            <i class="fa-solid fa-flag"></i>
            <span>گزارش خرابی لینک دمو</span>
        </button>

        <p class="rx-tem-report-hint">
            اگر دمو باز نمی‌شود یا به‌هم ریخته است، به ما بگو تا سریع درستش کنیم.
        </p>
    @endif
</div>