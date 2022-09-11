@props([
	'width' => null,
])

<td class="col-{{ $width }}">{{ $slot }}</td>