<x-admin.layout title="Community Feed">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Community feed</h1>
            <p class="text-sm text-ink-muted mt-1">Publish posts that appear in every member's feed and dashboard.</p>
        </div>
        <a href="{{ route('admin.feed.create') }}" class="btn-primary text-sm">+ New post</a>
    </div>

    <div class="card overflow-hidden">
        <div class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center justify-between text-xs text-ink-muted">
            <span>{{ $posts->total() }} {{ \Illuminate\Support\Str::plural('post', $posts->total()) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                    <tr>
                        <th class="text-left font-medium px-5 py-3">Title</th>
                        <th class="text-left font-medium px-5 py-3">Author</th>
                        <th class="text-left font-medium px-5 py-3">Published</th>
                        <th class="text-left font-medium px-5 py-3">Reactions</th>
                        <th class="text-left font-medium px-5 py-3">Pinned</th>
                        <th class="text-right font-medium px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[rgb(var(--border))]">
                    @forelse($posts as $post)
                        <tr class="hover:bg-surface/60">
                            <td class="px-5 py-3 font-medium">
                                <a href="{{ route('admin.feed.edit', $post) }}" class="hover:text-brand-primary">
                                    {{ $post->title ?: '(no title)' }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-ink-muted">{{ $post->author?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $post->published_at?->diffForHumans() ?? '—' }}</td>
                            <td class="px-5 py-3 text-ink-muted">{{ $post->reactions_count ?? 0 }}</td>
                            <td class="px-5 py-3">
                                @if($post->pinned)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-brand-primary/10 text-brand-primary">📌 Pinned</span>
                                @else
                                    <span class="text-ink-muted text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="row-actions">
                                    <x-row-action type="edit" href="{{ route('admin.feed.edit', $post) }}" />
                                    <form method="POST" action="{{ route('admin.feed.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-row-action type="delete" />
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">No posts yet. <a href="{{ route('admin.feed.create') }}" class="text-brand-primary hover:underline">Publish the first one →</a></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $posts->links() }}</div>
        @endif
    </div>
</x-admin.layout>
