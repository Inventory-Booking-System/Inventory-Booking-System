@props([
    'rows' => 4,
])

<textarea {{ $attributes }} rows="{{ $rows }}" name="description" class="form-control"></textarea>