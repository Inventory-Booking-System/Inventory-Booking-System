<button type="button" {{ $attributes->merge(['class' => 'btn btn-secondary']) }} {{ $attributes }}>
    {{ $slot }}
</button>