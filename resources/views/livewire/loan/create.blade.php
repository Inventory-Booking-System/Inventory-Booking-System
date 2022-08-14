<div class="row justify-content-center">
    <div class="col-lg-5 p-3">
        <div class="card">
            <div class="card-header text-center">
                <h5 class="font-weight-bold mb-0">Create Loan</h5>
            </div>

            <div class="card-body">
                <form wire:submit.prevent="save" >
                    <!-- Start Date Time -->
                    <x-input.group label="Start Date" for="start_date" :error="$errors->first('start_date')">
                        <x-input.datetime wire:model="start_date" id="start_date" />
                    </x-input.group>

                    <!-- End Date Time -->
                    <x-input.group label="End Date" for="end_date" :error="$errors->first('end_date')">
                        <x-input.datetime wire:model="end_date" id="end_date" />
                    </x-input.group>

                    <!-- Users -->
                    <x-input.group label="Users" for="user_id" :error="$errors->first('user_id')">
                        <x-input.select wire:model="user_id" id="user_id" placeholder="Select User">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->forename }} {{ $user->surname }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <!-- Equipment -->
                    <x-input.group label="Equipment" for="equipment_id" :error="$errors->first('equipment_id')">
                        <x-input.select wire:model="equipment_id" id="equipment_id" clearSelection iteration="{{ $iteration }}" placeholder="Select Equipment">
                            @foreach ($avaliableEquipment as $equipment)
                                <option value="{{ $equipment->id }}">{{ $equipment->name }} ({{ $equipment->tag }})</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <!-- Details -->
                    <x-input.group label="Details" for="details" :error="$errors->first('details')">
                        <x-input.textarea wire:model="details" id="details" rows="8" />
                    </x-input.group>

                    <!-- Reservation -->
                    <x-input.group label="Reservation" for="status_id" :error="$errors->first('status_id')" buttonGroup>
                        <x-input.radioButton wire:model="status_id" id="status_id_yes" value="1" text="Yes" checked="{{ $status_id }}" />
                        <x-input.radioButton wire:model="status_id" id="status_id_no" value="0" text="No" checked="{{ $status_id }}" />
                    </x-input.group>

                    <!-- Submit Button -->
                    <x-button value="Create Loan"></x-button>
                </form>
            </div>
        </div>
    </div>

    <!-- Shopping Cart -->
	<div class="col-lg-3 p-3" wire:model="shoppingCart">
		<x-shoppingCart.group totalCost="£{{ $shoppingCost }}" >
			@foreach ($shoppingCart as $key => $item)
				<x-shoppingCart.cartCard id="{{ $key }}" name="{{ $item['title'] }}" />
			@endforeach
		</x-shoppingCart.group>
	</div>
</div>