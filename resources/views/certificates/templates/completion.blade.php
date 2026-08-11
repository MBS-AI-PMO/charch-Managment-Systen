{{-- Certificate of Completion --}}
@include('certificates.templates._styles')
@php
  $brand = settings('brand.name', 'Assemblies of God');
  $tagline = settings('brand.tagline', 'Rawalpindi');
  $purpose = $certificate->title ?: 'the training programme';
@endphp
<div class="certificate certificate--completion">
  <div class="certificate-frame">
    <div class="certificate-inner">
    <div class="certificate-content">
      <p class="certificate-brand">{{ $brand }}@if($tagline)<span> · {{ $tagline }}</span>@endif</p>
      <div class="certificate-divider" aria-hidden="true"></div>
      <div class="certificate-ribbon">Certificate of Completion</div>
      <h1 class="certificate-heading" style="color:#2c2825;">Successfully Completed</h1>
      <p class="certificate-presented">This certifies that</p>
      <p class="certificate-recipient">{{ $certificate->recipient_name }}</p>
      <p class="certificate-body">
        has successfully completed <strong>{{ $purpose }}</strong>
        with dedication, attentiveness, and a heart to grow.
        We commend this achievement and pray that God continues to equip and use them for every good work.
      </p>
      <p class="certificate-verse">“I can do all things through Christ who strengthens me.” — Philippians 4:13</p>

      <div class="certificate-meta">
        <div>
          <p class="certificate-meta-value">{{ $certificate->issued_on?->format('F j, Y') }}</p>
          <p class="certificate-meta-label">Date</p>
        </div>
        <div class="certificate-seal" aria-hidden="true" style="border-color:#7a1f2b;"><span>✓</span></div>
        <div>
          <p class="certificate-meta-value">{{ $certificate->assigned_by }}</p>
          <p class="certificate-meta-label">Assigned by</p>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
