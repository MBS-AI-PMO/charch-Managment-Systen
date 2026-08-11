{{-- Certificate of Participation --}}
@include('certificates.templates._styles')
@php
  $brand = settings('brand.name', 'Assemblies of God');
  $tagline = settings('brand.tagline', 'Rawalpindi');
  $purpose = $certificate->title ?: 'our church gathering';
@endphp
<div class="certificate certificate--participation">
  <div class="certificate-frame">
    <div class="certificate-inner">
    <div class="certificate-content">
      <p class="certificate-brand">{{ $brand }}@if($tagline)<span> · {{ $tagline }}</span>@endif</p>
      <div class="certificate-divider" aria-hidden="true"></div>
      <p class="certificate-eyebrow">Certificate of Participation</p>
      <h1 class="certificate-heading">Thank You for Taking Part</h1>
      <p class="certificate-presented">Awarded with joy to</p>
      <p class="certificate-recipient">{{ $certificate->recipient_name }}</p>
      <p class="certificate-body">
        for active participation in <strong>{{ $purpose }}</strong>.
        Your presence, encouragement, and willing heart helped make this time meaningful for our church family.
        We celebrate your involvement and pray God continues to strengthen and guide you.
      </p>
      <p class="certificate-verse">“Let us not give up meeting together… but encouraging one another.” — Hebrews 10:25</p>

      <div class="certificate-meta">
        <div>
          <p class="certificate-meta-value">{{ $certificate->issued_on?->format('F j, Y') }}</p>
          <p class="certificate-meta-label">Date</p>
        </div>
        <div class="certificate-seal" aria-hidden="true" style="color:#8e743a;"><span>★</span></div>
        <div>
          <p class="certificate-meta-value">{{ $certificate->assigned_by }}</p>
          <p class="certificate-meta-label">Assigned by</p>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
