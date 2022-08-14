@props([
    'text' => '',
    'checked' => null,
    'value'
])

<label class="btn btn-success @if($checked == $value) active @endif ">
    <input {{ $attributes }} value="{{ $value }}" type="radio" class="btn-check" autocomplete="off">{{ $text }}
</label>

