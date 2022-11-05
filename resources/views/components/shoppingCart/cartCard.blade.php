@props([
	'id' => null,
	'name' => null,
	'cost' => null,
	'quantity' => null,
	'assetId' => null,
	'returned' => null,
])

<div class="card mb-3">
	<div class="card-body px-3 py-2 my-0">
		<div class="d-flex justify-content-between">
		<div class="d-flex flex-row align-items-center">
			<div>
			<h5 class="mb-0">{{ $name }}</h5>
			</div>
		</div>
		<div class="d-flex flex-row align-items-center">

			<!-- Quantity -->
			@isset($quantity)
				<div style="width: 50px;">
					<h5 class="fw-normal mb-0">{{ $quantity }}</h5>
				</div>
			@endif

			<!-- Asset ID -->
			@isset($assetId)
				<div style="width: 50px;">
					<h5 class="fw-normal mb-0">{{ $assetId }}</h5>
				</div>
			@endif

			<!-- Returned -->
			@isset($returned)
				<div style="width: 50px;">
					<h5 class="fw-normal mb-0">{{ $returned ? 'yes' : 'no' }}</h5>
				</div>
			@endif

			<!-- Cost -->
			@isset($cost)
				<div style="width: 80px;">
					<h5 class="mb-0">£{{ $cost }}</h5>
				</div>
			@endif

			<!-- Remove from cart -->
			<a href="#" wire:click='removeItem({{ $id }})' style="color: #cecece;"><i class="fas fa-trash-alt"></i></a>
		</div>
		</div>
	</div>
</div>