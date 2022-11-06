<tr {{ $attributes->merge(['class' => 'd-flex']) }} wire:loading.style.delay="opacity:0.5;" {{ $attributes }}>
    {{ $slot }}
</tr>
