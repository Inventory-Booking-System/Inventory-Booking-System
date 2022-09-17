@props(['type' => null])

<button type="{{ $type ? $type : 'button' }}" {{ $attributes->merge(['class' => 'btn btn-primary']) }}>
    {{ $slot }}
</button>