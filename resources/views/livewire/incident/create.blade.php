<div class="row justify-content-center">
    <div class="col-lg-5 p-3">
        <div class="card">
            <div class="card-header text-center">
                Create Incident
            </div>

            <div class="card-body">
                <form wire:submit.prevent="save" >
                    <!-- Start Date Time -->
                    <x-input.group label="start_date_time" for="start_date_time" :error="$errors->first('start_date_time')">
                        <x-input.datetime wire:model="start_date_time" id="start_date_time" />
                    </x-input.group>

                    <!-- Distribution Group -->
                    <x-input.group label="distribution_id" for="distribution_id" :error="$errors->first('distribution_id')">
                        <x-input.select wire:model="distribution_id" id="distribution_id">
                            @foreach ($distributions as $distribution)
                                <option value="{{ $distribution->id }}">{{ $distribution->name }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <!-- Location -->
                    <x-input.group label="location_id" for="location_id" :error="$errors->first('location_id')">
                        <x-input.select wire:model="location_id" id="location_id">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <!-- Equipment Issues -->
                    <x-input.group label="equipment_id" for="equipment_id" :error="$errors->first('equipment_id')">
                        <x-input.select wire:model="equipment_id" id="equipment_id">
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
    {{-- <div class="col-lg-3 p-3">
        <div id="equipmentList">
            <table class="table" id="equipmentTable">
                <thead>
                    <tr>
                        <th scope="col">Item</th>
                        <th scope="col">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset(session()->getOldInput()['bookableEquipment']))
                        @foreach (old('bookableEquipment') as $equipment)
                            @isset($equipment->selected)
                                @if($equipment->selected)
                                    <tr id="{{ $equipment->id }}">
                                        <td>{{ $equipment->name }} ({{ $equipment->tag }})</td>
                                        <td><button class="removeFromCart btn btn-danger btn-sm rounded-0" type="button" data-assetname="{{ $equipment->name }} ({{ $equipment->tag }})" data-assetid="{{ $equipment->id }}" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button></td>
                                    </tr>
                                @endif
                            @endisset
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <input type="hidden" id="equipmentToSubmit" name="equipmentSelected">

        <!-- Total Cost -->
        <h2 class="mt-3">Total Cost</h2><span></span>
    </div> --}}
</div>