@props([
    'route' => null,
    'id' => null,
    'value' => null,
    'lineThrough' => false
])

<a
    href="/{{ $route }}/{{ $id }}"
    @if($lineThrough) {{ $attributes->merge(['class' => 'line-through text-secondary']) }} @endif
    {{ $attributes }}
>
    {{ $value }}
</a>