<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PageRequest;
use App\Models\Page;
use App\Services\PagePublisher;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::with('revisions.user')->orderBy('title')->paginate(10);
        return view('admin.pages.index', compact('pages'));
    }

    public function edit(Page $page)
    {
        $page->load('revisions');
        return view('admin.pages.edit', compact('page'));
    }

    public function update(PageRequest $req, Page $page, PagePublisher $svc)
    {
        $data = $req->validated();

        if ($req->hasFile('hero_image_file')) {
            $path = $req->file('hero_image_file')->store('uploads/pages', 'public');
            $data['hero_image_path'] = $path;
        }
        unset($data['hero_image_file']);

        $svc->save($page, $data, $req->user('admin'));

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page updated.');
    }

    public function store(PageRequest $req, PagePublisher $svc)
    {
        $data = $req->validated();
        if ($req->hasFile('hero_image_file')) {
            $data['hero_image_path'] = $req->file('hero_image_file')->store('uploads/pages', 'public');
        }
        unset($data['hero_image_file']);

        $page = new Page();
        $svc->save($page, $data, $req->user('admin'));

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page created.');
    }
}
