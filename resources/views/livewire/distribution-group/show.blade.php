<div class="row" >
    <div class="col-lg-4">
        <div class="row">
            <div class="card w-100 mr-3">
                <div class="card-header bg-dark text-center">
                    <h1>{{ $distributionGroup->name }}</h1>
                </div>
                <div class="card-body">
                    <strong>Created Date:</strong><p class="card-text">{{ $distributionGroup->humanFormat($distributionGroup->created_at) }}</p>
                    <strong>Last Updated:</strong><p class="card-text">{{ $distributionGroup->humanFormat($distributionGroup->updated_at) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div wire:poll.10s class="col-lg-8">
        <div class="row">
            <div class="col-lg-3 mb-3">
                <x-input.text wire:model="filters.search" placeholder="Search Incidents..." />
            </div>

            <div class="col-lg-2">
                <x-button.primary wire:loading.style.delay='"' class="" wire:click="$toggle('showFilters')">Toggle Filters</x-button.primary>
            </div>

            <div class="col-lg-2" >
                <x-input.select wire:model="perPage" id="perPage" label="Per Page">
                    <option value="10" @if($perPage === 10) selected @endif>10</option>
                    <option value="25" @if($perPage === 25) selected @endif>25</option>
                    <option value="50" @if($perPage === 50) selected @endif>50</option>
                </x-input.select>
            </div>
        </div>

        <x-table>
            <x-slot name="head">
                <x-table.row>
                    <x-table.heading sortable wire:click="sortBy('id')" :direction="$sorts['id'] ?? null" class="col-1">ID</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('status_id')" :direction="$sorts['status_id'] ?? null" class="col-1">Status</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('start_date_time')" :direction="$sorts['start_date_time'] ?? null" class="col-2">Start Date</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('locations.name')" :direction="$sorts['locations.name'] ?? null" class="col-1">Location</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('equipment_id')" :direction="$sorts['equipment_id'] ?? null" class="col-2">Issues</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('evidence')" :direction="$sorts['evidence'] ?? null" class="col-2">Evidence</x-table.heading>
                    <x-table.heading sortable wire:click="sortBy('details')" :direction="$sorts['details'] ?? null" class="col-2">Details</x-table.heading>
                </x-table.row>

                @if($showFilters)
                    <x-table.row>
                        <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.id" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.status_id" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.start_date_time" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.location_id" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.equipment_id" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.evidence" class="form-control-sm p-0" /></x-table.heading>
                        <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.details" class="form-control-sm p-0" /></x-table.heading>
                    </x-table.row>
                @endif
            </x-slot>

            <x-slot name="body">
                @forelse ($incidents as $incident)
                    <x-table.row wire:key="row-{{ $incident->id }}">
                        <x-table.cell class="col-1"><x-link route="incidents" id="{{ $incident->id }}" value="#{{ $incident->id }}"></x-link></x-table.cell>
                        <x-table.cell class="col-1"><span class="badge badge-pill badge-{{ $incident->status_type }}">{{ $incident->status }}</span></x-table.cell>
                        <x-table.cell class="col-2">{{ $incident->start_date_time }}</x-table.cell>
                        <x-table.cell class="col-1"><x-link route="locations" id="{{ $incident->location->id }}" value="{{ $incident->location->name }}"></x-link></x-table.cell>
                        <x-table.cell class="col-2">
                            @foreach($incident->issues as $issue)
                                <x-link route="equipmentIssues" id="{{ $issue->id }}" value="x{{ $issue->pivot->quantity }} {{ $issue->title }}"></x-link><br>
                            @endforeach
                        </x-table.cell>
                        <x-table.cell class="col-2">{{ $incident->evidence }}</x-table.cell>
                        <x-table.cell class="col-2">{{ $incident->details }}</x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell width="12">
                            <div class="d-flex justify-content-center">
                                No incidents found
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-slot>
        </x-table>

        <x-table.pagination-summary :model="$incidents" />
    </div>
</div>