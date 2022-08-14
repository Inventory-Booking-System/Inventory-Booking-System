@props([
    'label',
    'for',
    'error' => false,
    'buttonGroup' => null,
])

<!-- Label for component -->
<label for="{{ $for }}">
    {{ $label }}
</label>

<!-- Component & error message -->
<div>
    @if($buttonGroup) <div class="btn-group btn-group-toggle" data-toggle="buttons"> @endif
    {{ $slot }}
    @if($buttonGroup) </div> @endif

    @if ($error)
        <span class="text-danger">{{ $error }}</span>
    @endif
</div>