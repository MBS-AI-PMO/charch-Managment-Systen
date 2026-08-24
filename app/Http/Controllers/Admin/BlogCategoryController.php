<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogCategoryRequest;
use App\Models\BlogCategory;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::orderBy('name')->paginate(10);
        return view('admin.blog.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog.categories.edit', ['category' => new BlogCategory()]);
    }

    public function store(BlogCategoryRequest $req)
    {
        BlogCategory::create($req->validated());
        return redirect()->route('admin.blog.categories.index')->with('success', 'Category created.');
    }

    public function edit(BlogCategory $category)
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    public function update(BlogCategoryRequest $req, BlogCategory $category)
    {
        $category->update($req->validated());
        return redirect()->route('admin.blog.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(BlogCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.blog.categories.index')->with('success', 'Category deleted.');
    }

    public function show(BlogCategory $category)
    {
        return redirect()->route('admin.blog.categories.edit', $category);
    }
}
