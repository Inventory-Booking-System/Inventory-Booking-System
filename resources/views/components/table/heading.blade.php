@props([
	'width' => null,
    'sortable' => null,
    'direction' => null,
])

<th role='button' class="col-{{ $width }}">{{ $slot }}
<span>
@if($direction == "desc")
    <i style="transform: translateY(-10%);" class="fa-solid fa-sort-down"></i>
@else
    <i style="transform: translateY(30%);" class="fa-solid fa-sort-up"></i>
@endif
</span></th>