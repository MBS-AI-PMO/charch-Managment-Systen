@php
$kpiCards = [
    [
        'label' => 'Pages',
        'value' => $kpis['pages'],
        'sub'   => 'singletons',
        'link'  => route('admin.pages.index'),
        'icon'  => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/>',
        'tint'  => 'bg-brand-primary/10 text-brand-primary',
    ],
    [
        'label' => 'Published news',
        'value' => $kpis['posts'],
        'sub'   => $kpis['drafts'] . ' draft' . ($kpis['drafts'] === 1 ? '' : 's'),
        'link'  => route('admin.blog.posts.index'),
        'icon'  => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/>',
        'tint'  => 'bg-brand-secondary/20 text-[#8a6e2c]',
    ],
    [
        'label' => 'Upcoming events',
        'value' => $kpis['upcoming_events'],
        'sub'   => 'published',
        'link'  => route('admin.events.index'),
        'icon'  => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
        'tint'  => 'bg-emerald-50 text-emerald-700',
    ],
    [
        'label' => 'Unread messages',
        'value' => $kpis['unread_messages'],
        'sub'   => 'inbox',
        'link'  => route('admin.messages.index'),
        'icon'  => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'tint'  => 'bg-blue-50 text-blue-700',
    ],
];

$user = auth('admin')->user();
$hour = (int) now()->format('G');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
@endphp

