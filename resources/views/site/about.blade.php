@extends('layouts.site')

@section('title', $page->meta_title ?: ($page->title.settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page->hero_image_path, 'about-hero-'.$page->id, 1800, 700);
  $storyImg = site_img(settings('about.story_image', 'uploads/pages/yp0EeGYMexBpkizJSy5KhUkpwljsA5xXk51ZQABI.jpg'), 'about-story-'.$page->id, 1400, 900);
  $brand = settings('brand.name', 'Assemblies of God');
  $tagline = settings('brand.tagline');

  $aboutIntroHtml = '';
  $aboutSections = [];
  $aboutGallery = [];
  $aboutParsed = false;

  $aboutFixText = function (?string $text): string {
      $text = (string) $text;
      // Common UTF-8 mojibake from DOMDocument / double-encoding
      $text = strtr($text, [
          'â' => '–',
          'â' => '—',
          'â' => '’',
          'â' => '‘',
          'â' => '“',
          'â' => '”',
          'â¦' => '…',
          "Â" => '',
      ]);

      return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
  };

  $aboutSplitItem = function (string $text) use ($aboutFixText): array {
      $text = $aboutFixText($text);
      $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
      $label = $text;
      $detail = '';

      if (preg_match('/^(.*?)\s*(?:[–—−]|-|:)\s+(.+)$/u', $text, $m)) {
          $label = trim($m[1]);
          $detail = trim($m[2]);
      }

      return [
          'label' => rtrim($aboutFixText($label), ':'),
          'detail' => $aboutFixText($detail),
      ];
  };

  $rawBody = trim((string) ($page->body ?? ''));
  if ($rawBody !== '') {
      $dom = new DOMDocument('1.0', 'UTF-8');
      libxml_use_internal_errors(true);
      $dom->loadHTML(
          '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body><div id="about-root">'.$rawBody.'</div></body></html>',
          LIBXML_NOERROR | LIBXML_NOWARNING
      );
      libxml_clear_errors();

      $root = $dom->getElementById('about-root');
      if ($root) {
          $current = [
              'title' => null,
              'kind' => 'intro',
              'html' => '',
              'items' => [],
              'images' => [],
          ];

          $flush = function () use (&$current, &$aboutIntroHtml, &$aboutSections) {
              if ($current['kind'] === 'intro') {
                  $aboutIntroHtml .= $current['html'];
              } elseif (! empty($current['items']) || trim(strip_tags($current['html'])) !== '' || ! empty($current['images'])) {
                  $aboutSections[] = $current;
              }

              $current = [
                  'title' => null,
                  'kind' => 'block',
                  'html' => '',
                  'items' => [],
                  'images' => [],
              ];
          };

          $captureImage = function (DOMElement $img) use (&$current, &$aboutGallery, $brand, $aboutFixText): void {
              $src = trim((string) $img->getAttribute('src'));
              if ($src === '') {
                  return;
              }

              $photo = [
                  'src' => $src,
                  'alt' => $aboutFixText(trim((string) $img->getAttribute('alt')) ?: $brand),
              ];

              $current['images'][] = $photo;
              $aboutGallery[] = $photo;
          };

          foreach (iterator_to_array($root->childNodes) as $node) {
              if ($node->nodeType === XML_TEXT_NODE && trim($node->textContent) === '') {
                  continue;
              }

              $tag = strtolower($node->nodeName ?? '');

              if (in_array($tag, ['h2', 'h3', 'h4'], true)) {
                  $flush();
                  $title = $aboutFixText(trim($node->textContent));
                  $kind = 'block';
                  if (stripos($title, 'ministr') !== false) {
                      $kind = 'ministries';
                  } elseif (stripos($title, 'schedule') !== false || stripos($title, 'service times') !== false) {
                      $kind = 'schedule';
                  }
                  $current = [
                      'title' => $title,
                      'kind' => $kind,
                      'html' => '',
                      'items' => [],
                      'images' => [],
                  ];
                  continue;
              }

              if ($tag === 'ul' || $tag === 'ol') {
                  foreach (iterator_to_array($node->childNodes) as $li) {
                      if (strtolower($li->nodeName ?? '') !== 'li') {
                          continue;
                      }

                      foreach (iterator_to_array($li->getElementsByTagName('img')) as $img) {
                          $captureImage($img);
                          $img->parentNode?->removeChild($img);
                      }

                      $text = trim(preg_replace('/\s+/u', ' ', $li->textContent ?? '') ?? '');
                      if ($text === '') {
                          continue;
                      }

                      $current['items'][] = $aboutSplitItem($text);
                  }
                  continue;
              }

              if ($tag === 'img' && $node instanceof DOMElement) {
                  $captureImage($node);
                  continue;
              }

              if ($tag === 'p' && $node instanceof DOMElement && $node->getElementsByTagName('img')->length) {
                  foreach (iterator_to_array($node->getElementsByTagName('img')) as $img) {
                      $captureImage($img);
                      $img->parentNode?->removeChild($img);
                  }
                  $textLeft = trim(preg_replace('/\s+/u', ' ', $node->textContent ?? '') ?? '');
                  if ($textLeft !== '') {
                      $current['html'] .= $aboutFixText($dom->saveHTML($node) ?: '');
                  }
                  continue;
              }

              $current['html'] .= $aboutFixText($dom->saveHTML($node) ?: '');
          }

          $flush();
          $aboutIntroHtml = $aboutFixText($aboutIntroHtml);
          $aboutParsed = $aboutIntroHtml !== '' || count($aboutSections) > 0;
      }
  }

  $aboutShownImageSrcs = [];
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Our story"
    :heading="$page->hero_heading ?: $page->title"
    :sub="$page->hero_subheading"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'About'],
    ]" />
  </div>

  <section class="about-page">
    <div class="about-page-bg" aria-hidden="true"></div>

    <div class="relative max-w-container mx-auto px-4 sm:px-6 py-10 md:py-14 space-y-10 md:space-y-14">
      @if($aboutParsed)
        <div class="about-lead reveal">
          <figure class="about-lead-media">
            <img src="{{ $storyImg }}" alt="{{ $brand }}" loading="lazy" decoding="async">
            <figcaption class="about-lead-badge">
              <span class="about-lead-badge-name">{{ $brand }}</span>
              @if($tagline)
                <span class="about-lead-badge-tag">{{ $tagline }}</span>
              @endif
            </figcaption>
          </figure>

          <div class="about-lead-copy">
            <p class="about-kicker">About us</p>
            <h2 class="about-title">{{ $page->title }}</h2>
            <div class="about-lead-text">
              {!! site_render_html($aboutIntroHtml) !!}
            </div>
          </div>
        </div>

        @foreach($aboutSections as $i => $section)
          @if($section['kind'] === 'ministries' && !empty($section['items']))
            <div class="about-block reveal delay-{{ ($i % 3) + 1 }}">
              <div class="about-block-head">
                <p class="about-kicker">Serve &amp; grow</p>
                <h3 class="about-block-title">{{ $section['title'] }}</h3>
              </div>
              <div class="about-ministry-grid">
                @foreach($section['items'] as $item)
                  <article class="about-ministry-card">
                    <h4 class="about-ministry-name">{{ $item['label'] }}</h4>
                    @if($item['detail'] !== '')
                      <p class="about-ministry-detail">{{ $item['detail'] }}</p>
                    @endif
                  </article>
                @endforeach
              </div>
            </div>
          @elseif($section['kind'] === 'schedule' && !empty($section['items']))
            <div class="about-block reveal delay-{{ ($i % 3) + 1 }}">
              <div class="about-schedule">
                <div class="about-schedule-copy">
                  <p class="about-kicker">This week</p>
                  <h3 class="about-block-title">{{ $section['title'] }}</h3>
                  <p class="about-schedule-lede">Join us through the week — there is a place for every season of life.</p>
                  @if(!empty($section['images']))
                    @foreach($section['images'] as $photo)
                      @php $aboutShownImageSrcs[$photo['src']] = true; @endphp
                      <figure class="about-schedule-media">
                        <img src="{{ $photo['src'] }}" alt="{{ $photo['alt'] }}" loading="lazy" decoding="async">
                      </figure>
                    @endforeach
                  @endif
                </div>
                <ul class="about-schedule-list">
                  @foreach($section['items'] as $item)
                    <li>
                      <span class="about-schedule-label">{{ $item['label'] }}</span>
                      @if($item['detail'] !== '')
                        <span class="about-schedule-time">{{ $item['detail'] }}</span>
                      @endif
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          @else
            <div class="about-block reveal delay-{{ ($i % 3) + 1 }}">
              @if(!empty($section['title']))
                <div class="about-block-head">
                  <h3 class="about-block-title">{{ $section['title'] }}</h3>
                </div>
              @endif
              @if(!empty($section['items']))
                <ul class="about-simple-list">
                  @foreach($section['items'] as $item)
                    <li>
                      <strong>{{ $item['label'] }}</strong>
                      @if($item['detail'] !== '')
                        <span>{{ $item['detail'] }}</span>
                      @endif
                    </li>
                  @endforeach
                </ul>
              @endif
              @if(trim(strip_tags($section['html'])) !== '')
                <div class="about-lead-text">
                  {!! site_render_html($section['html']) !!}
                </div>
              @endif
            </div>
          @endif
        @endforeach

        @php
          $aboutExtraPhotos = collect($aboutGallery)
              ->reject(fn ($photo) => isset($aboutShownImageSrcs[$photo['src']]))
              ->unique('src')
              ->values();
        @endphp
        @if($aboutExtraPhotos->isNotEmpty())
          <div class="about-gallery reveal">
            @foreach($aboutExtraPhotos as $photo)
              <figure class="about-gallery-frame">
                <img src="{{ $photo['src'] }}" alt="{{ $photo['alt'] }}" loading="lazy" decoding="async">
              </figure>
            @endforeach
          </div>
        @endif
      @else
        <div class="about-lead reveal">
          <figure class="about-lead-media">
            <img src="{{ $storyImg }}" alt="{{ $brand }}" loading="lazy" decoding="async">
            <figcaption class="about-lead-badge">
              <span class="about-lead-badge-name">{{ $brand }}</span>
              @if($tagline)
                <span class="about-lead-badge-tag">{{ $tagline }}</span>
              @endif
            </figcaption>
          </figure>
          <div class="about-lead-copy">
            <p class="about-kicker">About us</p>
            <h2 class="about-title">{{ $page->title }}</h2>
            <div class="about-lead-text">
              {!! site_render_html($page->body) !!}
            </div>
          </div>
        </div>
      @endif
    </div>
  </section>

  <x-site.cta-band
    heading="Come and see for yourself."
    :sub="settings('contact.service_times', 'Sunday gatherings — there is a seat saved with your name on it.')"
    :cta="['url' => route('site.contact'), 'label' => 'Plan your visit']"
  />

  @include('site._footer')
@endsection
