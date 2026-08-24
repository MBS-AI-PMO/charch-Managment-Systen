<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ChurchBranchRequest;
use App\Models\ChurchBranch;

class ChurchBranchController extends Controller
{
    public function index()
    {
        $branches = ChurchBranch::ordered()->paginate(10);

        return view('admin.churches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.churches.edit', ['branch' => new ChurchBranch()]);
    }

    public function store(ChurchBranchRequest $req)
    {
        $branch = ChurchBranch::create($req->validated());

        return redirect()->route('admin.churches.edit', $branch)->with('success', 'Church branch created.');
    }

    public function edit(ChurchBranch $church)
    {
        return view('admin.churches.edit', ['branch' => $church]);
    }

    public function update(ChurchBranchRequest $req, ChurchBranch $church)
    {
        $church->update($req->validated());

        return redirect()->route('admin.churches.edit', $church)->with('success', 'Church branch updated.');
    }

    public function show(ChurchBranch $church)
    {
        return redirect()->route('admin.churches.edit', $church);
    }

    public function destroy(ChurchBranch $church)
    {
        $church->delete();

        return redirect()->route('admin.churches.index')->with('success', 'Church branch deleted.');
    }
}