<x-admin.layout title="Dashboard">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif text-ink">{{ $greeting }}, {{ explode(' ', $user?->name ?? 'Admin')[0] }}</h1>
            <p class="text-sm text-ink-muted mt-1">Here's what's happening across your church website today.</p>
        </div>
        <div class="text-xs text-ink-muted">Today is {{ now()->format('l, F j, Y') }}</div>
    </div>

    {{-- KPI grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        @foreach($kpiCards as $k)
            <div class="card p-5">
                <div class="flex items-start justify-between">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $k['tint'] }}">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $k['icon'] !!}</svg>
                    </div>
                    <span class="text-[11px] text-ink-muted uppercase tracking-wider">{{ $k['sub'] }}</span>
                </div>
                <div class="mt-4 text-3xl font-serif text-ink">{{ $k['value'] }}</div>
                <div class="mt-1 flex items-center justify-between">
                    <span class="text-sm text-ink-muted">{{ $k['label'] }}</span>
                    <a href="{{ $k['link'] }}" class="text-xs text-brand-primary hover:underline">View all &rarr;</a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Activity + quick actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card lg:col-span-2">
            <div class="px-5 py-4 border-b border-[rgb(var(--border))] flex items-center justify-between">
                <div>
                    <h2 class="text-base font-serif">Recent activity</h2>
                    <p class="text-xs text-ink-muted mt-0.5">Edits, publishes and uploads from your team.</p>
                </div>
            </div>
            @if($activity->isEmpty())
                <div class="px-5 py-10 text-center text-sm text-ink-muted">No activity yet. As your team edits pages, posts and events, the log will fill up here.</div>
            @else
                <ul class="divide-y divide-[rgb(var(--border))]">
                    @foreach($activity as $a)
                        <li class="px-5 py-3 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-surface flex items-center justify-center text-ink-muted shrink-0">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-ink">
                                    <span class="font-medium">{{ $a->user?->name ?? 'System' }}</span>
                                    <span class="text-ink-muted">{{ $a->action }}</span>
                                    <span class="font-medium">{{ class_basename($a->subject_type) }} #{{ $a->subject_id }}</span>
                                </p>
                                <p class="text-xs text-ink-muted mt-0.5">{{ $a->created_at?->diffForHumans() }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="text-base font-serif mb-1">Quick actions</h2>
                <p class="text-xs text-ink-muted mb-4">Get something done without digging through menus.</p>
                <div class="space-y-2">
                    <a href="{{ route('admin.blog.posts.create') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-brand-primary/10 text-brand-primary flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            </span>
                            <span><span class="block font-medium">New news post</span><span class="block text-xs text-ink-muted">Share an update</span></span>
                        </span>
                        <span class="text-ink-muted">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.events.create') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-700 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>
                            </span>
                            <span><span class="block font-medium">New event</span><span class="block text-xs text-ink-muted">Add a service or gathering</span></span>
                        </span>
                        <span class="text-ink-muted">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.pages.index') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-brand-secondary/20 text-[#8a6e2c] flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/></svg>
                            </span>
                            <span><span class="block font-medium">Edit a page</span><span class="block text-xs text-ink-muted">Hero, body, SEO</span></span>
                        </span>
                        <span class="text-ink-muted">&rarr;</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center justify-between gap-3 p-3 rounded-lg border border-[rgb(var(--border))] hover:bg-surface transition">
                        <span class="flex items-center gap-3 text-sm">
                            <span class="w-8 h-8 rounded-md bg-blue-50 text-blue-700 flex items-center justify-center">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/></svg>
                            </span>
                            <span><span class="block font-medium">Site settings</span><span class="block text-xs text-ink-muted">Brand, contact, footer</span></span>
                        </span>
                        <span class="text-ink-muted">&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Trading-style KPI trend chart (Pages / News / Events / Messages) --}}
            <div
                class="card overflow-hidden"
                x-data="adminKpiTradingChart(@js($chart))"
            >
                <div class="px-5 pt-4 pb-2 flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-serif">Site pulse</h2>
                        <p class="text-xs text-ink-muted mt-0.5">14-day cumulative trend of your top counters</p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[10px] uppercase tracking-wider text-ink-muted font-medium">Live</span>
                    </div>
                </div>

                <div class="px-5 pb-2 flex flex-wrap gap-x-3 gap-y-1.5">
                    <template x-for="s in series" :key="s.key">
                        <button
                            type="button"
                            class="inline-flex items-center gap-1.5 text-[11px] transition"
                            :class="s.visible ? 'text-ink' : 'text-ink-muted/50'"
                            @click="toggle(s.key)"
                        >
                            <span class="w-2 h-2 rounded-sm" :style="'background: rgb(' + s.color + ')'"></span>
                            <span x-text="s.label"></span>
                            <span class="font-semibold tabular-nums" x-text="s.values[s.values.length - 1]"></span>
                        </button>
                    </template>
                </div>

                <div class="relative px-2 pb-2">
                    <canvas
                        x-ref="canvas"
                        class="w-full h-[220px] cursor-crosshair"
                        @mousemove="onMove($event)"
                        @mouseleave="hover = null"
                    ></canvas>
                    <div
                        x-show="hover"
                        x-cloak
                        class="pointer-events-none absolute z-10 rounded-md bg-[#1F1A18] text-white text-[11px] px-2.5 py-2 shadow-lg min-w-[7.5rem]"
                        :style="tooltipStyle"
                    >
                        <div class="font-medium mb-1" x-text="hover?.label"></div>
                        <template x-for="row in (hover?.rows || [])" :key="row.key">
                            <div class="flex items-center justify-between gap-3 py-0.5">
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full" :style="'background: rgb(' + row.color + ')'"></span>
                                    <span class="text-white/70" x-text="row.label"></span>
                                </span>
                                <span class="tabular-nums font-medium" x-text="row.value"></span>
                            </div>
                        </template>
                        <div class="mt-1 pt-1 border-t border-white/10 flex justify-between gap-3 text-white/55">
                            <span>Volume</span>
                            <span class="tabular-nums" x-text="hover?.volume ?? 0"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminKpiTradingChart', (payload) => ({
                labels: payload.labels || [],
                series: (payload.series || []).map((s) => ({ ...s, visible: true })),
                volume: payload.volume || [],
                hover: null,
                tooltipStyle: '',
                raf: null,
                t0: 0,
                pad: { top: 16, right: 12, bottom: 36, left: 8 },
                volH: 36,

                init() {
                    this.t0 = performance.now();
                    const redraw = () => {
                        this.draw(performance.now() - this.t0);
                        this.raf = requestAnimationFrame(redraw);
                    };
                    this.raf = requestAnimationFrame(redraw);
                    this._ro = new ResizeObserver(() => this.draw(performance.now() - this.t0));
                    this.$nextTick(() => {
                        if (this.$refs.canvas) this._ro.observe(this.$refs.canvas);
                    });
                },

                destroy() {
                    if (this.raf) cancelAnimationFrame(this.raf);
                    if (this._ro) this._ro.disconnect();
                },

                toggle(key) {
                    const s = this.series.find((x) => x.key === key);
                    if (!s) return;
                    const visibleCount = this.series.filter((x) => x.visible).length;
                    if (s.visible && visibleCount === 1) return;
                    s.visible = !s.visible;
                },

                onMove(e) {
                    const canvas = this.$refs.canvas;
                    const rect = canvas.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const plot = this.plotRect(rect.width, rect.height);
                    if (x < plot.x || x > plot.x + plot.w) {
                        this.hover = null;
                        return;
                    }
                    const n = Math.max(this.labels.length - 1, 1);
                    const idx = Math.round(((x - plot.x) / plot.w) * n);
                    const i = Math.max(0, Math.min(this.labels.length - 1, idx));
                    this.hover = {
                        index: i,
                        label: this.labels[i],
                        volume: this.volume[i] ?? 0,
                        rows: this.series.filter((s) => s.visible).map((s) => ({
                            key: s.key,
                            label: s.label,
                            color: s.color,
                            value: s.values[i] ?? 0,
                        })),
                    };
                    const tipW = 140;
                    const left = Math.min(Math.max(8, x - tipW / 2), rect.width - tipW - 8);
                    const top = Math.max(8, y - 88);
                    this.tooltipStyle = `left:${left}px;top:${top}px;width:${tipW}px`;
                },

                plotRect(w, h) {
                    return {
                        x: this.pad.left,
                        y: this.pad.top,
                        w: w - this.pad.left - this.pad.right,
                        h: h - this.pad.top - this.pad.bottom - this.volH - 8,
                    };
                },

                draw(elapsed) {
                    const canvas = this.$refs.canvas;
                    if (!canvas) return;
                    const dpr = window.devicePixelRatio || 1;
                    const cssW = canvas.clientWidth || 320;
                    const cssH = canvas.clientHeight || 220;
                    if (canvas.width !== Math.floor(cssW * dpr) || canvas.height !== Math.floor(cssH * dpr)) {
                        canvas.width = Math.floor(cssW * dpr);
                        canvas.height = Math.floor(cssH * dpr);
                    }
                    const ctx = canvas.getContext('2d');
                    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                    ctx.clearRect(0, 0, cssW, cssH);

                    const plot = this.plotRect(cssW, cssH);
                    const visible = this.series.filter((s) => s.visible);
                    const allVals = visible.flatMap((s) => s.values);
                    let maxV = Math.max(1, ...allVals, 1);
                    maxV = Math.ceil(maxV * 1.12) || 1;
                    const maxVol = Math.max(1, ...this.volume);
                    const n = Math.max(this.labels.length - 1, 1);
                    const progress = Math.min(1, elapsed / 900);
                    const ease = 1 - Math.pow(1 - progress, 3);
                    const pulse = 0.5 + 0.5 * Math.sin(elapsed / 420);

                    // Grid
                    ctx.strokeStyle = 'rgba(31, 26, 24, 0.06)';
                    ctx.lineWidth = 1;
                    for (let g = 0; g <= 4; g++) {
                        const gy = plot.y + (plot.h * g) / 4;
                        ctx.beginPath();
                        ctx.moveTo(plot.x, gy);
                        ctx.lineTo(plot.x + plot.w, gy);
                        ctx.stroke();
                        const val = Math.round(maxV * (1 - g / 4));
                        ctx.fillStyle = 'rgba(31, 26, 24, 0.35)';
                        ctx.font = '10px Inter, system-ui, sans-serif';
                        ctx.textAlign = 'right';
                        ctx.fillText(String(val), plot.x + plot.w - 2, gy - 3);
                    }

                    // Volume bars
                    const volY = plot.y + plot.h + 10;
                    const barW = Math.max(2, (plot.w / this.labels.length) * 0.55);
                    this.volume.forEach((v, i) => {
                        const x = plot.x + (i / n) * plot.w;
                        const bh = (v / maxVol) * this.volH * ease;
                        ctx.fillStyle = this.hover?.index === i
                            ? 'rgba(122, 31, 43, 0.45)'
                            : 'rgba(122, 31, 43, 0.14)';
                        ctx.fillRect(x - barW / 2, volY + this.volH - bh, barW, bh);
                    });

                    const xAt = (i) => plot.x + (i / n) * plot.w;
                    const yAt = (v) => plot.y + plot.h - (v / maxV) * plot.h * ease;

                    // Area + lines
                    visible.forEach((s) => {
                        const pts = s.values.map((v, i) => ({ x: xAt(i), y: yAt(v) }));
                        if (pts.length < 2) return;

                        const grad = ctx.createLinearGradient(0, plot.y, 0, plot.y + plot.h);
                        grad.addColorStop(0, `rgba(${s.color}, 0.22)`);
                        grad.addColorStop(1, `rgba(${s.color}, 0.01)`);
                        ctx.beginPath();
                        ctx.moveTo(pts[0].x, plot.y + plot.h);
                        pts.forEach((p) => ctx.lineTo(p.x, p.y));
                        ctx.lineTo(pts[pts.length - 1].x, plot.y + plot.h);
                        ctx.closePath();
                        ctx.fillStyle = grad;
                        ctx.fill();

                        ctx.beginPath();
                        pts.forEach((p, i) => (i ? ctx.lineTo(p.x, p.y) : ctx.moveTo(p.x, p.y)));
                        ctx.strokeStyle = `rgb(${s.color})`;
                        ctx.lineWidth = 1.8;
                        ctx.lineJoin = 'round';
                        ctx.lineCap = 'round';
                        ctx.stroke();

                        // Candlestick-style markers on each point
                        pts.forEach((p, i) => {
                            const prev = s.values[i - 1] ?? s.values[i];
                            const up = s.values[i] >= prev;
                            ctx.fillStyle = up ? `rgb(${s.color})` : `rgba(${s.color}, 0.55)`;
                            ctx.fillRect(p.x - 1.5, p.y - 3, 3, 6);
                        });

                        // Live pulse on latest point
                        const last = pts[pts.length - 1];
                        ctx.beginPath();
                        ctx.arc(last.x, last.y, 3 + pulse * 2.5, 0, Math.PI * 2);
                        ctx.fillStyle = `rgba(${s.color}, ${0.15 + pulse * 0.2})`;
                        ctx.fill();
                        ctx.beginPath();
                        ctx.arc(last.x, last.y, 2.5, 0, Math.PI * 2);
                        ctx.fillStyle = `rgb(${s.color})`;
                        ctx.fill();
                    });

                    // Crosshair
                    if (this.hover) {
                        const hx = xAt(this.hover.index);
                        ctx.setLineDash([3, 3]);
                        ctx.strokeStyle = 'rgba(31, 26, 24, 0.35)';
                        ctx.beginPath();
                        ctx.moveTo(hx, plot.y);
                        ctx.lineTo(hx, plot.y + plot.h + this.volH + 8);
                        ctx.stroke();
                        ctx.setLineDash([]);
                    }

                    // X labels
                    ctx.fillStyle = 'rgba(31, 26, 24, 0.4)';
                    ctx.font = '10px Inter, system-ui, sans-serif';
                    ctx.textAlign = 'center';
                    const step = Math.ceil(this.labels.length / 5);
                    this.labels.forEach((lab, i) => {
                        if (i % step !== 0 && i !== this.labels.length - 1) return;
                        ctx.fillText(lab, xAt(i), cssH - 6);
                    });
                },
            }));
        });
    </script>
    @endpush
</x-admin.layout>
