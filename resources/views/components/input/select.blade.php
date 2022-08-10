@props([
    'placeholder' => null,
])

<select {{ $attributes }} class="form-control">
    @if ($placeholder)
        <option disabled value="">{{ $placeholder }}</option>
    @endif
    {{ $slot }}
</select>