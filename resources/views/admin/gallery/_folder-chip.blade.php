@php
    $active = $currentFolder && (int) $currentFolder->id === (int) $folder->id;
@endphp
<a href="{{ route('admin.gallery.index', ['folder' => $folder->id]) }}"
   @class(['admin-filter-chip', 'is-active' => $active])
   style="{{ $depth > 0 ? 'margin-left: '.($depth * 0.15).'rem' : '' }}">
    {{ $folder->name }}
</a>
@foreach($folder->children as $child)
    @include('admin.gallery._folder-chip', ['folder' => $child, 'depth' => $depth + 1])
@endforeach
