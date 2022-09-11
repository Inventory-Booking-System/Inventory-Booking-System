<div>
    <div class="row">
        <div class="col-lg-3 mb-3">
            <x-input.text wire:model="search" placeholder="Search Assets..." />
        </div>

        <div class="col-lg-6">
            <button type="button" class="btn btn-primary">Create new Asset</button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading sortable wire:click="sortBy('name')" width="3">Name</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('tag')" width="2">Tag</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('description')" width="7">Description</x-table.heading>
                    </x-table.row>
                </x-slot>

                <x-slot name="body">
                    @forelse ($assets as $asset)
                        <x-table.row :wire:key="$loop->index">
                            <x-table.cell width="3">{{ $asset->name }}</x-table.cell>
                            <x-table.cell width="2">{{ $asset->tag }}</x-table.cell>
                            <x-table.cell width="7">{{ $asset->description }}</x-table.cell>
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
</div>