<?php

use App\Support\MaintenanceMode;
use Livewire\Volt\Component;

new class extends Component {
    public bool $enabled = false;

    public function mount(): void
    {
        $this->refreshState();
    }

    public function refreshState(): void
    {
        try {
            $this->enabled = MaintenanceMode::enabled();
        } catch (\Throwable $e) {
            $this->enabled = false;
        }
    }

    public function toggle(): void
    {
        $next = ! $this->enabled;

        try {
            if (method_exists(MaintenanceMode::class, 'toggle')) {
                MaintenanceMode::toggle($next);
            } elseif ($next && method_exists(MaintenanceMode::class, 'enable')) {
                MaintenanceMode::enable();
            } elseif (! $next && method_exists(MaintenanceMode::class, 'disable')) {
                MaintenanceMode::disable();
            } else {
                $this->dispatch('rx-toast', ['type' => 'error', 'message' => 'متد تغییر حالت تعمیرات در دسترس نیست.']);

                return;
            }
        } catch (\Throwable $e) {
            $this->dispatch('rx-toast', ['type' => 'error', 'message' => 'تغییر حالت تعمیرات ممکن نشد.']);

            return;
        }

        $this->enabled = $next;

        $this->dispatch('rx-toast', [
            'type' => $next ? 'warning' : 'success',
            'message' => $next
                ? 'حالت تعمیرات وبسایت فعال شد.'
                : 'حالت تعمیرات وبسایت غیرفعال شد.',
        ]);
    }
};

?>

<button
    type="button"
    wire:click="toggle"
    class="admin-btn admin-btn-ghost rx-maintenance-btn {{ $enabled ? 'is-active' : '' }}"
    title="{{ $enabled ? 'غیرفعال کردن حالت تعمیرات' : 'فعال کردن حالت تعمیرات' }}"
>
    <span class="rx-maintenance-dot" aria-hidden="true"></span>

    <i class="fa-solid {{ $enabled ? 'fa-screwdriver-wrench' : 'fa-toggle-off' }}"></i>

    <span class="rx-maintenance-text">
        {{ $enabled ? 'تعمیرات روشن' : 'حالت تعمیرات' }}
    </span>
</button>