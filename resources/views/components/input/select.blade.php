@props([
    'placeholder' => null,
    'clearSelection' => null,
    'iteration' => null,
])

@if($iteration)<div wire:key='"select-field-version-{{ $iteration }}'>@endif
<div
    wire:ignore
    x-data="{
        {{ $attributes->get('id') }}: @entangle($attributes->wire('model'))
    }"
    x-init="
        select2 = $('#{{ $attributes->get('id') }}').select2({
            theme: 'bootstrap-5',
            placeholder: '{{ $placeholder }}',
            allowClear: true,
        });

        select2.on('select2:select', (event) => {
            console.log('triggered');
            {{ $attributes->get('id') }} = event.params.data['id'];

            @if ($clearSelection)
                $('#{{ $attributes->get('id') }}').val('').trigger('change');
            @endif
        });

        window.livewire.on('showModal', () => {
            $('#{{ $attributes->get('id') }}').val(@entangle($attributes->wire('model')).initialValue).trigger('change');
        })

        "
>
    <select
        x-bind:value="{{ $attributes->get('id') }}"
        x-ref="select"
        {{ $attributes }} class="form-control">
        @if ($placeholder)
            <option></option>
        @endif
        {{ $slot }}
    </select>
</div>
@if($iteration)</div>@endif