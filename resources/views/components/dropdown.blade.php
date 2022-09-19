@props(['label' => ''])

<div {{ $attributes->merge(['class' => 'dropdown']) }}>
    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      {{ $label }}
    </button>
    <div class="dropdown-menu" >
      {{ $slot }}
    </div>
</div>