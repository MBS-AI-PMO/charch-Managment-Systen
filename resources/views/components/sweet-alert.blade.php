{{-- Centered SweetAlert-style modal for flash / confirmations --}}
@php
  $flashError = session('error');
  $flashSuccess = session('success');
  $flashStatus = session('status');
  $flashInfo = session('info');

  $flashType = null;
  $flashMessage = null;
  if ($flashError) {
      $flashType = 'error';
      $flashMessage = $flashError;
  } elseif ($flashSuccess) {
      $flashType = 'success';
      $flashMessage = $flashSuccess;
  } elseif ($flashStatus) {
      $flashType = 'success';
      $flashMessage = $flashStatus;
  } elseif ($flashInfo) {
      $flashType = 'info';
      $flashMessage = $flashInfo;
  }
@endphp

<div
  x-data
  x-cloak
  @if($flashMessage)
    x-init="$store.alert.show(@js($flashMessage), @js($flashType))"
  @endif
>
  <div
    x-show="$store.alert.open"
    x-cloak
    class="fixed inset-0 z-[120] flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="swal-title"
  >
    <div
      class="absolute inset-0 bg-ink/45"
      x-show="$store.alert.open"
      x-transition.opacity.duration.200ms
      @click="$store.alert.close()"
    ></div>

    <div
      class="swal-card relative w-full max-w-[20rem]"
      x-show="$store.alert.open"
      x-transition:enter="transition ease-out duration-250"
      x-transition:enter-start="opacity-0 translate-y-3 scale-95"
      x-transition:enter-end="opacity-100 translate-y-0 scale-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100 translate-y-0 scale-100"
      x-transition:leave-end="opacity-0 translate-y-2 scale-95"
      @click.stop
      @keydown.escape.window="if ($store.alert.open) $store.alert.close()"
    >
      <template x-if="$store.alert.open && $store.alert.type === 'success'">
        <div class="swal-success">
          <svg class="swal-success-svg" viewBox="0 0 52 52" aria-hidden="true">
            <circle class="swal-success-ring" cx="26" cy="26" r="24" fill="none"/>
            <path class="swal-success-tick" fill="none" d="M14.5 27.5 L22.5 34.5 L37.5 18"/>
          </svg>
        </div>
      </template>

      <template x-if="$store.alert.open && $store.alert.type === 'error'">
        <div class="swal-icon-wrap swal-icon-error">
          <svg class="swal-icon swal-draw-x" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path stroke="currentColor" stroke-width="2.25" stroke-linecap="round" d="M6 6l12 12"/>
            <path stroke="currentColor" stroke-width="2.25" stroke-linecap="round" d="M18 6L6 18"/>
          </svg>
        </div>
      </template>

      <template x-if="$store.alert.open && $store.alert.type !== 'success' && $store.alert.type !== 'error'">
        <div class="swal-icon-wrap swal-icon-info">
          <svg class="swal-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path stroke="currentColor" stroke-width="2.25" stroke-linecap="round" d="M12 8v5m0 3h.01"/>
          </svg>
        </div>
      </template>

      <h2 id="swal-title" class="swal-title" x-text="$store.alert.message"></h2>

      <button type="button" class="swal-btn" @click="$store.alert.close()">
        OK
      </button>
    </div>
  </div>
</div>
