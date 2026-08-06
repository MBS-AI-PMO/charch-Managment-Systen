@extends('mail._layout')
@section('body')
    <p>Hi {{ $user->name }},</p>
    <p>Here's what's coming up this week.</p>

    @if(!empty($payload['events']) && (is_array($payload['events']) ? count($payload['events']) : $payload['events']->isNotEmpty()))
        <h3>Upcoming events</h3>
        <ul>
            @foreach($payload['events'] as $e)
                <li><strong>{{ $e->title }}</strong> — {{ $e->starts_at?->format('D, M j') }}</li>
            @endforeach
        </ul>
    @endif

    @if(!empty($payload['prayer']) && (is_array($payload['prayer']) ? count($payload['prayer']) : $payload['prayer']->isNotEmpty()))
        <h3>Prayer requests</h3>
        <ul>
            @foreach($payload['prayer'] as $p)
                <li>{{ \Illuminate\Support\Str::limit($p->title, 100) }}</li>
            @endforeach
        </ul>
    @endif

    @if(!empty($payload['feed']) && (is_array($payload['feed']) ? count($payload['feed']) : $payload['feed']->isNotEmpty()))
        <h3>From the community</h3>
        <ul>
            @foreach($payload['feed'] as $f)
                <li>{{ \Illuminate\Support\Str::limit($f->title ?? '', 100) }}</li>
            @endforeach
        </ul>
    @endif
@endsection
