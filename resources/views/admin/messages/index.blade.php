<x-admin.layout title="Messages">
    <div class="mb-6 flex items-end justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-serif">Inbox</h1>
            <p class="text-sm text-ink-muted mt-1">
                Contact form &amp; Ask a question submissions.
                <span class="font-medium">{{ $unreadCount }}</span> unread ·
                <span class="font-medium">{{ $awaitingCount }}</span> awaiting reply.
            </p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <form method="GET" class="px-5 py-3 border-b border-[rgb(var(--border))] flex items-center gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}" class="input w-64" placeholder="Search name, email, subject…">
            <select name="status" class="input w-40" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="unread" @selected(request('status') === 'unread')>Unread</option>
                <option value="read" @selected(request('status') === 'read')>Read</option>
                <option value="awaiting" @selected(request('status') === 'awaiting')>Awaiting reply</option>
                <option value="replied" @selected(request('status') === 'replied')>Replied</option>
            </select>
            <button type="submit" class="btn-ghost text-sm">Filter</button>
            <span class="ml-auto text-xs text-ink-muted">{{ $messages->total() }} messages</span>
        </form>

        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-surface text-ink-muted text-xs uppercase tracking-wider">
                <tr>
                    <th class="text-left font-medium px-5 py-3 w-2"></th>
                    <th class="text-left font-medium px-5 py-3">From</th>
                    <th class="text-left font-medium px-5 py-3">Subject</th>
                    <th class="text-left font-medium px-5 py-3">Status</th>
                    <th class="text-left font-medium px-5 py-3">Received</th>
                    <th class="text-right font-medium px-5 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[rgb(var(--border))]">
                @forelse($messages as $msg)
                    <tr class="hover:bg-surface/60 {{ is_null($msg->read_at) ? 'font-medium bg-blue-50/30' : '' }}">
                        <td class="px-5 py-3">
                            @if(is_null($msg->read_at))
                                <span class="w-2 h-2 rounded-full bg-blue-500 inline-block" title="Unread"></span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <div>{{ $msg->name }}</div>
                            <div class="text-xs text-ink-muted font-normal">{{ $msg->email }}</div>
                        </td>
                        <td class="px-5 py-3">{{ $msg->subject ?? '—' }}</td>
                        <td class="px-5 py-3 font-normal">
                            @if($msg->replied_at)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-700">Replied</span>
                                @if($msg->is_public)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-blue-50 text-blue-700 ml-1">Q&A</span>
                                @endif
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-amber-50 text-amber-700">Awaiting</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-ink-muted font-normal">{{ $msg->created_at?->diffForHumans() }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.messages.show', $msg) }}" class="text-xs text-brand-primary hover:underline">Open</a>
                            @can('manage-messages')
                                <form method="POST" action="{{ route('admin.messages.destroy', $msg) }}" class="inline ml-3" onsubmit="return confirm('Delete this message?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-600 hover:underline">Delete</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-ink-muted">Inbox is empty.</td></tr>
                @endforelse
            </tbody>
        </table></div>

        @if($messages->hasPages())
            <div class="px-5 py-3 border-t border-[rgb(var(--border))]">{{ $messages->links() }}</div>
        @endif
    </div>
</x-admin.layout>
