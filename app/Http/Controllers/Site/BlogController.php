<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Page;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $page = Page::where('slug', 'blog')->published()->first();
        $categories = BlogCategory::orderBy('name')->get();

        $query = BlogPost::published()->with(['category', 'author'])->latest('published_at');

        if ($slug = $request->query('category')) {
            $cat = BlogCategory::where('slug', $slug)->first();
            if ($cat) {
                $query->where('category_id', $cat->id);
            }
        }

        $posts = $query->paginate(12)->withQueryString();
        $activeCategory = $request->query('category');

        return view('site.blog.index', compact('page', 'posts', 'categories', 'activeCategory'));
    }

    public function show(BlogPost $post)
    {
        abort_unless($post->is_published && $post->published_at && $post->published_at->lte(now()), 404);

        $post->load(['category', 'author']);

        $related = BlogPost::published()
            ->with(['category', 'author'])
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('site.blog.show', compact('post', 'related'));
    }
}
