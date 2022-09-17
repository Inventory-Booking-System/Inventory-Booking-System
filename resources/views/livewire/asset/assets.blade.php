<div>
    <div class="row">
        <div class="col-lg-3 mb-3">
            <x-input.text wire:model="filters.search" placeholder="Search Assets..." />
        </div>

        <div class="col-lg-1">
            <x-button.link class="align-bottom" wire:click="$toggle('showFilters')">@if ($showFilters) Hide @endif Advanced Search...</x-button.link>
        </div>

        <div class="col-lg-8">
            <x-button.primary class="float-right" wire:click="create">New Asset</x-button.primary>
        </div>
    </div>

    <div>
        @if($showFilters)
            <div style="background:#3c9edf !important;" class="jumbotron jumbotron-fluid p-3 my-2 text-white">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <x-input.group for="name" label="Name" >
                                <x-input.text wire:model.lazy="filters.name" />
                            </x-input.group>

                            <x-input.group for="tag" label="Tag" >
                                <x-input.text wire:model.lazy="filters.tag" />
                            </x-input.group>
                        </div>

                        <div class="col-lg-6">
                            <x-input.group for="description" label="Description" >
                                <x-input.text wire:model.lazy="filters.description" />
                            </x-input.group>
                        </div>
                    </div>
                    <div class="row">
                        <x-button.link wire:click="resetFilters">Reset Filters...</x-button.link>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading sortable wire:click="sortBy('name')" :direction="$sortField === 'name' ? $sortDirection : null" width="3">Name</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('tag')" :direction="$sortField === 'tag' ? $sortDirection : null" width="2">Tag</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('description')" :direction="$sortField === 'description' ? $sortDirection : null" width="7">Description</x-table.heading>
                        <x-table.heading />
                    </x-table.row>
                </x-slot>

                <x-slot name="body">
                    @forelse ($assets as $asset)
                        <x-table.row :wire:key="$loop->index">
                            <x-table.cell width="3">{{ $asset->name }}</x-table.cell>
                            <x-table.cell width="2">{{ $asset->tag }}</x-table.cell>
                            <x-table.cell width="5">{{ $asset->description }}</x-table.cell>
                            <x-table.cell width="2">
                                <x-button.primary wire:click="edit({{ $asset->id }})" >Edit</x-button.primary>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    No assets found
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <div class="row mt-2">
                <div class="col-lg-3 d-flex flex-row">
                    <span>Showing {{ ($assets->currentPage() * $assets->count()) - ($assets->count() - 1) }} to {{ $assets->currentPage() * $assets->count() }} of {{ $assets->total() }} results</span>
                </div>
                <div class="col-lg-9 d-flex flex-row-reverse">
                    {{ $assets->links() }}
                </div>
            </div>
        </div>
    </div>

    <button type="button" wire:click="edit()">
        Test Modal {{ $counter }}
    </button>

    <form wire:submit.prevent="save">
        <x-modal.dialog>
            <x-slot name="title">Edit Asset</x-slot>

            <x-slot name="content">
                <x-input.group for="name" label="Name" :error="$errors->first('editing.name')">
                    <x-input.text wire:model="editing.name" id="name" />
                </x-input.group>

                <x-input.group for="tag" label="Tag" :error="$errors->first('editing.tag')">
                    <x-input.text wire:model="editing.tag" id="tag" />
                </x-input.group>

                <x-input.group for="description" label="Description" :error="$errors->first('editing.description')">
                    <x-input.text wire:model="editing.description" id="description" />
                </x-input.group>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal')">Cancel</x-button.secondary>
                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>
</div>