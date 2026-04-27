<div>
    <x-table.controls name="Loan" perPage="{{ $perPage }}" legacyModal="{{ false }}" />

    <div class="row">
        <div wire:poll.10s class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('id')" :direction="$sorts['id'] ?? null" class="col-1">ID</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('user_full_name')" :direction="$sorts['user_full_name'] ?? null" class="col-2">User</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('status_id')" :direction="$sorts['status_id'] ?? null" class="col-1">Status</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('start_date_time')" :direction="$sorts['start_date_time'] ?? null" class="col-1">Start Date</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('end_date_time')" :direction="$sorts['end_date_time'] ?? null" class="col-1">End Date</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('details')" :direction="$sorts['details'] ?? null" class="col-2">Details</x-table.heading>
                        <x-table.heading class="col-2">Assets</x-table.heading>
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
                            <x-table.cell class="col-1"><x-link route="loans" id="{{ $loan->id }}" value="#{{ $loan->id }}"></x-link></x-table.cell>
                            <x-table.cell class="col-2"><x-link route="users" id="{{ $loan->user->id }}" value="{{ $loan->user->forename }} {{ $loan->user->surname }}"></x-link></x-table.cell>
                            <x-table.cell class="col-1"><span class="badge badge-pill badge-{{ $loan->status_type }}">{{ $loan->status }}</span></x-table.cell>
                            <x-table.cell class="col-1">{{ $loan->start_date_time }}</x-table.cell>
                            <x-table.cell class="col-1">{{ $loan->end_date_time }}</x-table.cell>
                            <x-table.cell class="col-2" title="{{ $loan->details }}">
                                @if(strlen($loan->details) > 300 && !in_array('details-'.$loan->id, $expandedCells))
                                    {{ substr($loan->details, 0, 297) }}...
                                    <div><x-button.link wire:click.prevent="expandCell('details-{{ $loan->id }}')"><strong>Show more</strong></x-button.link></div>
                                @elseif(in_array('details-'.$loan->id, $expandedCells))
                                    {{ $loan->details }}
                                    <div><x-button.link wire:click.prevent="collapseCell('details-{{ $loan->id }}')"><strong>Show less</strong></x-button.link></div>
                                @else
                                    {{ $loan->details }}
                                @endif
                            </x-table.cell>
                            <x-table.cell class="col-2">
                                
                                @if(
                                    (
                                        !(new Carbon\Carbon($loan->start_date_time))->isCurrentDay() && 
                                        !(new Carbon\Carbon($loan->end_date_time))->isCurrentDay()
                                    ) && 
                                    count($loan->assets) > 9 && 
                                    !in_array('assets-'.$loan->id, $expandedCells)
                                )
                                    @for($i = 0; $i < 9; $i++)
                                        <x-link route="assets" id="{{ $loan->assets[$i]->id }}" value="{{ $loan->assets[$i]->name }} ({{ $loan->assets[$i]->tag }})" lineThrough="{{ $loan->assets[$i]->pivot->returned }}"></x-link><br>
                                    @endfor
                                    <div><x-button.link wire:click.prevent="expandCell('assets-{{ $loan->id }}')"><strong>Show {{ count($loan->assets) - 9 }} more</strong></x-button.link></div>
                                @elseif(in_array('assets-'.$loan->id, $expandedCells))
                                    @foreach($loan->assets as $asset)
                                        <x-link route="assets" id="{{ $asset->id }}" value="{{ $asset->name }} ({{ $asset->tag }})" lineThrough="{{ $asset->pivot->returned }}"></x-link><br>
                                    @endforeach
                                    <div><x-button.link wire:click.prevent="collapseCell('assets-{{ $loan->id }}')"><strong>Show less</strong></x-button.link></div>
                                @else
                                    @foreach($loan->assetGroups as $assetGroup)
                                        <x-link route="asset-groups" id="{{ $assetGroup->id }}" value="{{ $assetGroup->name }} (x{{ $assetGroup->pivot->quantity }})" class="text-secondary"></x-link><br>
                                    @endforeach
                                    @foreach($loan->assets as $asset)
                                        <x-link route="assets" id="{{ $asset->id }}" value="{{ $asset->name }} ({{ $asset->tag }})" lineThrough="{{ $asset->pivot->returned }}"></x-link><br>
                                    @endforeach
                                @endif

                            </x-table.cell>
                            <x-table.cell class="col">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    @if($loan->status_id == 1)
                                        <x-button.success wire:click="book({{ $loan->id }})" ><x-loading wire:target="book({{ $loan->id }})" />Book Out</x-button.success>
                                        <x-button.danger wire:click="cancel({{ $loan->id }})" ><x-loading wire:target="cancel({{ $loan->id }})" />Cancel</x-button.danger>
                                    @else
                                        <x-button.success wire:click="complete({{ $loan->id }})" ><x-loading wire:target="complete({{ $loan->id }})" />Complete</x-button.success>
                                    @endif
                                    <x-button.primary class="edit-button" data-loan="{{ $loan->toJSON() }}">Edit</x-button.primary>
                                </div>
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
    
    <!-- React component -->
    <div id="create-edit-modal"></div>

    <script src="{{ mix('js/vendor-react.js') }}"></script>
    <script src="{{ mix('js/loans.js') }}"></script>
</div>