<div>
    <div class="row">
        <div class="col-lg-3 mb-3">
            <x-input.text wire:model="filters.search" placeholder="Search Loans..." />
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
            <x-button.primary class="float-right mx-2 px-5" wire:click="create">New Loan</x-button.primary>
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
                        <x-table.heading sortable wire:click="sortBy('id')" :direction="$sorts['id'] ?? null" class="col-1">ID</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('user_id')" :direction="$sorts['user_id'] ?? null" class="col-2">User</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('status_id')" :direction="$sorts['status_id'] ?? null" class="col-1">Status ID</x-table.heading>
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
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.status_id" class="form-control-sm p-0" /></x-table.heading>
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
                                            <span>You selected <strong> {{ $loans->count() }} </strong> loans, do you want to select all <strong> {{ $loans->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $loans->total() }} </strong> loans.</span>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($loans as $loan)
                        <x-table.row wire:key="row-{{ $loan->id }}">
                            <x-table.cell>
                                <x-input.checkbox wire:model="selected" value="{{ $loan->id }}"></x-input.checkbox>
                            </x-table.cell>
                            <x-table.cell class="col-1">#{{ $loan->id }}</x-table.cell>
                            <x-table.cell class="col-2">{{ $loan->user->forename }} {{ $loan->user->surname }}</x-table.cell>
                            <x-table.cell class="col-1">{{ $loan->status_id }}</x-table.cell>
                            <x-table.cell class="col-1">{{ $loan->start_date_time }}</x-table.cell>
                            <x-table.cell class="col-1">{{ $loan->end_date_time }}</x-table.cell>
                            <x-table.cell class="col-2">{{ $loan->details }}</x-table.cell>
                            <x-table.cell class="col-2">
                                @foreach($loan->assets as $asset)
                                    {{ $asset->name }} ({{ $asset->tag }})<br>
                                @endforeach
                            </x-table.cell>
                            <x-table.cell class="col">
                                <x-button.primary wire:click="edit({{ $loan->id }})" ><x-loading wire:target="edit({{ $loan->id }})" />Edit</x-button.primary>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    No loans found
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <div class="row mt-2">
                <div class="col-lg-3 d-flex flex-row">
                    <span>Showing {{ ($loans->currentPage() * $loans->count()) - ($loans->count() - 1) }} to {{ $loans->currentPage() * $loans->count() }} of {{ $loans->total() }} results</span>
                </div>
                <div class="col-lg-9 d-flex flex-row-reverse">
                    {{ $loans->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.dialog type="confirmModal">
            <x-slot name="title">Delete Loans</x-slot>

            <x-slot name="content">
                Are you sure you want to delete these loans? This action is irreversible.
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
            <x-slot name="title">Edit Loan</x-slot>

            <x-slot name="content">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Start Date Time -->
                        <x-input.group label="Start Date" for="start_date_time" :error="$errors->first('editing.start_date_time')">
                            <x-input.datetime wire:model="editing.start_date_time" id="start_date_time" />
                        </x-input.group>

                        <!-- End Date Time -->
                        <x-input.group label="End Date" for="end_date_time" :error="$errors->first('editing.end_date_time')">
                            <x-input.datetime wire:model="editing.end_date_time" id="end_date_time" />
                        </x-input.group>

                        <!-- Users -->
                        <x-input.group label="Users" for="user_id" :error="$errors->first('editing.user_id')">
                            <x-input.select wire:model="editing.user_id" id="user_id" placeholder="Select User">
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
                        <x-input.group label="Details" for="details" :error="$errors->first('editing.details')">
                            <x-input.textarea wire:model="editing.details" id="details" rows="8" />
                        </x-input.group>

                        <!-- Reservation -->
                        <x-input.group label="Reservation" for="status_id" :error="$errors->first('editing.status_id')" buttonGroup>
                            <x-input.radioButton wire:model="editing.status_id" id="status_id_yes" value="1" text="Yes" checked="{{ $status_id }}" />
                            <x-input.radioButton wire:model="editing.status_id" id="status_id_no" value="0" text="No" checked="{{ $status_id }}" />
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