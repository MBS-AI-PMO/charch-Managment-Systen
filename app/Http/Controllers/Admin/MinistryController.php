<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\MinistryRequest;
use App\Models\Ministry;
use Illuminate\Http\Request;

class MinistryController extends Controller
{
    public function index()
    {
        $ministries = Ministry::orderBy('sort_order')->orderBy('name')->paginate(10);
        return view('admin.ministries.index', compact('ministries'));
    }

    public function create()
    {
        return view('admin.ministries.edit', ['ministry' => new Ministry()]);
    }

    public function store(MinistryRequest $req)
    {
        $data = $req->validated();
        if ($req->hasFile('cover_image_file')) {
            $data['cover_image_path'] = $req->file('cover_image_file')->store('uploads/ministries', 'public');
        }
        unset($data['cover_image_file']);

        $ministry = Ministry::create($data);
        return redirect()->route('admin.ministries.edit', $ministry)->with('success', 'Ministry created.');
    }

    public function edit(Ministry $ministry)
    {
        return view('admin.ministries.edit', compact('ministry'));
    }

    public function update(MinistryRequest $req, Ministry $ministry)
    {
        $data = $req->validated();
        if ($req->hasFile('cover_image_file')) {
            $data['cover_image_path'] = $req->file('cover_image_file')->store('uploads/ministries', 'public');
        }
        unset($data['cover_image_file']);

        $ministry->update($data);
        return redirect()->route('admin.ministries.edit', $ministry)->with('success', 'Ministry updated.');
    }

    public function show(Ministry $ministry)
    {
        return redirect()->route('admin.ministries.edit', $ministry);
    }

    public function destroy(Ministry $ministry)
    {
        $ministry->delete();
        return redirect()->route('admin.ministries.index')->with('success', 'Ministry deleted.');
    }

    public function reorder(Request $req)
    {
        $ids = (array) $req->input('ids', []);
        foreach ($ids as $i => $id) {
            Ministry::where('id', $id)->update(['sort_order' => $i]);
        }
        return response()->json(['ok' => true]);
    }
}
