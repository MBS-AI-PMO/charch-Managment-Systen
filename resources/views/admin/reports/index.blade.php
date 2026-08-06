<x-admin.layout title="Reports">
    <h1 class="font-serif text-2xl mb-6">Reports</h1>

    <form method="GET" class="card p-4 mb-6 flex flex-wrap items-end gap-3">
        <label class="flex flex-col text-sm">
            Range
            <select name="preset" class="border rounded p-2">
                @foreach(['7' => 'Last 7 days', '30' => 'Last 30 days', '90' => 'Last 90 days', 'ytd' => 'This year'] as $k => $v)
                    <option value="{{ $k }}" @selected($preset === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </label>
        <label class="flex flex-col text-sm">From <input type="date" name="from" value="{{ request('from') }}" class="border rounded p-2"></label>
        <label class="flex flex-col text-sm">To   <input type="date" name="to"   value="{{ request('to') }}"   class="border rounded p-2"></label>
        <button class="btn-secondary text-sm">Update</button>

        <div class="ml-auto flex gap-2">
            <a href="{{ route('admin.reports.attendance.csv', request()->only(['preset', 'from', 'to'])) }}" class="btn-secondary text-sm">Attendance CSV</a>
            <a href="{{ route('admin.reports.giving.csv',     request()->only(['preset', 'from', 'to'])) }}" class="btn-secondary text-sm">Giving CSV</a>
        </div>
    </form>

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Members</p>
            <p class="text-2xl font-semibold">{{ $memberCount }}</p>
            <p class="text-xs text-green-700">+{{ $memberDelta }} new</p>
        </div>
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Events held</p>
            <p class="text-2xl font-semibold">{{ $eventsHeld }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Avg attendance</p>
            <p class="text-2xl font-semibold">{{ $avgAttendance }}</p>
        </div>
        <div class="card p-4">
            <p class="text-xs uppercase text-ink-muted">Giving</p>
            <p class="text-2xl font-semibold">{{ formatMoney($givingTotal) }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4 mb-6">
        <div class="card p-4">
            <h2 class="font-serif text-lg mb-2">Attendance trend</h2>
            <div class="text-brand-primary"><x-admin.sparkline :data="$perEventAttendance" /></div>
        </div>
        <div class="card p-4">
            <h2 class="font-serif text-lg mb-2">Giving by fund</h2>
            @php $maxFund = $byFund->max('total') ?: 1; @endphp
            <ul class="space-y-2">
                @foreach($byFund as $f)
                    <li class="text-sm">
                        <div class="flex justify-between mb-1"><span>{{ $f['name'] }}</span><span class="font-mono">{{ formatMoney($f['total']) }}</span></div>
                        <div class="h-2 bg-surface rounded">
                            <div class="h-2 bg-brand-secondary rounded" style="width: {{ round(($f['total'] / $maxFund) * 100) }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-4">
        <div class="card p-4">
            <h2 class="font-serif text-lg mb-2">Top events by attendance</h2>
            <ol class="space-y-2 text-sm">
                @foreach($topEvents as $e)
                    <li class="flex justify-between"><span>{{ $e->title }}</span><span class="font-mono">{{ $e->attendances_count }}</span></li>
                @endforeach
            </ol>
        </div>
        <div class="card p-4">
            <h2 class="font-serif text-lg mb-2">Givers</h2>
            <p class="text-sm">Total donors: <strong>{{ $donorCount }}</strong></p>
            <p class="text-xs text-ink-muted mt-1">Names are intentionally hidden here. See <a href="{{ route('admin.tithes.index') }}" class="text-brand-primary">Tithes</a> for full records.</p>
        </div>
    </div>
</x-admin.layout>
