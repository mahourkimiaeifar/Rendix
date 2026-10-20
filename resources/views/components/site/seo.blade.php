@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
])

@php
    $siteName = setting('site.name', 'رندیکس');

    $pageTitle = $title
        ? $title . ' | ' . $siteName
        : $siteName;

    $pageDescription = $description ?: setting(
        'site.tagline',
        'طراحی وبسایت، سئو و دیجیتال مارکتینگ با رندیکس.'
    );

    $ogImage = $image ?: setting_url('brand.og_image', asset('images/og-default.png'));
    $favicon = setting_url('brand.favicon', asset('images/favicon.png?v=6'));
    $logo = setting_url('brand.logo', asset('images/favicon.png?v=6'));

    $canonicalUrl = $canonical ?: url()->current();

    $sameAs = array_values(array_filter([
        setting('social.instagram'),
        setting('social.telegram'),
        setting('social.linkedin'),
        setting('social.youtube'),
        setting('social.twitter'),
    ]));

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $siteName,
        'url' => url('/'),
        'logo' => $logo,
        'description' => $pageDescription,
        'sameAs' => $sameAs,
        'contactPoint' => array_values(array_filter([
            setting('contact.phone') ? [
                '@type' => 'ContactPoint',
                'telephone' => setting('contact.phone'),
                'contactType' => 'customer service',
                'availableLanguage' => ['Persian', 'English'],
            ] : null,
            setting('contact.email') ? [
                '@type' => 'ContactPoint',
                'email' => setting('contact.email'),
                'contactType' => 'customer service',
                'availableLanguage' => ['Persian', 'English'],
            ] : null,
        ])),
    ];
@endphp

<title>{{ $pageTitle }}</title>

<meta name="description" content="{{ $pageDescription }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">

<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="icon" type="image/png" href="{{ $favicon }}">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:locale" content="fa_IR">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">

<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>