<title>{{ $title }}</title>

<meta name="description" content="{{ $description }}">
<meta name="robots" content="{{ $robots }}">
<meta name="author" content="Rendix | Mahour Kimiaeifar">
<link rel="canonical" href="{{ $url }}">

@if($keywords)
    <meta name="keywords" content="{{ $keywords }}">
@endif

<meta property="og:site_name" content="رندیکس">
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:locale" content="fa_IR">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

<meta name="theme-color" media="(prefers-color-scheme: dark)" content="#071329">
<meta name="theme-color" media="(prefers-color-scheme: light)" content="#F7F9FF">

@if(!empty($schema))
    @foreach($schema as $schemaItem)
        <script type="application/ld+json">
            {!! json_encode($schemaItem, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endforeach
@endif