{{-- Certificate of Appreciation --}}
@include('certificates.templates._styles')
@php
  $brand = settings('brand.name', 'Assemblies of God');
  $tagline = settings('brand.tagline', 'Rawalpindi');
  $purpose = $certificate->title ?: 'faithful service and dedication';
@endphp
<div class="certificate certificate--appreciation">
  <div class="certificate-frame">
    <div class="certificate-inner">
    <div class="certificate-content">
      <p class="certificate-brand">{{ $brand }}@if($tagline)<span> · {{ $tagline }}</span>@endif</p>
      <div class="certificate-divider" aria-hidden="true"></div>
      <p class="certificate-eyebrow">Certificate of Appreciation</p>
      <h1 class="certificate-heading">With Sincere Gratitude</h1>
      <p class="certificate-presented">This certificate is proudly presented to</p>
      <p class="certificate-recipient">{{ $certificate->recipient_name }}</p>
      <p class="certificate-body">
        in joyful recognition of <strong>{{ $purpose }}</strong>.
        Your willingness to serve, encourage, and walk with this church family has been a true blessing.
        We honour your faithfulness and give thanks to God for your life and ministry among us.
      </p>
      <p class="certificate-verse">“Well done, good and faithful servant.” — Matthew 25:23</p>

      <div class="certificate-meta">
        <div>
          <p class="certificate-meta-value">{{ $certificate->issued_on?->format('F j, Y') }}</p>
          <p class="certificate-meta-label">Date</p>
        </div>
        <div class="certificate-seal" aria-hidden="true"><span>AG</span></div>
        <div>
          <p class="certificate-meta-value">{{ $certificate->assigned_by }}</p>
          <p class="certificate-meta-label">Assigned by</p>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
