@props([
    'id',
])

<div
    x-data="{ {{ $id }}: @entangle($attributes->wire('model'))}"
    x-init="picker = new tempusDominus.TempusDominus(document.getElementById('picker{{ $id }}'), {display: {sideBySide: true,}}); picker.dates.formatInput = date => moment(date).format('DD MMM yyyy HH:mm')"
>
    <div
        class='input-group'
        id='picker{{ $id }}'
        data-td-target-input='nearest'
        data-td-target-toggle='nearest'
    >
   <input
      {{ $attributes->whereDoesntStartWith('wire:model') }}
      x-bind:value="{{ $id }}"
      x-on:change="{{ $id }} = $event.target.value"
      type='text'
      class='form-control'
      data-td-target='#picker{{ $id }}'
   />
   <span
     class='input-group-text'
     data-td-target='#picker{{ $id }}'
     data-td-toggle='datetimepicker'
   >
     <span class='fa-solid fa-calendar'></span>
   </span>
 </div>
</div>
