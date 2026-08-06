@extends('mail._layout')
@section('body')
    <p>Hi {{ $admin->name }},</p>
    <p>Yesterday's activity and what's on for today.</p>

    <h3>New members ({{ count($payload['new_members'] ?? []) }})</h3>
    <h3>New prayer requests ({{ count($payload['new_prayer'] ?? []) }})</h3>
    <h3>Open care requests ({{ count($payload['open_care'] ?? []) }})</h3>
    <h3>Today's events with attendance open</h3>
    <ul>
        @foreach($payload['events_today'] ?? [] as $e)
            <li>{{ $e->title }} · {{ $e->starts_at?->format('g:i A') }} · code {{ $e->checkin_code }}</li>
        @endforeach
    </ul>
@endsection
