<x-mail::message>
# We replied to your question

Hi {{ $m->name }},

A member of our team has replied to your question.

**Your question:**
{{ $m->message }}

---

**Our reply:**
{{ $reply->body }}

@if($m->user_id)
<x-mail::button :url="url('/member/questions/'.$m->id)">
View in your history
</x-mail::button>
@endif

Thanks,<br>{{ config('app.name') }}
</x-mail::message>
