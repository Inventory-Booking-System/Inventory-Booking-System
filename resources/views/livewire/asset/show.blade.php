<div class="row" >
    <div class="col-lg-4">
        <div class="row">
            <div class="card w-100 mr-3">
                <div class="card-header bg-dark text-center">
                    <h1>{{ $asset->name }} ({{ $asset->tag }})</h1>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $asset->description }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row">
            <div class="col-lg-3 mb-3">
                <x-input.text wire:model="filters.search" placeholder="Search Loans & Setups..." />
            </div>

            <div class="col-lg-2">
                <x-button.primary wire:loading.style.delay='"' class="" wire:click="$toggle('showFilters')">Toggle Filters</x-button.primary>
            </div>

            <div class="col-lg-2" >
                <x-input.select wire:model="perPage" id="perPage" label="Per Page">
                    <option value="2">2</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </x-input.select>
            </div>
        </div>

        <x-table>
            <x-slot name="head">
                <x-table.row>
                    <x-table.heading sortable wire:click="sortBy('id')" :direction="$sorts['id'] ?? null" class="col-1">ID</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('user_id')" :direction="$sorts['user_id'] ?? null" class="col-2">User</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('status_id')" :direction="$sorts['status_id'] ?? null" class="col-1">Status</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('start_date_time')" :direction="$sorts['start_date_time'] ?? null" class="col-2">Start Date</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('end_date_time')" :direction="$sorts['end_date_time'] ?? null" class="col-2">End Date</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('details')" :direction="$sorts['details'] ?? null" class="col-2">Details</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('assets')" :direction="$sorts['assets'] ?? null" class="col-2">Assets</x-table.heading>
                </x-table.row>

                @if($showFilters)
                    <x-table.row>
                        <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.id" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.user_id" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.status_id" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.start_date_time" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.end_date_time" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.details" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.assets" class="form-control-sm p-0" /></x-table.heading>
                    </x-table.row>
                @endif
            </x-slot>

            <x-slot name="body">
                @forelse ($loans as $loan)
                    <x-table.row wire:key="row-{{ $loan->id }}">
                        <x-table.cell class="col-1"><x-link route="loans" id="{{ $loan->id }}" value="#{{ $loan->id }}"></x-link></x-table.cell>
                        <x-table.cell class="col-2"><x-link route="users" id="{{ $loan->user->id }}" value="{{ $loan->user->forename }} {{ $loan->user->surname }}"></x-link></x-table.cell>
                        <x-table.cell class="col-1"><span class="badge badge-pill badge-{{ $loan->status_type }}">{{ $loan->status }}</span></x-table.cell>
                        <x-table.cell class="col-2">{{ $loan->start_date_time }}</x-table.cell>
                        <x-table.cell class="col-2">{{ $loan->end_date_time }}</x-table.cell>
                        <x-table.cell class="col-2">{{ $loan->details }}</x-table.cell>
                        <x-table.cell class="col-2">
                            @foreach($loan->assets as $asset)
                                <x-link route="assets" id="{{ $asset->id }}" value="{{ $asset->name }} ({{ $asset->tag }})"></x-link><br>
                            @endforeach
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

        <x-table.pagination-summary :model="$loans" />
    </div>
</div>