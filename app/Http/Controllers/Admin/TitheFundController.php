<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TitheFund;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TitheFundController extends Controller
{
    public function index()
    {
        return view('admin.tithes.funds.index', [
            'funds' => TitheFund::orderBy('sort_order')->orderBy('name')->paginate(10),
        ]);
    }

    public function create()
    {
        return view('admin.tithes.funds.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:tithe_funds,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        TitheFund::create($data);

        return redirect()->route('admin.tithes.funds.index')->with('status', 'Fund created.');
    }

    public function edit(TitheFund $fund)
    {
        return view('admin.tithes.funds.edit', compact('fund'));
    }

    public function update(Request $request, TitheFund $fund)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:tithe_funds,name,'.$fund->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $fund->update($data);

        return redirect()->route('admin.tithes.funds.index')->with('status', 'Fund updated.');
    }

    public function destroy(TitheFund $fund)
    {
        if ($fund->tithes()->exists()) {
            $fund->update(['is_active' => false]);

            return back()->with('status', 'Fund deactivated (preserves historical records).');
        }

        $fund->delete();

        return back()->with('status', 'Fund deleted.');
    }
}
