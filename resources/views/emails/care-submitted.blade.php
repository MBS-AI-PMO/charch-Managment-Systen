<x-mail::message>
# A member has knocked for help

**Member:** {{ $c->user->name }} ({{ $c->user->email }})
@if($c->user->phone)
**Phone:** {{ $c->user->phone }}
@endif
**Category:** {{ ucfirst($c->category) }}
**Share with team:** {{ $c->share_with_team ? 'Yes' : 'No (pastor only)' }}

> {{ $c->message }}

<x-mail::button :url="url('/admin/care/'.$c->id)">Open in admin</x-mail::button>

Submitted {{ $c->created_at->format('M j, Y · g:i A') }}
</x-mail::message>
