@props([
    'label',
    'for',
    'error' => false,
])

<!-- Label for component -->
<label for="{{ $for }}">
    {{ $label }}
</label>

<!-- Component & error message -->
<div>
    {{ $slot }}

    @if ($error)
        <span class="text-danger">{{ $error }}</span>
    @endif
</div>