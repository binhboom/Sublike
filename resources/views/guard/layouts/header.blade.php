<head>
    <title>@yield('title')</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="logo" content="{{ site('logo') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (Auth::check())
        <meta name="access-token" content="{{ Auth::user()->api_token }}">
    @endif

    <!-- [SEO] -->
    <meta name="description" content="{{ siteValue('description') }}">
    <meta name="keywords" content="{{ siteValue('keywords') }}">
    <meta name="author" content="{{ siteValue('author') }}">
    <meta name="robots" content="index, follow">

    <!-- [Open Graph] -->
    <meta property="og:title" content="{{ siteValue('title') }}">
    <meta property="og:description" content="{{ siteValue('description') }}">
    <meta property="og:image" content="{{ siteValue('thumbnail') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ siteValue('title') }}">
    <meta property="og:type" content="website">

    {{-- twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ siteValue('title') }}">
    <meta name="twitter:title" content="{{ siteValue('title') }}">
    <meta name="twitter:description" content="{{ siteValue('description') }}">
    <meta name="twitter:image" content="{{ siteValue('thumbnail') }}">
    <meta name="twitter:image:alt" content="{{ siteValue('title') }}">
    <meta name="twitter:creator" content="{{ siteValue('author') }}">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:domain" content="{{ url()->current() }}">
    <meta name="twitter:data1" content="{{ siteValue('author') }}">

    <!-- [Favicon] icon -->
    <link rel="icon" href="{{ siteValue('favicon') }}" type="image/x-icon"> <!-- [Font] Family -->
    <link rel="shortcut icon" href="{{ siteValue('favicon') }}" type="image/x-icon">

    <link rel="stylesheet" href="/assets/fonts/inter/inter.css" id="main-font-link" />
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="/assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="/assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="/assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="/assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="/assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="/assets/css/style-preset.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    {!! site('script_head') !!}
</head>
