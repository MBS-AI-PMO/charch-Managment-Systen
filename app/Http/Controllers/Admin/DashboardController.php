<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $kpis = [
            'pages' => Page::count(),
            'posts' => BlogPost::published()->count(),
            'drafts' => BlogPost::where('is_published', false)->count(),
            'upcoming_events' => Event::published()->where('starts_at', '>=', now())->count(),
            'unread_messages' => ContactMessage::whereNull('read_at')->count(),
        ];

        $activity = ActivityLog::with('user')->latest('created_at')->limit(10)->get();
        $chart = $this->buildKpiChart(14);

        return view('admin.dashboard', compact('kpis', 'activity', 'chart'));
    }

    /**
     * Cumulative daily totals for the four dashboard KPI counters (trading-style series).
     *
     * @return array{labels: list<string>, series: list<array{key: string, label: string, color: string, values: list<int>}>, volume: list<int>}
     */
    private function buildKpiChart(int $days): array
    {
        $end = now()->endOfDay();
        $start = now()->subDays($days - 1)->startOfDay();

        $pageStarts = $this->dailyNewCounts(Page::query(), 'created_at', $start, $end);
        $postStarts = $this->dailyNewCounts(
            BlogPost::query()->where('is_published', true)->whereNotNull('published_at'),
            'published_at',
            $start,
            $end
        );
        $eventStarts = $this->dailyNewCounts(Event::query(), 'created_at', $start, $end);
        $messageStarts = $this->dailyNewCounts(ContactMessage::query(), 'created_at', $start, $end);

        $pagesBefore = Page::where('created_at', '<', $start)->count();
        $postsBefore = BlogPost::published()->where('published_at', '<', $start)->count();
        $eventsBefore = Event::where('created_at', '<', $start)->count();
        $messagesBefore = ContactMessage::where('created_at', '<', $start)->count();

        $labels = [];
        $pages = [];
        $posts = [];
        $events = [];
        $messages = [];
        $volume = [];

        $p = $pagesBefore;
        $b = $postsBefore;
        $e = $eventsBefore;
        $m = $messagesBefore;

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->toDateString();
            $labels[] = $day->format('M j');

            $dp = (int) ($pageStarts[$key] ?? 0);
            $db = (int) ($postStarts[$key] ?? 0);
            $de = (int) ($eventStarts[$key] ?? 0);
            $dm = (int) ($messageStarts[$key] ?? 0);

            $p += $dp;
            $b += $db;
            $e += $de;
            $m += $dm;

            $pages[] = $p;
            $posts[] = $b;
            $events[] = $e;
            $messages[] = $m;
            $volume[] = $dp + $db + $de + $dm;
        }

        // Ensure the last point matches live KPI totals when history is sparse.
        $pages[count($pages) - 1] = max(end($pages), (int) Page::count());
        $posts[count($posts) - 1] = max(end($posts), (int) BlogPost::published()->count());
        $events[count($events) - 1] = max(end($events), (int) Event::count());
        $messages[count($messages) - 1] = max(end($messages), (int) ContactMessage::count());

        return [
            'labels' => $labels,
            'series' => [
                [
                    'key' => 'pages',
                    'label' => 'Pages',
                    'color' => '122, 31, 43',
                    'values' => $pages,
                ],
                [
                    'key' => 'posts',
                    'label' => 'News',
                    'color' => '201, 169, 97',
                    'values' => $posts,
                ],
                [
                    'key' => 'events',
                    'label' => 'Events',
                    'color' => '4, 120, 87',
                    'values' => $events,
                ],
                [
                    'key' => 'messages',
                    'label' => 'Messages',
                    'color' => '29, 78, 216',
                    'values' => $messages,
                ],
            ],
            'volume' => $volume,
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return array<string, int>
     */
    private function dailyNewCounts($query, string $column, Carbon $start, Carbon $end): array
    {
        $driver = DB::connection()->getDriverName();
        $dateExpr = match ($driver) {
            'sqlite' => "strftime('%Y-%m-%d', {$column})",
            'pgsql' => "to_char({$column}, 'YYYY-MM-DD')",
            default => "DATE({$column})",
        };

        return $query
            ->whereBetween($column, [$start, $end])
            ->selectRaw("{$dateExpr} as day, COUNT(*) as aggregate")
            ->groupByRaw($dateExpr)
            ->pluck('aggregate', 'day')
            ->map(fn ($v) => (int) $v)
            ->all();
    }
}
