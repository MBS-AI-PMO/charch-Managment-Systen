<x-mail::message>
@if($p->title)
# {{ $p->title }}
@else
# A new update from the church
@endif

{{ \Illuminate\Support\Str::limit(strip_tags($p->body), 400) }}

<x-mail::button :url="url('/member/feed')">Read in the portal</x-mail::button>

Posted {{ $p->published_at?->format('M j, Y · g:i A') }}
</x-mail::message>
