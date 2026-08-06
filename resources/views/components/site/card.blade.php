@props(['image'=>null,'eyebrow'=>null,'title'=>'','meta'=>null,'href'=>'#'])
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'card lift-card overflow-hidden block']) }}>
  @if($image)
    <div class="aspect-[16/10] overflow-hidden">
      <div class="lift-image w-full h-full bg-cover bg-center" style="background-image:url('{{ $image }}')"></div>
    </div>
  @endif
  <div class="p-5">
    @if($eyebrow)<div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-2">{{ $eyebrow }}</div>@endif
    <h3 class="font-serif text-xl leading-snug">{{ $title }}</h3>
    @if($meta)<div class="text-sm text-ink-muted mt-2">{{ $meta }}</div>@endif
    {{ $slot ?? '' }}
  </div>
</a>
