@props(['eyebrow'=>null,'heading'=>'','lede'=>null,'align'=>'left'])
<div class="@if($align==='center') text-center mx-auto max-w-2xl @endif mb-10">
  @if($eyebrow)<div class="uppercase tracking-[0.18em] text-xs text-brand-primary font-semibold mb-2">{{ $eyebrow }}</div>@endif
  <h2 class="font-serif text-3xl md:text-4xl leading-tight">{{ $heading }}</h2>
  @if($lede)<p class="mt-3 text-ink-muted text-lg leading-relaxed">{{ $lede }}</p>@endif
</div>
