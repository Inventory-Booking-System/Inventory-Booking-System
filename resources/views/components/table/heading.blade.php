@props([
	'width' => null,
    'sortable' => null,
    'direction' => 'asc',
])

<th {{ $attributes }} role='button' class="col-{{ $width }}">{{ $slot }}
<span>
@if($sortable)
    @if($direction == "desc")
        <i style="transform: translateY(-10%);" class="fa-solid fa-sort-down"></i>
    @elseif($direction == "asc")
        <i style="transform: translateY(30%);" class="fa-solid fa-sort-up"></i>
    @endif
@endif
</span></th>