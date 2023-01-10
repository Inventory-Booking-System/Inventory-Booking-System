@props([
    'route' => null,
    'id' => null,
    'value' => null,
])

<a href="/{{ $route }}/{{ $id }}" {{ $attributes }} >{{ $value }}</a>