<div>
    <div class="row">
        <div class="col-lg-3 mb-3">
            <x-input.text wire:model="filters.search" placeholder="Search Locations..." />
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
            <x-button.primary class="float-right mx-2 px-5" wire:click="create">New Location</x-button.primary>
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
                        <x-table.heading sortable wire:click="sortBy('name')" :direction="$sorts['name'] ?? null" class="col-6">Name</x-table.heading>
                        <x-table.heading class="col-6"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-6" direction="null"><x-input.text wire:model="filters.name" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-6" direction="null"/>
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
                                            <span>You selected <strong> {{ $locations->count() }} </strong> locations, do you want to select all <strong> {{ $locations->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $locations->total() }} </strong> locations.</span>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($locations as $location)
                        <x-table.row wire:key="row-{{ $location->id }}">
                            <x-table.cell >
                                <x-input.checkbox wire:model="selected" value="{{ $location->id }}"></x-input.checkbox>
                            </x-table.cell>
                            <x-table.cell class="col-6">{{ $location->name }}</x-table.cell>
                            <x-table.cell class="col-6">
                                <x-button.primary wire:click="edit({{ $location->id }})" ><x-loading wire:target="edit({{ $location->id }})" />Edit</x-button.primary>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    No locations found
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <div class="row mt-2">
                <div class="col-lg-3 d-flex flex-row">
                    <span>Showing {{ ($locations->currentPage() * $locations->count()) - ($locations->count() - 1) }} to {{ $locations->currentPage() * $locations->count() }} of {{ $locations->total() }} results</span>
                </div>
                <div class="col-lg-9 d-flex flex-row-reverse">
                    {{ $locations->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.dialog type="confirmModal">
            <x-slot name="title">Delete Locations</x-slot>

            <x-slot name="content">
                Are you sure you want to delete these locations? This action is irreversible.
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','confirm')">Cancel</x-button.secondary>
                <x-button.danger type="submit">Delete</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <!-- Create/Edit Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog type="editModal">
            <x-slot name="title">Edit Location</x-slot>

            <x-slot name="content">
                <x-input.group for="name" label="Name" :error="$errors->first('editing.name')">
                    <x-input.text wire:model="editing.name" id="name" />
                </x-input.group>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','edit')">Cancel</x-button.secondary>
                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>
</div>