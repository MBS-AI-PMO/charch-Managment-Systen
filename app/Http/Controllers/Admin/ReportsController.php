<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        $cacheKey = "reports.{$request->user()->id}.{$from->toDateString()}.{$to->toDateString()}";

        $data = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($from, $to) {
            $memberCount = User::where('is_admin', false)->count();
            $memberDelta = User::where('is_admin', false)
                ->whereBetween('created_at', [$from, $to])->count();

            $eventsHeld = Event::whereBetween('starts_at', [$from, $to])->count();
            $avgAttendance = (int) round(
                EventAttendance::whereHas('event', fn ($q) => $q->whereBetween('starts_at', [$from, $to]))
                    ->count() / max(1, $eventsHeld)
            );

            $givingTotal = (int) Tithe::whereBetween('received_at', [$from->toDateString(), $to->toDateString()])
                ->sum('amount_cents');

            $byFund = Tithe::selectRaw('fund_id, SUM(amount_cents) AS total')
                ->whereBetween('received_at', [$from->toDateString(), $to->toDateString()])
                ->groupBy('fund_id')
                ->with('fund')
                ->get()
                ->map(fn ($r) => ['name' => $r->fund?->name ?? '—', 'total' => (int) $r->total])
                ->sortByDesc('total')->values();

            $topEvents = Event::withCount('attendances')
                ->whereBetween('starts_at', [$from, $to])
                ->orderByDesc('attendances_count')
                ->limit(5)->get(['id', 'title', 'starts_at']);

            $donorCount = Tithe::whereBetween('received_at', [$from->toDateString(), $to->toDateString()])
                ->distinct()
                ->count('user_id');

            $perEventAttendance = Event::withCount('attendances')
                ->whereBetween('starts_at', [$from, $to])
                ->orderBy('starts_at')
                ->get(['id', 'title', 'starts_at'])
                ->map(fn ($e) => ['label' => $e->starts_at?->format('M j'), 'value' => $e->attendances_count])
                ->all();

            return compact(
                'memberCount', 'memberDelta', 'eventsHeld', 'avgAttendance',
                'givingTotal', 'byFund', 'topEvents', 'donorCount', 'perEventAttendance'
            );
        });

        return view('admin.reports.index', array_merge($data, [
            'from' => $from, 'to' => $to,
            'preset' => $request->input('preset', '30'),
        ]));
    }

    public function attendanceCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Event', 'Date', 'RSVPs', 'Checked In', 'Walk-ins']);
            foreach (Event::with(['rsvps', 'attendances'])
                ->whereBetween('starts_at', [$from, $to])
                ->orderBy('starts_at')->cursor() as $e) {
                fputcsv($out, [
                    $e->title,
                    $e->starts_at?->toDateString(),
                    $e->rsvps->where('status', 'going')->count(),
                    $e->attendances->whereNotNull('user_id')->count(),
                    $e->attendances->whereNull('user_id')->count(),
                ]);
            }
            fclose($out);
        }, "attendance-{$from->toDateString()}-{$to->toDateString()}.csv", ['Content-Type' => 'text/csv']);
    }

    public function givingCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->range($request);

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Fund', 'Method', 'Amount']);
            foreach (Tithe::with('fund')
                ->whereBetween('received_at', [$from->toDateString(), $to->toDateString()])
                ->orderBy('received_at')->cursor() as $t) {
                fputcsv($out, [
                    $t->received_at->toDateString(),
                    $t->fund->name,
                    $t->method,
                    number_format($t->amount_cents / 100, 2),
                ]);
            }
            fclose($out);
        }, "giving-{$from->toDateString()}-{$to->toDateString()}.csv", ['Content-Type' => 'text/csv']);
    }

    private function range(Request $request): array
    {
        $preset = $request->input('preset', '30');
        $to = $request->filled('to') ? \Carbon\Carbon::parse($request->input('to'))->endOfDay() : now()->endOfDay();
        $from = $request->filled('from')
            ? \Carbon\Carbon::parse($request->input('from'))->startOfDay()
            : (match ($preset) {
                '7' => now()->subDays(7)->startOfDay(),
                '30' => now()->subDays(30)->startOfDay(),
                '90' => now()->subDays(90)->startOfDay(),
                'ytd' => now()->startOfYear(),
                default => now()->subDays(30)->startOfDay(),
            });

        return [$from, $to];
    }
}
