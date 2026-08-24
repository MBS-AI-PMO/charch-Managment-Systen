<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Services\BlogPostPublisher;
use Illuminate\Http\Request;

class BlogPostController extends Controller
{
    public function index(Request $req)
    {
        $q = BlogPost::with(['category', 'author']);
        if ($s = $req->query('search')) {
            $q->where('title', 'like', "%$s%");
        }
        if ($status = $req->query('status')) {
            if ($status === 'published') $q->where('is_published', true);
            if ($status === 'draft') $q->where('is_published', false);
        }
        if ($cat = $req->query('category')) {
            $q->where('category_id', $cat);
        }
        $posts = $q->latest('updated_at')->paginate(10)->withQueryString();
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $post = new BlogPost();
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.posts.edit', compact('post', 'categories'));
    }

    public function store(BlogPostRequest $req, BlogPostPublisher $svc)
    {
        $data = $req->validated();
        if ($req->hasFile('featured_image_file')) {
            $data['featured_image_path'] = $req->file('featured_image_file')->store('uploads/blog', 'public');
        }
        unset($data['featured_image_file']);

        $post = new BlogPost();
        $svc->save($post, $data, $req->user('admin'));
        return redirect()->route('admin.blog.posts.edit', $post)->with('success', 'Post created.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.posts.edit', compact('post', 'categories'));
    }

    public function update(BlogPostRequest $req, BlogPost $post, BlogPostPublisher $svc)
    {
        $data = $req->validated();
        if ($req->hasFile('featured_image_file')) {
            $data['featured_image_path'] = $req->file('featured_image_file')->store('uploads/blog', 'public');
        }
        unset($data['featured_image_file']);

        $svc->save($post, $data, $req->user('admin'));
        return redirect()->route('admin.blog.posts.edit', $post)->with('success', 'Post updated.');
    }

    public function show(BlogPost $post)
    {
        return redirect()->route('admin.blog.posts.edit', $post);
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->route('admin.blog.posts.index')->with('success', 'Post deleted.');
    }
}
