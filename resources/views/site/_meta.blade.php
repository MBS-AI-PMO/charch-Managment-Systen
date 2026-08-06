@push('head')
  @if(! empty($page))
    @if($page->meta_description)
      <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @if($page->meta_title)
      <meta property="og:title" content="{{ $page->meta_title }}">
    @endif
    @if($page->meta_description)
      <meta property="og:description" content="{{ $page->meta_description }}">
    @endif
  @endif
@endpush
