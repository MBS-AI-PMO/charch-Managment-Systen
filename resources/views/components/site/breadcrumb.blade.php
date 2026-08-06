@props(['trail'=>[]])
<nav class="text-sm text-ink-muted my-4">
  <ol class="flex flex-wrap gap-2">
    @foreach($trail as $i => $item)
      <li>
        @if($i)<span class="mx-1">/</span>@endif
        @if(!empty($item['url']))
          <a href="{{ $item['url'] }}" class="hover:text-brand-primary transition-colors">{{ $item['label'] }}</a>
        @else
          <span class="text-ink">{{ $item['label'] }}</span>
        @endif
      </li>
    @endforeach
  </ol>
</nav>
