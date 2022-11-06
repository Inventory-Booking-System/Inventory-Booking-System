<div>
    <div class="row">
        <div class="col-lg-3 mb-3">
            <x-input.text wire:model="filters.search" placeholder="Search Setups..." />
        </div>

        <div class="col-lg-1">
            <x-button.primary wire:loading.style.delay='"' class="" wire:click="$toggle('showFilters')">Toggle Filters</x-button.primary>
        </div>

        <div class="col-lg-1" >
            <x-input.select wire:model="perPage" id="perPage" label="Per Page">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </x-input.select>
        </div>

        <div class="col">
            <x-dropdown class="float-right" label="Actions">
                <x-dropdown.item wire:click="exportSelected">Export</x-dropdown.item>
                <x-dropdown.item wire:click="$emit('showModal','confirm')">Delete</x-dropdown.item>
            </x-dropdown>
            <x-button.primary class="float-right mx-2 px-5" wire:click="create">New Setup</x-button.primary>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('user_id')" :direction="$sorts['user_id'] ?? null" class="col-3">User ID</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('status_id')" :direction="$sorts['status_id'] ?? null" class="col-3">Status ID</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('start_date_time')" :direction="$sorts['start_date_time'] ?? null" class="col-3">Start Date & Time</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('end_date_time')" :direction="$sorts['end_date_time'] ?? null" class="col-3">End Date & Time</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('title')" :direction="$sorts['title'] ?? null" class="col-3">Title</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('location_id')" :direction="$sorts['location_id'] ?? null" class="col-1">Location ID</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('details')" :direction="$sorts['details'] ?? null" class="col">Details</x-table.heading>
                        <x-table.heading class="col-2"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-3" direction="null"><x-input.text wire:model="filters.user_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.status_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"><x-input.text wire:model="filters.start_date_time" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"><x-input.text wire:model="filters.end_date_time" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"><x-input.text wire:model="filters.title" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"><x-input.text wire:model="filters.location_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"><x-input.text wire:model="filters.details" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null"/>
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
                        <x-table.row wire:key="row-{{ $setup->id }}">
                            <x-table.cell >
                                <x-input.checkbox wire:model="selected" value="{{ $setup->id }}"></x-input.checkbox>
                            </x-table.cell>
                            <x-table.cell class="col-3">{{ $setup->user_id }}</x-table.cell>
                            <x-table.cell class="col-1">{{ $setup->status_id }}</x-table.cell>
                            <x-table.cell class="col">{{ $setup->start_date_time }}</x-table.cell>
                            <x-table.cell class="col">{{ $setup->end_date_time }}</x-table.cell>
                            <x-table.cell class="col">{{ $setup->title }}</x-table.cell>
                            <x-table.cell class="col">{{ $setup->location_id }}</x-table.cell>
                            <x-table.cell class="col">{{ $setup->details }}</x-table.cell>
                            <x-table.cell class="col-2">
                                <x-button.primary wire:click="edit({{ $setup->id }})" ><x-loading wire:target="edit({{ $setup->id }})" />Edit</x-button.primary>
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

            <div class="row mt-2">
                <div class="col-lg-3 d-flex flex-row">
                    <span>Showing {{ ($setups->currentPage() * $setups->count()) - ($setups->count() - 1) }} to {{ $setups->currentPage() * $setups->count() }} of {{ $setups->total() }} results</span>
                </div>
                <div class="col-lg-9 d-flex flex-row-reverse">
                    {{ $setups->links() }}
                </div>
            </div>
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
            <x-slot name="title">Edit Setup</x-slot>

            <x-slot name="content">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Title -->
                        <x-input.group label="Title" for="title" :error="$errors->first('editing.title')">
                            <x-input.text wire:model="editing.title" id="title" />
                        </x-input.group>

                        <!-- Start Date Time -->
                        <x-input.group label="Start Date" for="start_date_time" :error="$errors->first('editing.loan.start_date_time')">
                            <x-input.datetime wire:model="editing.loan.start_date_time" id="start_date_time" />
                        </x-input.group>

                        <!-- End Date Time -->
                        <x-input.group label="End Date" for="end_date_time" :error="$errors->first('editing.loan.end_date_time')">
                            <x-input.datetime wire:model="editing.loan.end_date_time" id="end_date_time" />
                        </x-input.group>

                        <!-- Users -->
                        <x-input.group label="Users" for="user_id" :error="$errors->first('editing.loan.user_id')">
                            <x-input.select wire:model="editing.loan.user_id" id="user_id" placeholder="Select User">
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->forename }} {{ $user->surname }}</option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>

                        <!-- Location -->
                        <x-input.group label="Location" for="location_id" :error="$errors->first('editing.location_id')">
                            <x-input.select wire:model="editing.location_id" id="location_id" placeholder="Select Location">
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
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
                        <x-input.group label="Details" for="details" :error="$errors->first('editing.loan.details')">
                            <x-input.textarea wire:model="editing.loan.details" id="details" rows="8" />
                        </x-input.group>
                    </div>

                    <div class="col-md-6">
                        <!-- Shopping Cart -->
                        <div wire:model="shoppingCart">
                            <x-shoppingCart.group totalCost="£{{ $shoppingCost }}" >
                                @foreach ($shoppingCart as $key => $item)
                                    <x-shoppingCart.cartCard id="{{ $key }}" name="{{ $item['title'] }}" assetId="{{ $item['asset_id'] }}" returned="{{ $item['returned'] }}" />
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
</div>