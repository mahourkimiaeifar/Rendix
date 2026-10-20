<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Seo extends Component
{
    public string $title;
    public string $description;
    public string $keywords;
    public string $url;
    public string $image;
    public string $type;
    public string $robots;
    public array $schema;

    public function __construct(
        string $title = 'رندیکس | طراحی وبسایت، سئو و دیجیتال مارکتینگ در نجف آباد',
        string $description = 'آژانس رندیکس؛ طراحی وبسایت اختصاصی، سئو و دیجیتال مارکتینگ در نجف آباد و اصفهان.',
        string $keywords = '',
        ?string $url = null,
        string $image = '/images/og-image.jpg',
        string $type = 'website',
        string $robots = 'index, follow, max-image-preview:large',
        array $schema = []
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        $this->url = $url ?? url()->current();
        $this->image = str_starts_with($image, 'http') ? $image : url($image);
        $this->type = $type;
        $this->robots = $robots;
        $this->schema = $schema;
    }

    public function render()
    {
        return view('components.seo');
    }
}