@php
    $isActive = isset($currentFolder) && $currentFolder && $currentFolder->id === $folder->id;
    $pickerQuery = ($picker ?? false) ? ['folder' => $folder->id, 'picker' => 1] : ['folder' => $folder->id];
@endphp
<a href="{{ route('admin.media.index', $pickerQuery) }}"
   @class(['admin-filter-chip', 'is-active' => $isActive])>
    {{ $folder->name }}
</a>
@foreach($folder->children as $child)
    @include('admin.media._folder-chip', ['folder' => $child, 'depth' => $depth + 1])
@endforeach
