<x-admin.layout title="News categories">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <div class="text-xs text-ink-muted mb-1">
                <a href="{{ route('admin.blog.posts.index') }}" class="hover:underline">News</a>
                <span class="mx-1">/</span>
                <span>Categories</span>
            </div>
            <h1 class="text-2xl font-serif">News categories</h1>
            <p class="text-sm text-ink-muted mt-1">Group updates so readers can browse by theme.</p>
        </div>
        @can('manage-blog')
            <a href="{{ route('admin.blog.categories.create') }}" class="btn-primary text-sm">+ New category</a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] text-xs text-ink-muted">{{ $categories->total() }} categories</div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3">Name</th>
                    <th class="text-left font-medium px-5 py-3">Slug</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($categories as $cat)
                    <tr class="hover:bg-surface/60">
                        <td class="px-5 py-3 font-medium">{{ $cat->name }}</td>
                        <td class="px-5 py-3 text-ink-muted font-mono text-xs">{{ $cat->slug }}</td>
                        <td class="px-5 py-3 text-right">
                            <div class="row-actions">
                                <x-row-action type="edit" href="{{ route('admin.blog.categories.edit', $cat) }}" />
                                @can('manage-blog')
                                    <form method="POST" action="{{ route('admin.blog.categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')">
                                        @csrf @method('DELETE')
                                        <x-row-action type="delete" />
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-10 text-center text-sm text-ink-muted">No categories yet.</td></tr>
                @endforelse
            </tbody>
        </table></div>
        @if($categories->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $categories->links() }}</div>
        @endif
    </div>
</x-admin.layout>
