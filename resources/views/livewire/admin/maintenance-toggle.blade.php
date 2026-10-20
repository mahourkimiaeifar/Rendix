<?php

use App\Support\MaintenanceMode;
use Livewire\Volt\Component;

new class extends Component {
    public bool $enabled = false;

    public string $message = '';

    public string $telegram = '';

    public function mount(): void
    {
        $this->refreshState();
    }

    public function refreshState(): void
    {
        $data = MaintenanceMode::data();

        $this->enabled = (bool) ($data['enabled'] ?? false);
        $this->message = (string) ($data['message'] ?? '');
        $this->telegram = (string) ($data['telegram'] ?? '');
    }

    public function toggle(): void
    {
        $nextState = !$this->enabled;

        MaintenanceMode::toggle($nextState);

        $this->refreshState();

        $this->dispatch('rx-toast', [
            'type' => 'success',
            'message' => $nextState
                ? 'حالت تعمیرات وبسایت فعال شد.'
                : 'حالت تعمیرات وبسایت غیرفعال شد.',
        ]);
    }
};

?>

<button type="button" wire:click="toggle"
    class="admin-btn admin-btn-ghost rx-maintenance-btn {{ $enabled ? 'is-active' : '' }}"
    title="{{ $enabled ? 'غیرفعال کردن حالت تعمیرات وبسایت' : 'فعال کردن حالت تعمیرات وبسایت' }}">
    <span class="rx-maintenance-dot" aria-hidden="true"></span>

    <i class="fa-solid {{ $enabled ? 'fa-screwdriver-wrench' : 'fa-toggle-off' }}"></i>

    <span class="rx-maintenance-text">
        {{ $enabled ? 'تعمیرات روشن' : 'حالت تعمیرات' }}
    </span>
</button>