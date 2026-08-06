<x-admin.layout title="News">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">News</h1>
            <p class="text-sm text-ink-muted mt-1">Publish church news and updates for your congregation.</p>
        </div>
        @can('manage-blog')
            <a href="{{ route('admin.blog.posts.create') }}" class="btn-primary text-sm">+ New post</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <form method="GET" class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" class="input w-64" placeholder="Search posts…">
            <select name="status" class="input w-40" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <option value="published" @selected(request('status')==='published')>Published</option>
                <option value="draft" @selected(request('status')==='draft')>Draft</option>
            </select>
            <select name="category" class="input w-40" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected((string) request('category') === (string) $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-ghost text-sm">Filter</button>
            <span class="ml-auto text-xs text-ink-muted">{{ $posts->total() }} posts</span>
        </form>

        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Title</th>
                    <th class="text-left font-medium px-5 py-3">Category</th>
                    <th class="text-left font-medium px-5 py-3">Author</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-left font-medium px-5 py-3">Published</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($posts as $post)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 font-medium">{{ $post->title }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $post->category?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-ink-muted">{{ $post->author?->name ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($post->is_published)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Published</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-muted">{{ $post->published_at?->format('M j, Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.blog.posts.edit', $post) }}" class="text-xs text-brand-primary hover:underline">Edit</a>
                            @can('manage-blog')
                                <form method="POST" action="{{ route('admin.blog.posts.destroy', $post) }}" class="inline ml-3" onsubmit="return confirm('Delete this post?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600 hover:underline">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">No posts yet. <a href="{{ route('admin.blog.posts.create') }}" class="text-brand-primary hover:underline">Write your first one</a>.</td></tr>
                @endforelse
            </tbody>
        </table></div>

        @if($posts->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $posts->links() }}</div>
        @endif
    </div>
</x-admin.layout>
