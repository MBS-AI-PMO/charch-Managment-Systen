<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', config('app.name') . ' Admin')</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>:root { --brand-primary: {{ $brandPrimaryRgb ?? '122 31 43' }}; --brand-secondary: {{ $brandSecondaryRgb ?? '201 169 97' }}; }</style>
  @stack('head')
</head>
<body class="bg-[#F7F5F1]">
  {{ $slot ?? '' }}
  @yield('content')
  <x-sweet-alert />
  @stack('scripts')
</body>
</html>
