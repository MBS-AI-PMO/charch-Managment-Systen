@extends('layouts.site')

@section('title', $page?->meta_title ?: (($page?->title ?: 'Contact').settings('seo.default_title_suffix', '')))

@include('site._meta')

@php
  $heroImg = site_img($page?->hero_image_path, 'contact-hero', 1800, 700);
  $address = settings('contact.address');
  $phone = settings('contact.phone');
  $email = settings('contact.email');
  $svc = settings('contact.service_times');
@endphp

@section('content')
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    :image="$heroImg"
    eyebrow="Say hello"
    :heading="$page?->hero_heading ?: 'We would love to hear from you.'"
    :sub="$page?->hero_subheading ?: 'Questions, prayer requests, planning a visit — start the conversation here.'"
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('site.home')],
      ['label' => 'Contact'],
    ]" />
  </div>

  <section class="contact-page relative overflow-hidden">
    <div class="contact-page-bg" aria-hidden="true"></div>

    <div class="relative max-w-container mx-auto px-4 sm:px-6 py-10 md:py-14">
      <div class="grid lg:grid-cols-2 gap-6 lg:gap-8 lg:items-stretch">
        {{-- Form --}}
        <div class="contact-panel">
          <h2 class="font-serif text-2xl md:text-3xl text-ink">Send us a message</h2>
          <p class="text-ink-muted text-sm mt-2">We usually respond within one business day.</p>

          <form method="post" action="{{ route('site.contact.submit') }}" class="mt-8 space-y-5 flex-1 flex flex-col">
            @csrf

            <div aria-hidden="true" class="absolute -left-[9999px] top-0">
              <label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off" value="{{ old('website') }}"></label>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label for="contact-name" class="block text-sm font-medium mb-1">Name</label>
                <input id="contact-name" type="text" name="name" value="{{ old('name') }}" class="input" placeholder="Jane Doe" required />
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label for="contact-email" class="block text-sm font-medium mb-1">Email</label>
                <input id="contact-email" type="email" name="email" value="{{ old('email') }}" class="input" placeholder="jane@example.com" required />
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
              </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label for="contact-phone" class="block text-sm font-medium mb-1">Phone <span class="text-ink-muted font-normal">(optional)</span></label>
                <input id="contact-phone" type="tel" name="phone" value="{{ old('phone') }}" class="input" />
                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
              </div>
              <div>
                <label for="contact-subject" class="block text-sm font-medium mb-1">Subject</label>
                @php $subjects = ['Planning a visit', 'Prayer request', 'Get involved', 'General question']; @endphp
                <select id="contact-subject" name="subject" class="input">
                  @foreach($subjects as $opt)
                    <option value="{{ $opt }}" @selected(old('subject') === $opt)>{{ $opt }}</option>
                  @endforeach
                </select>
                @error('subject')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
              </div>
            </div>
            <div class="flex-1 flex flex-col">
              <label for="contact-message" class="block text-sm font-medium mb-1">Message</label>
              <textarea id="contact-message" name="message" rows="6" class="input flex-1 min-h-[9rem]" placeholder="Tell us a little about you…" required>{{ old('message') }}</textarea>
              @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary shine-btn glow-primary w-full mt-auto">
              <span>Send message</span>
            </button>
          </form>
        </div>

        {{-- Visit / info — same height --}}
        <aside class="contact-panel contact-panel--info">
          <div>
            <p class="text-xs uppercase tracking-[0.18em] text-brand-primary font-semibold">Visit us</p>
            <h2 class="font-serif text-2xl md:text-3xl text-ink mt-1">Come as you are</h2>
            <p class="text-ink-muted text-sm mt-2 leading-relaxed">
              We would love to welcome you in person. Here is how to find and reach us.
            </p>
          </div>

          <ul class="contact-info-list">
            @if($address)
              <li class="contact-info-item">
                <span class="contact-info-icon" aria-hidden="true">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                  </svg>
                </span>
                <div>
                  <div class="contact-info-label">Address</div>
                  <div class="contact-info-value">{{ $address }}</div>
                </div>
              </li>
            @endif
            @if($phone)
              <li class="contact-info-item">
                <span class="contact-info-icon" aria-hidden="true">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                  </svg>
                </span>
                <div>
                  <div class="contact-info-label">Phone</div>
                  <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="contact-info-value contact-info-link">{{ $phone }}</a>
                </div>
              </li>
            @endif
            @if($email)
              <li class="contact-info-item">
                <span class="contact-info-icon" aria-hidden="true">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                  </svg>
                </span>
                <div>
                  <div class="contact-info-label">Email</div>
                  <a href="mailto:{{ $email }}" class="contact-info-value contact-info-link break-all">{{ $email }}</a>
                </div>
              </li>
            @endif
            @if($svc)
              <li class="contact-info-item">
                <span class="contact-info-icon" aria-hidden="true">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </span>
                <div>
                  <div class="contact-info-label">Service times</div>
                  <div class="contact-info-value whitespace-pre-line">{{ $svc }}</div>
                </div>
              </li>
            @endif
          </ul>

          <div class="contact-info-note mt-auto">
            <p class="font-serif text-lg text-ink">Planning your first visit?</p>
            <p class="text-ink-muted text-sm mt-1 leading-relaxed">
              Join us this Sunday — someone will be glad to greet you and help you feel at home.
            </p>
          </div>
        </aside>
      </div>
    </div>
  </section>

  @include('site._footer')
@endsection
