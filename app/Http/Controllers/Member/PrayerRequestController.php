<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\PrayerRequestRequest;
use App\Models\PrayerRequest;
use App\Models\PrayerRequestPray;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PrayerRequestController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $req)
    {
        $mine = PrayerRequest::where('user_id', $req->user()->id)
            ->latest()
            ->paginate(10, ['*'], 'mine_page');

        $community = PrayerRequest::public()
            ->where('user_id', '!=', $req->user()->id)
            ->latest()
            ->paginate(10, ['*'], 'community_page');

        return view('member.prayer.index', compact('mine', 'community'));
    }

    public function create()
    {
        return view('member.prayer.create');
    }

    public function store(PrayerRequestRequest $req)
    {
        $data = $req->validated();
        $u = $req->user();

        PrayerRequest::create([
            'user_id' => $u->id,
            'name' => $u->name,
            'title' => $data['title'],
            'body' => $data['body'],
            'is_public' => (bool) ($data['is_public'] ?? false),
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
            'status' => 'pending',
        ]);

        return redirect()->route('member.prayer.index')->with('success', 'Prayer request submitted.');
    }

    public function show(PrayerRequest $prayer)
    {
        $this->authorize('view', $prayer);

        return view('member.prayer.show', ['prayer' => $prayer]);
    }

    public function destroy(PrayerRequest $prayer)
    {
        $this->authorize('delete', $prayer);

        $prayer->delete();

        return redirect()->route('member.prayer.index')->with('success', 'Prayer request removed.');
    }

    public function togglePray(Request $req, PrayerRequest $prayer)
    {
        abort_unless($prayer->is_public, 404);

        $existing = PrayerRequestPray::where('prayer_request_id', $prayer->id)
            ->where('user_id', $req->user()->id)
            ->first();

        if ($existing) {
            $existing->delete();

            return back()->with('success', 'Removed from your prayers.');
        }

        PrayerRequestPray::create([
            'prayer_request_id' => $prayer->id,
            'user_id' => $req->user()->id,
        ]);

        return back()->with('success', 'Added to your prayers.');
    }
}
