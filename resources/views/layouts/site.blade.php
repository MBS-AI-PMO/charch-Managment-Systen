<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', config('app.name'))</title>
  @php
    $faviconPath = function_exists('settings') ? settings('brand.favicon') : null;
    $faviconUrl = $brandFaviconUrl ?? ($faviconPath ? site_storage_url($faviconPath) : null);
    $faviconVersion = $faviconPath && is_file(storage_path('app/public/'.ltrim($faviconPath, '/')))
      ? filemtime(storage_path('app/public/'.ltrim($faviconPath, '/')))
      : time();
  @endphp
  @if($faviconUrl)
    <link rel="icon" href="{{ $faviconUrl }}?v={{ $faviconVersion }}" sizes="any">
    <link rel="shortcut icon" href="{{ $faviconUrl }}?v={{ $faviconVersion }}">
    <link rel="apple-touch-icon" href="{{ $faviconUrl }}?v={{ $faviconVersion }}">
  @else
    <link rel="icon" href="{{ asset('favicon.png') }}?v=aog3" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=aog3" sizes="any">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=aog3">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=aog3">
  @endif
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>:root { --brand-primary: {{ $brandPrimaryRgb ?? '122 31 43' }}; --brand-secondary: {{ $brandSecondaryRgb ?? '201 169 97' }}; }</style>
  <style>[x-cloak]{display:none!important}</style>
  @stack('head')
</head>
<body>
  {{ $slot ?? '' }}
  @yield('content')
  <x-sweet-alert />
  @stack('scripts')
</body>
</html>
