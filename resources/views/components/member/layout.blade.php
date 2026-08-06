@props(['title' => 'Member Portal'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | {{ function_exists('settings') ? settings('brand.name', config('app.name')) : config('app.name') }}</title>
    @php
        $memberFavicon = $brandFaviconUrl ?? ((function_exists('settings') && settings('brand.favicon')) ? site_storage_url(settings('brand.favicon')) : null);
    @endphp
    @if($memberFavicon)
        <link rel="icon" href="{{ $memberFavicon }}" sizes="any">
        <link rel="shortcut icon" href="{{ $memberFavicon }}">
    @else
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" sizes="32x32">
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @endif
    <style>
        :root {
            --brand-primary: {{ $brandPrimaryRgb ?? '122 31 43' }};
            --brand-secondary: {{ $brandSecondaryRgb ?? '201 169 97' }};
        }
        [x-cloak]{display:none!important}
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="bg-surface font-sans text-ink antialiased min-h-screen flex flex-col">
    <x-member.header />
    <main class="flex-1">
        {{ $slot }}
    </main>
    <x-member.footer />
    <x-sweet-alert />
    @stack('scripts')
</body>
</html>
