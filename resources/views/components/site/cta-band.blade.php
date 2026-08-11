@props(['heading'=>'','sub'=>null,'cta'=>['url'=>'#','label'=>'Learn more']])
<section class="aurora-bg mt-10 md:mt-16 relative overflow-hidden">
  <div class="max-w-container mx-auto px-4 py-10 md:py-16 text-center relative">
    <h2 class="font-serif text-3xl md:text-4xl leading-tight">{{ $heading }}</h2>
    @if($sub)<p class="mt-3 text-ink-muted max-w-xl mx-auto leading-relaxed">{{ $sub }}</p>@endif
    <a href="{{ $cta['url'] }}" class="btn-primary shine-btn glow-primary mt-6 inline-flex">{{ $cta['label'] }}</a>
  </div>
</section>
