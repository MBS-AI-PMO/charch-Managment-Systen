@php
    $children = $byParent->get($item->id, collect());
@endphp
<li class="menu-node" data-id="{{ $item->id }}">
    <div class="menu-row">
        <span class="menu-handle" aria-label="Drag to reorder" title="Drag to reorder">&#x2630;</span>
        <span class="text-ink-muted text-xs w-6 text-center">{{ $item->sort_order }}</span>
        <div class="flex-1 min-w-0">
            <div class="text-sm font-medium truncate">{{ $item->label }}</div>
            <div class="text-xs text-ink-muted font-mono truncate">{{ $item->link_type }}: {{ $item->link_value }}</div>
        </div>
        <div class="row-actions">
            <x-row-action type="edit" href="{{ route('admin.items.edit', $item) }}" />
            @can('manage-menus')
                <form method="POST" action="{{ route('admin.items.destroy', $item) }}" onsubmit="return confirm('Remove this item?')">
                    @csrf @method('DELETE')
                    <x-row-action type="remove" />
                </form>
            @endcan
        </div>
    </div>
    <ul class="menu-children">
        @foreach($children as $child)
            @include('admin.menus._tree_item', ['item' => $child, 'byParent' => $byParent])
        @endforeach
    </ul>
</li>
