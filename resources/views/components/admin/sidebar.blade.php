@php include resource_path('views/components/admin/partials/nav-config.php'); @endphp

@php
$user = auth('admin')->user();
$initials = $user
    ? collect(explode(' ', $user->name))->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('')
    : 'AD';
$adminBrand = settings('brand.name', 'Assemblies of God');
$adminTagline = settings('brand.tagline', 'Rawalpindi') ?: 'Rawalpindi';
@endphp

{{-- Desktop only — never shown as a mobile drawer --}}
<aside
    class="admin-sidebar admin-sidebar--expanded fixed z-40 inset-y-0 left-0 bg-[#1F1A18] text-[#FAF7F2] flex-col"
    :class="collapsed ? 'admin-sidebar--collapsed' : 'admin-sidebar--expanded'"
    aria-label="Admin navigation"
>
    <div class="admin-sidebar-head shrink-0 border-b border-white/10 px-2 py-3">
        <div
            class="admin-sidebar-brand"
            :class="collapsed ? 'admin-sidebar-brand--collapsed' : 'admin-sidebar-brand--expanded'"
        >
            <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand-link" :title="collapsed ? '{{ $adminBrand }}' : null">
                @if($brandLogoUrl ?? null)
                    <img
                        src="{{ $brandLogoUrl }}"
                        alt="{{ $adminBrand }}"
                        class="admin-sidebar-logo object-contain shrink-0"
                        :class="collapsed ? 'admin-sidebar-logo--collapsed' : ''"
                    >
                @else
                    <span class="admin-sidebar-logo-fallback">
                        {{ mb_strtoupper(mb_substr($adminBrand, 0, 1)) }}
                    </span>
                @endif
                <span class="admin-sidebar-label leading-snug min-w-0 pt-0.5">
                    <span class="block text-[12px] font-semibold text-white leading-snug">{{ $adminBrand }}</span>
                    <span class="block text-[10px] text-white/55 mt-1 leading-none">{{ $adminTagline }}</span>
                </span>
            </a>
            <button
                type="button"
                class="admin-sidebar-toggle"
                @click="setCollapsed(!collapsed)"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                :title="collapsed ? 'Expand' : 'Collapse'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     :class="collapsed ? 'rotate-180' : ''" class="transition-transform duration-200">
                    <path d="M15 6l-6 6 6 6"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="admin-sidebar-nav flex-1 overflow-y-auto overflow-x-hidden py-2">
        @foreach($adminVisibleLinks as $l)
            <a
                href="{{ Route::has($l['route']) ? route($l['route']) : '#' }}"
                :title="collapsed ? '{{ $l['label'] }}' : null"
                @class([
                    'admin-sidebar-link group relative mx-1.5 mb-0.5 flex items-center gap-3 rounded-lg transition px-3 py-2',
                    'bg-white/10 text-brand-secondary font-medium' => $adminNavIsActive($l['route']),
                    'text-white/65 hover:bg-white/8 hover:text-white' => ! $adminNavIsActive($l['route']),
                ])
                :class="collapsed ? 'justify-center px-0 h-10' : 'px-3 py-2'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 opacity-90">
                    {!! $adminNavIcons[$l['icon']] ?? $adminNavIcons['doc'] !!}
                </svg>
                <span class="admin-sidebar-label text-[13px] truncate">{{ $l['label'] }}</span>
                @if(!empty($l['badge']))
                    <span class="admin-sidebar-label ml-auto text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-white/55">{{ $l['badge'] }}</span>
                @endif

                <span
                    x-show="collapsed"
                    x-cloak
                    class="pointer-events-none absolute left-[calc(100%+0.4rem)] top-1/2 z-50 -translate-y-1/2 whitespace-nowrap rounded-md bg-[#1F1A18] px-2 py-1 text-[11px] font-medium text-white opacity-0 shadow-lg ring-1 ring-white/10 transition group-hover:opacity-100"
                >{{ $l['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="admin-sidebar-foot shrink-0 border-t border-white/10 flex items-center gap-2.5 px-3 py-3" :class="collapsed ? 'justify-center px-2' : 'px-3'">
        <div class="w-8 h-8 rounded-full bg-brand-secondary/20 text-brand-secondary flex items-center justify-center text-[11px] font-semibold shrink-0">{{ $initials }}</div>
        <div class="admin-sidebar-label leading-tight min-w-0 overflow-hidden">
            <div class="text-white text-[12px] truncate">{{ $user?->name ?? 'Admin' }}</div>
            <div class="text-[10px] text-white/50 truncate">{{ $user?->roles?->first()?->name ?? 'Administrator' }}</div>
        </div>
    </div>
</aside>
