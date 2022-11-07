@props([
    'route' => null,
    'id' => null,
    'value' => null,
])

<a href="/{{ $route }}/{{ $id }}">{{ $value }}</a>