<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\CareRequestRequest;
use App\Mail\CareRequestSubmitted;
use App\Models\CareRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class CareRequestController extends Controller
{
    public function index(Request $req)
    {
        $items = CareRequest::where('user_id', $req->user()->id)
            ->latest()
            ->get();

        return view('member.care.index', compact('items'));
    }

    public function create()
    {
        return view('member.care.create');
    }

    public function store(CareRequestRequest $req)
    {
        $data = $req->validated();
        $u = $req->user();

        $care = CareRequest::create([
            'user_id' => $u->id,
            'category' => $data['category'],
            'message' => $data['message'],
            'share_with_team' => (bool) ($data['share_with_team'] ?? false),
            'status' => 'open',
        ]);

        $recipients = User::role(['Site Admin', 'Prayer Organizer'], 'admin')
            ->pluck('email')
            ->toArray();

        if (!empty($recipients)) {
            try {
                Mail::to($recipients)->send(new CareRequestSubmitted($care));
            } catch (\Throwable $e) {
                logger()->error('Care mail failed', ['err' => $e->getMessage()]);
            }
        }

        return redirect()->route('member.care.thanks');
    }

    public function show(Request $req, CareRequest $care)
    {
        Gate::authorize('view', $care);

        return view('member.care.show', ['care' => $care]);
    }
}
