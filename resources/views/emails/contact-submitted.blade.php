<x-mail::message>
# New message from the contact form

**From:** {{ $m->name }} &lt;{{ $m->email }}&gt;
@if($m->phone)**Phone:** {{ $m->phone }}@endif

**Subject:** {{ $m->subject }}

---

{{ $m->message }}

---

Received: {{ $m->created_at->format('M j, Y · g:i A') }}
IP: {{ $m->ip }}

<x-mail::button :url="url('/admin/messages/'.$m->id)">
Open in admin
</x-mail::button>

Thanks,<br>{{ config('app.name') }}
</x-mail::message>
