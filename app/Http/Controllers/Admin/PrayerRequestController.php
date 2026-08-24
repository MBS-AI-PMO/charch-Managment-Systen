<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PrayerRequestUpdateRequest;
use App\Models\PrayerRequest;
use App\Models\User;
use Illuminate\Http\Request;

class PrayerRequestController extends Controller
{
    public function index(Request $req)
    {
        $q = PrayerRequest::query()->latest();

        if ($req->filled('status')) {
            $q->where('status', $req->status);
        }
        if ($req->filled('search')) {
            $q->where('title', 'like', '%'.$req->search.'%');
        }

        return view('admin.prayer-requests.index', [
            'items' => $q->paginate(10)->withQueryString(),
        ]);
    }

    public function show(PrayerRequest $prayerRequest)
    {
        $assignees = User::where('is_admin', true)->orderBy('name')->get();

        return view('admin.prayer-requests.show', [
            'p' => $prayerRequest,
            'assignees' => $assignees,
        ]);
    }

    public function update(PrayerRequestUpdateRequest $req, PrayerRequest $prayerRequest)
    {
        $data = $req->validated();

        if (!empty($data['admin_notes'])) {
            $data['admin_notes'] = clean($data['admin_notes'], 'cms');
        }

        $prayerRequest->update($data);

        return back()->with('success', 'Prayer request updated.');
    }

    public function destroy(PrayerRequest $prayerRequest)
    {
        $prayerRequest->delete();

        return redirect()->route('admin.prayer-requests.index')->with('success', 'Deleted.');
    }
}
