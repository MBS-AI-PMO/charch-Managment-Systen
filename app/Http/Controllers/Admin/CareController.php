<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CareUpdateRequest;
use App\Models\CareRequest;
use Illuminate\Http\Request;

class CareController extends Controller
{
    public function index(Request $req)
    {
        $q = CareRequest::query()->with('user')->latest();

        if ($req->filled('status')) {
            $q->where('status', $req->status);
        }
        if ($req->filled('category')) {
            $q->where('category', $req->category);
        }

        return view('admin.care.index', [
            'items' => $q->paginate(20)->withQueryString(),
        ]);
    }

    public function show(CareRequest $care)
    {
        return view('admin.care.show', ['care' => $care]);
    }

    public function update(CareUpdateRequest $req, CareRequest $care)
    {
        $data = $req->validated();

        if (!empty($data['response_notes'])) {
            $data['response_notes'] = clean($data['response_notes'], 'cms');
        }

        if ($care->status === 'open' && $data['status'] !== 'open') {
            $data['responder_id'] = $req->user('admin')->id;
        }

        if ($data['status'] === 'closed') {
            $data['closed_at'] = now();
        }

        $care->update($data);

        return back()->with('success', 'Knock for Help updated.');
    }
}
