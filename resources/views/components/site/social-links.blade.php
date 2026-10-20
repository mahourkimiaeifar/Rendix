@php
    $telegram = setting('social.telegram', 'https://t.me/RendixDMServices');

    $whatsapp = setting('social.whatsapp');
    if ($whatsapp && ! filter_var($whatsapp, FILTER_VALIDATE_URL)) {
        $whatsapp = 'https://wa.me/' . preg_replace('/\D/', '', $whatsapp);
    }

    $links = [
        [
            'label' => 'تلگرام',
            'icon' => 'fa-brands fa-telegram',
            'url' => $telegram,
        ],
        [
            'label' => 'واتساپ',
            'icon' => 'fa-brands fa-whatsapp',
            'url' => $whatsapp,
        ],
        [
            'label' => 'اینستاگرام',
            'icon' => 'fa-brands fa-instagram',
            'url' => setting('social.instagram'),
        ],
        [
            'label' => 'لینکدین',
            'icon' => 'fa-brands fa-linkedin-in',
            'url' => setting('social.linkedin'),
        ],
        [
            'label' => 'یوتیوب',
            'icon' => 'fa-brands fa-youtube',
            'url' => setting('social.youtube'),
        ],
        [
            'label' => 'توییتر / X',
            'icon' => 'fa-brands fa-x-twitter',
            'url' => setting('social.twitter'),
        ],
    ];
@endphp

<div class="rx-social-links">
    @foreach ($links as $link)
        @if ($link['url'])
            <a
                href="{{ $link['url'] }}"
                target="_blank"
                rel="noopener noreferrer"
                class="rx-social-link"
                title="{{ $link['label'] }}"
                aria-label="{{ $link['label'] }}"
            >
                <i class="{{ $link['icon'] }}"></i>
            </a>
        @endif
    @endforeach
</div>