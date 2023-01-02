<div>
    <input {{ $attributes->merge(['class' => 'form-control']) }} type="{{ isset($attributes->type) ? $attributes->type : 'text'  }}" />
</div>