@props([
    'placeholder' => null,
    'clearSelection' => null,
    'disabledSelected' => null,
    'refreshData' => null,
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
            //This option allows us to disable options in the dropdown and highlight red when the user has selected them
            //Useful for the asset dropdown as we dont want users to select assets which are not avaliable
            @if ($disabledSelected)
                $('#{{ $attributes->get('id') }}').find(':selected').attr('disabled','disabled').css('color','red !important').trigger('change');
            @endif

            {{ $attributes->get('id') }} = event.params.data['id'];

            //This will clear the box that shows what was last selected
            //Used for the shopping cart as it's a means to select an item to shop up in the cart
            //Rather than a static option which is selected
            @if ($clearSelection)
                console.log('Clearing Selection');
                $('#{{ $attributes->get('id') }}').val('').trigger('change');
            @endif
        });

        //When the modal opened make sure the options are cleared out so they can be reloaded
        //again with the correct data (which bookings are avaiable etc)
        window.livewire.on('showModal', (data) => {
            console.log('Recieved showModal event');
            console.log(data);
            console.log(@entangle('editing.loan.start_date_time'));
            if(data == &quot;create&quot;){
                @if($refreshData)
                    $('#{{ $attributes->get('id') }}').select2().empty();
                @endif
            }

            //This allows us the wire:model initial value to be applied to the Select2 box.
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