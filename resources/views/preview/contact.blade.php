@extends('layouts.site')

@section('title', 'Contact · Grace Community')

@section('content')
  @php include resource_path('views/preview/_seed.php'); @endphp
  <x-site.header :nav="$siteNav" />

  <x-site.hero
    image="https://picsum.photos/seed/contact-hero/1800/700"
    eyebrow="Say hello"
    heading="We would love to hear from you."
    sub="Questions, prayer requests, planning a visit — start the conversation here."
    size="sm"
  />

  <div class="max-w-container mx-auto px-4">
    <x-site.breadcrumb :trail="[
      ['label' => 'Home', 'url' => route('preview.home')],
      ['label' => 'Contact'],
    ]" />
  </div>

  <section class="max-w-container mx-auto px-4 py-16 grid lg:grid-cols-2 gap-12 items-start">
    {{-- Form --}}
    <div class="card p-8 md:p-10">
      <h2 class="font-serif text-2xl md:text-3xl">Send us a message</h2>
      <p class="text-ink-muted text-sm mt-2">We usually respond within one business day.</p>
      <form class="mt-8 space-y-5">
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" class="input" placeholder="Jane Doe" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" class="input" placeholder="jane@example.com" />
          </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Phone <span class="text-ink-muted font-normal">(optional)</span></label>
            <input type="tel" class="input" placeholder="(217) 555-0123" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Subject</label>
            <select class="input">
              <option>Planning a visit</option>
              <option>Prayer request</option>
              <option>Get involved</option>
              <option>General question</option>
              <option>Press / partnership</option>
            </select>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Message</label>
          <textarea rows="6" class="input" placeholder="Tell us a little about you or what you are wondering…"></textarea>
        </div>
        <label class="flex items-start gap-3 text-sm text-ink-muted">
          <input type="checkbox" class="mt-1 rounded text-brand-primary focus:ring-brand-primary">
          <span>It is OK to add me to the church newsletter (one email per week, easy to unsubscribe).</span>
        </label>
        <button type="button" disabled class="btn-primary w-full opacity-70 cursor-not-allowed">Send message (preview only)</button>
      </form>
    </div>

    {{-- Info + map --}}
    <div class="space-y-8">
      <div class="card p-8">
        <h3 class="font-serif text-2xl">Visit us</h3>
        <div class="mt-6 space-y-5 text-sm">
          <div>
            <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">Address</div>
            <div class="font-medium">{{ $footerInfo['address'] }}</div>
          </div>
          <div>
            <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">Phone</div>
            <a href="tel:{{ $footerInfo['phone'] }}" class="font-medium hover:text-brand-primary">{{ $footerInfo['phone'] }}</a>
          </div>
          <div>
            <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">Email</div>
            <a href="mailto:{{ $footerInfo['email'] }}" class="font-medium hover:text-brand-primary">{{ $footerInfo['email'] }}</a>
          </div>
          <div>
            <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">Service times</div>
            <div class="font-medium whitespace-pre-line leading-relaxed">{{ $footerInfo['services'] }}</div>
          </div>
          <div>
            <div class="text-xs uppercase tracking-wider text-brand-primary font-semibold mb-1">Office hours</div>
            <div class="font-medium">Tue – Fri · 9 AM – 4 PM</div>
          </div>
        </div>
      </div>

      <div class="rounded-2xl overflow-hidden border border-[rgb(var(--border))] aspect-[16/10] shadow-sm">
        <iframe class="w-full h-full" loading="lazy"
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12136.654!2d-89.6437!3d39.7817!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88754ed8f3aa17d3%3A0x9a8e8d36cb84e80!2sSpringfield%2C%20IL!5e0!3m2!1sen!2sus!4v1716000000000"></iframe>
      </div>
    </div>
  </section>

  @include('preview._footer')
@endsection
