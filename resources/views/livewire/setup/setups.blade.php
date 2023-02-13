<div>
    <x-table.controls name="Setup" perPage="{{ $perPage }}" />

    <div class="row">
        <div wire:poll.10s class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('id')" :direction="$sorts['id'] ?? null" class="col-1">ID</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('user_id')" :direction="$sorts['user_id'] ?? null" class="col-2">User</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('location')" :direction="$sorts['location'] ?? null" class="col-1">Location</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('start_date_time')" :direction="$sorts['start_date_time'] ?? null" class="col-1">Start Date</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('end_date_time')" :direction="$sorts['end_date_time'] ?? null" class="col-1">End Date</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('details')" :direction="$sorts['details'] ?? null" class="col-2">Details</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('assets')" :direction="$sorts['assets'] ?? null" class="col-2">Assets</x-table.heading>
                        <x-table.heading class="col"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.user_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.location" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.start_date_time" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.end_date_time" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.details" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.assets" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"/>
                        </x-table.row>
                    @endif
                </x-slot>

                <x-slot name="body">
                    @if($selectPage)
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    @unless($selectAll)
                                        <div>
                                            <span>You selected <strong> {{ $setups->count() }} </strong> setups, do you want to select all <strong> {{ $setups->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $setups->total() }} </strong> setups.</span>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($setups as $setup)
                    <x-table.row wire:key="row-{{ $setup->loan->id }}">
                        <x-table.cell>
                            <x-input.checkbox wire:model="selected" value="{{ $setup->loan->id }}"></x-input.checkbox>
                        </x-table.cell>
                        <x-table.cell class="col-1"><x-link route="setups" id="{{ $setup->id }}" value="#{{ $setup->id }} {{ $setup->title }}"></x-link></x-table.cell>
                        <x-table.cell class="col-2"><x-link route="users" id="{{ $setup->loan->user->id }}" value="{{ $setup->loan->user->forename }} {{ $setup->loan->user->surname }}"></x-link></x-table.cell>
                        <x-table.cell class="col-1"><x-link route="locations" id="{{ $setup->location->id }}" value="{{ $setup->location->name }}"></x-link></x-table.cell>
                        <x-table.cell class="col-1">{{ $setup->loan->start_date_time }}</x-table.cell>
                        <x-table.cell class="col-1">{{ $setup->loan->end_date_time }}</x-table.cell>
                        <x-table.cell class="col-2" title="{{ $setup->loan->details }}">
                            @if(strlen($setup->loan->details) > 300 && !in_array('details-'.$setup->id, $expandedCells))
                                {{ substr($setup->loan->details, 0, 297) }}...
                                <div><x-button.link wire:click.prevent="expandCell('details-{{ $setup->id }}')">Show more</x-button.link></div>
                            @elseif(in_array('details-'.$setup->id, $expandedCells))
                                {{ $setup->loan->details }}
                                <div><x-button.link wire:click.prevent="collapseCell('details-{{ $setup->id }}')">Show less</x-button.link></div>
                            @else
                                {{ $setup->loan->details }}
                            @endif
                        </x-table.cell>
                        <x-table.cell class="col-2">
                            @foreach($setup->loan->assets as $asset)
                                <x-link route="assets" id="{{ $asset->id }}" value="{{ $asset->name }} ({{ $asset->tag }})" style="{{ $asset->pivot->returned ? 'text-decoration: line-through' : '' }}" class="{{ $asset->pivot->returned ? 'text-secondary' : '' }}"></x-link><br>
                            @endforeach
                        </x-table.cell>
                        <x-table.cell class="col">
                            <div class="btn-group" role="group" aria-label="Basic example">
                                <x-button.success wire:click="complete({{ $setup->id }})" ><x-loading wire:target="complete({{ $setup->id }})" />Complete</x-button.success>
                                <x-button.danger wire:click="cancel({{ $setup->id }})" ><x-loading wire:target="cancel({{ $setup->id }})" />Cancel</x-button.danger>
                                <x-button.primary wire:click="edit({{ $setup->id }})" ><x-loading wire:target="edit({{ $setup->id }})" />Edit</x-button.primary>
                            </div>
                        </x-table.cell>
                    </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    No setups found
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <x-table.pagination-summary :model="$setups" />
        </div>
    </div>

    <!-- Delete Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.dialog type="confirmModal">
            <x-slot name="title">Delete Setups</x-slot>

            <x-slot name="content">
                Are you sure you want to delete these setups? This action is irreversible.
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','confirm')">Cancel</x-button.secondary>
                <x-button.danger type="submit">Delete</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <!-- Create/Edit Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog type="editModal" class="modal-xl">
            <x-slot name="title">{{ $modalType }} Setup</x-slot>

            <x-slot name="content">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Title -->
                        <x-input.group label="Title" for="title" :error="$errors->first('editing.title')">
                            <x-input.text wire:model.defer="editing.title" id="title" />
                        </x-input.group>

                        <!-- Start Date Time -->
                        <x-input.group label="Start Date" for="start_date_time" :error="$errors->first('editing.loan.start_date_time')">
                            <x-input.datetime wire:model="editing.loan.start_date_time" id="start_date_time" />
                        </x-input.group>

                        <!-- End Date Time -->
                        <x-input.group label="End Date" for="end_date_time" :error="$errors->first('editing.loan.end_date_time')">
                            <x-input.datetime wire:model="editing.loan.end_date_time" id="end_date_time" relation="editing.loan" />
                        </x-input.group>

                        <!-- Users -->
                        <x-input.group label="Users" for="user_id" :error="$errors->first('editing.loan.user_id')">
                            <x-input.select wire:model.defer="editing.loan.user_id" id="user_id" placeholder="Select User" fullWidth inModal>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->forename }} {{ $user->surname }}</option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>

                        <!-- Location -->
                        <x-input.group label="Location" for="location_id" :error="$errors->first('editing.location_id')">
                            <x-input.select wire:model.defer="editing.location_id" id="location_id" placeholder="Select Location" fullWidth inModal>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>

                        <!-- Equipment -->
                        <x-input.group label="Equipment" for="equipment_id" :error="$errors->first('equipment_id')">
                            <x-input.select wire:model="equipment_id" id="equipment_id" clearSelection disabledSelected iteration="{{ $iteration }}" placeholder="Select Equipment" fullWidth inModal>
                                @foreach ($equipmentList as $equipment)
                                @if($equipment['avaliable'] == true)
                                    <option value="{{ $equipment['id'] }}">{{ $equipment['name'] }} ({{ $equipment['tag'] }})</option>
                                @else
                                    <option value="{{ $equipment['id'] }}" disabled>{{ $equipment['name'] }} ({{ $equipment['tag'] }})</option>
                                @endif
                                @endforeach
                            </x-input.select>
                        </x-input.group>

                        <!-- Details -->
                        <x-input.group label="Details" for="details" :error="$errors->first('editing.loan.details')">
                            <x-input.textarea wire:model.defer="editing.loan.details" id="details" rows="8" />
                        </x-input.group>
                    </div>

                    <div class="col-md-6">
                        <!-- Shopping Cart -->
                        <div wire:model="shoppingCart" iteration="{{ $iteration }}">
                            <x-shoppingCart.group>
                                @foreach ($shoppingCart as $key => $asset)
                                    <x-shoppingCart.cartCard id="{{ $asset['id'] }}" name="{{ $asset['name'] }}" assetId="{{ $asset['tag'] }}" returned="{{ $asset['pivot']['returned'] }}" new="{{ (int)$asset['new'] }}" />
                                @endforeach
                            </x-shoppingCart.group>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','edit')">Cancel</x-button.secondary>
                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <script>
        $(document).ready(function() {
            console.log(@entangle('editing.loan.start_date_time'));
        })
    </script>
</div>