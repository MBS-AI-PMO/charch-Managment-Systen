@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | Church CMS Admin</title>
    @php
        $adminFavicon = $brandFaviconUrl ?? ((function_exists('settings') && settings('brand.favicon')) ? site_storage_url(settings('brand.favicon')) : null);
    @endphp
    @if($adminFavicon)
        <link rel="icon" href="{{ $adminFavicon }}?v={{ time() }}" sizes="any">
        <link rel="shortcut icon" href="{{ $adminFavicon }}?v={{ time() }}">
    @else
        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" sizes="32x32">
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>:root { --brand-primary: {{ $brandPrimaryRgb ?? '122 31 43' }}; --brand-secondary: {{ $brandSecondaryRgb ?? '201 169 97' }}; }</style>
    <style>[x-cloak]{display:none!important}</style>
    @stack('head')
</head>
<body class="admin-app bg-[#F7F5F1] font-sans text-ink antialiased">
    <div
        x-data="{
            sidebar: false,
            collapsed: localStorage.getItem('adminSidebarCollapsed') === '1'
        }"
        class="min-h-screen"
    >
        <x-admin.sidebar />

        <div
            class="admin-main flex flex-col min-h-screen"
            :class="collapsed ? 'admin-main--collapsed' : 'admin-main--expanded'"
        >
            <x-admin.topbar />

            @if ($errors->any())
                <div class="px-4 lg:px-8 pt-4">
                    <div class="px-4 py-3 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 text-sm">
                        <p class="font-medium mb-1">Please correct the following:</p>
                        <ul class="list-disc list-inside text-xs space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <main class="flex-1 p-4 lg:p-8">
                {{ $slot }}
            </main>
            <footer class="px-6 py-4 text-xs text-ink-muted border-t border-[rgb(var(--border))] bg-white/40">
                {{ settings('brand.name', 'Church CMS') }} Admin &middot; {{ now()->year }}
            </footer>
        </div>
    </div>
    <x-sweet-alert />
    @stack('scripts')
</body>
</html>
