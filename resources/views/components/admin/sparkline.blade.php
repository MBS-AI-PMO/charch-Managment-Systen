@props(['data' => []])

@php
    $values = collect($data)->pluck('value')->all() ?: [0];
    $max = max($values);
    $width = 200;
    $height = 40;
    $points = collect($values)->map(function ($v, $i) use ($values, $width, $height, $max) {
        $x = (count($values) > 1) ? ($i / (count($values) - 1)) * $width : 0;
        $y = $height - ($max > 0 ? ($v / $max) * $height : 0);
        return $x.','.$y;
    })->implode(' ');
@endphp

<svg viewBox="0 0 {{ $width }} {{ $height }}" class="w-full h-10">
    <polyline fill="none" stroke="currentColor" stroke-width="2" points="{{ $points }}" />
</svg>
