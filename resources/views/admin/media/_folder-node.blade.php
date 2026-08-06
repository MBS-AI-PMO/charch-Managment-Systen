@php
    $isActive = isset($currentFolder) && $currentFolder && $currentFolder->id === $folder->id;
    $pickerQuery = ($picker ?? false) ? ['folder' => $folder->id, 'picker' => 1] : ['folder' => $folder->id];
@endphp
<li>
    <a href="{{ route('admin.media.index', $pickerQuery) }}"
       style="padding-left: {{ ($depth * 12) + 8 }}px;"
       class="block py-1 pr-2 rounded text-sm {{ $isActive ? 'bg-brand-primary/10 text-brand-primary' : 'text-ink-muted hover:bg-surface' }}">
        <span class="inline-block w-3 text-ink-muted/60">{{ $folder->children->count() ? '▾' : '·' }}</span>
        {{ $folder->name }}
    </a>
    @if($folder->children->count())
        <ul>
            @foreach($folder->children as $child)
                @include('admin.media._folder-node', ['folder' => $child, 'depth' => $depth + 1])
            @endforeach
        </ul>
    @endif
</li>
