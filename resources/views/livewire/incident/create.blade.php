<div class="row justify-content-center">
    <div class="col-lg-5 p-3">
        <div class="card">
            <div class="card-header text-center">
                Create Incident
            </div>

            <div class="card-body">
                <form wire:submit.prevent="save" >
                    <!-- Start Date Time -->
                    <x-input.group label="Start Date" for="start_date_time" :error="$errors->first('start_date_time')">
                        <x-input.datetime wire:model="start_date_time" id="start_date_time" />
                    </x-input.group>

                    <!-- Distribution Group -->
                    <x-input.group label="Alert" for="distribution_id" :error="$errors->first('distribution_id')">
                        <x-input.select wire:model="distribution_id" id="distribution_id" placeholder="Select who to alert">
                            @foreach ($distributions as $distribution)
                                <option value="{{ $distribution->id }}">{{ $distribution->name }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <!-- Location -->
                    <x-input.group label="Location" for="location_id" :error="$errors->first('location_id')">
                        <x-input.select wire:model="location_id" id="location_id" placeholder="Select Location">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <!-- Equipment Issues -->
                    <x-input.group label="Equipment Issues" for="equipment_id" :error="$errors->first('equipment_id')">
                        <x-input.select wire:model="equipment_id" id="equipment_id" placeholder="Select Issue" clearSelection>
                            @foreach ($equipmentIssues as $equipment)
                                <option value="{{ $equipment->id }}">{{ $equipment->title }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <!-- Evidence -->
                    <x-input.group label="Evidence" for="evidence" :error="$errors->first('evidence')">
                        <x-input.text wire:model="evidence" id="evidence" />
                    </x-input.group>

                    <!-- Details -->
                    <x-input.group label="Details" for="details" :error="$errors->first('details')">
                        <x-input.textarea wire:model="details" id="details" rows="8" />
                    </x-input.group>

                    <!-- Submit Button -->
                    <x-button value="Create Incident"></x-button>
                </form>
            </div>
        </div>
    </div>

    <!-- Shopping Cart -->
	<div class="col-lg-3 p-3" wire:model="shoppingCart">
		<x-shoppingCart.group totalCost="£{{ $shoppingCost }}" >
			@foreach ($shoppingCart as $key => $item)
				<x-shoppingCart.cartCard id="{{ $key }}" name="{{ $item['title'] }}" cost="{{ $item['cost'] }}" quantity="{{ $item['quantity'] }}" />
			@endforeach
		</x-shoppingCart.group>
	</div>
</div>