<?php

use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.app')] class extends Component {
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = trim($slug);
    }

    public function page(): ?Page
    {
        try {
            return Page::query()
                ->active()
                ->where('slug', $this->slug)
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }
};

?>

@php
    $page = $this->page();

    if (! $page) {
        abort(404);
    }

    $content = function_exists('render_rich_content')
        ? render_rich_content((string) $page->content)
        : nl2br(e($page->content));
@endphp

<div>
    <section class="pt-36 pb-24 px-5 md:px-10">
        <div class="max-w-4xl mx-auto">

            <div class="glass p-7 md:p-10 space-y-8">
                <header class="space-y-4">
                    <h1 class="text-3xl md:text-5xl font-black leading-[1.25]">
                        {{ $page->title }}
                    </h1>
                </header>

                <div class="rx-post-content">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </section>
</div>