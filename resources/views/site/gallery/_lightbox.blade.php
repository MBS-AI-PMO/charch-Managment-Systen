@props(['photos'])

@php
  $items = $photos->getCollection()->map(fn ($p) => [
      'src' => site_img($p->path, 'photo-'.$p->id, 1600, 1200),
      'alt' => $p->alt_text ?: $p->filename,
  ])->values();
@endphp

<div
  x-data="{
    items: @js($items),
    active: null,
    get current() { return this.active === null ? null : this.items[this.active]; },
    open(index) { this.active = index; document.body.classList.add('overflow-hidden'); },
    close() { this.active = null; document.body.classList.remove('overflow-hidden'); },
    next() { if (this.active === null || !this.items.length) return; this.active = (this.active + 1) % this.items.length; },
    prev() { if (this.active === null || !this.items.length) return; this.active = (this.active - 1 + this.items.length) % this.items.length; },
  }"
  class="gallery-photo-grid"
>
  @foreach($photos as $i => $photo)
    <button
      type="button"
      class="gallery-photo-tile reveal delay-{{ ($i % 4) + 1 }}"
      @click="open({{ $i }})"
      aria-label="View {{ $photo->alt_text ?: $photo->filename }}"
    >
      <img
        src="{{ site_img($photo->path, 'photo-'.$photo->id, 800, 800) }}"
        alt="{{ $photo->alt_text ?: $photo->filename }}"
        loading="lazy"
      >
    </button>
  @endforeach

  <template x-teleport="body">
    <div
      x-show="active !== null"
      x-cloak
      class="gallery-lightbox"
      @keydown.escape.window="close()"
      @keydown.arrow-right.window="next()"
      @keydown.arrow-left.window="prev()"
    >
      <div class="gallery-lightbox-backdrop" @click="close()"></div>
      <button type="button" class="gallery-lightbox-close" @click="close()" aria-label="Close">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <button type="button" class="gallery-lightbox-nav is-prev" @click="prev()" aria-label="Previous">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
      </button>
      <figure class="gallery-lightbox-figure" x-show="active !== null" x-transition>
        <img :src="current?.src" :alt="current?.alt || ''">
        <figcaption x-text="current?.alt || ''"></figcaption>
      </figure>
      <button type="button" class="gallery-lightbox-nav is-next" @click="next()" aria-label="Next">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
      </button>
    </div>
  </template>
</div>
