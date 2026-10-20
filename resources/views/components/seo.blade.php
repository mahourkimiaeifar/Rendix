@props([
    'title' => 'رندیکس | سیستم رشد دیجیتال',
    'description' => 'رندیکس — دیجیتال مارکتینگ در نجف‌آباد، طراحی وبسایت اختصاصی، سئو و رشد کسب‌وکار شما.',
    'keywords' => 'دیجیتال مارکتینگ نجف آباد, طراحی وبسایت, سئو, رندیکس',
    'image' => '/images/og-image.jpg',
    'type' => 'website',
    'schema' => null,
])

@php
    $canonical = url()->current();
    $imageUrl = asset($image);
    $siteName = 'رندیکس';
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<link rel="canonical" href="{{ $canonical }}">
<meta name="robots" content="index, follow">

{{-- Open Graph --}}
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $imageUrl }}">
<meta property="og:locale" content="fa_IR">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $imageUrl }}">

{{-- Schema.org JSON-LD --}}
@if($schema)
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif