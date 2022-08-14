<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="col-lg-3 mb-3 px-0">
            <x-input.text wire:model="search" placeholder="Search Assets..." />
        </div>

        <x-table>
            <x-slot name="head">
                <x-table.heading>Name</x-table.heading>
                <x-table.heading>Tag</x-table.heading>
                <x-table.heading>Description</x-table.heading>
            </x-slot>

            <x-slot name="body">
                @foreach ($assets as $asset)
                    <x-table.row :wire:key="$loop->index">
                        <x-table.cell>{{ $asset->name }}</x-table.cell>
                        <x-table.cell>{{ $asset->tag }}</x-table.cell>
                        <x-table.cell>{{ $asset->description }}</x-table.cell>
                    </x-table.row>
                @endforeach
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