@extends('mail._layout')
@section('body')
    <p>Hi {{ $user->name }},</p>
    <p>This is a friendly reminder that <strong>{{ $event->title }}</strong> is happening tomorrow.</p>
    <p>
        <strong>When:</strong> {{ $event->starts_at?->format('l, F j · g:i A') }}<br>
        <strong>Where:</strong> {{ $event->location ?: '—' }}
    </p>
    @if($rsvp->guest_count > 0)
        <p>You're bringing {{ $rsvp->guest_count }} guest{{ $rsvp->guest_count === 1 ? '' : 's' }}.</p>
    @endif
    <p>Can't make it? <a href="{{ $cancelUrl }}">Cancel your RSVP</a>.</p>
@endsection
