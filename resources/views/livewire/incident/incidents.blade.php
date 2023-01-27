<div>
    <x-table.controls name="Incident" />

    <div class="row">
        <div wire:poll.10s class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('id')" :direction="$sorts['id'] ?? null" class="col-1">ID</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('start_date_time')" :direction="$sorts['start_date_time'] ?? null" class="col-1">Start Date</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('location_id')" :direction="$sorts['location_id'] ?? null" class="col-1">Location</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('distribtuion_id')" :direction="$sorts['distribution_id'] ?? null" class="col-1">Alert</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('equipment_id')" :direction="$sorts['equipment_id'] ?? null" class="col">Issues</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('evidence')" :direction="$sorts['evidence'] ?? null" class="col">Evidence</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('details')" :direction="$sorts['details'] ?? null" class="col">Details</x-table.heading>
                        <x-table.heading class="col-2"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.start_date_time" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.location_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.distribution_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"><x-input.text wire:model="filters.equipment_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"><x-input.text wire:model="filters.evidence" class="form-control-sm p-0" /></x-table.heading>
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
                                            <span>You selected <strong> {{ $incidents->count() }} </strong> incidents, do you want to select all <strong> {{ $incidents->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $incidents->total() }} </strong> incidents.</span>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($incidents as $incident)
                        <x-table.row wire:key="row-{{ $incident->id }}">
                            <x-table.cell >
                                <x-input.checkbox wire:model="selected" value="{{ $incident->id }}"></x-input.checkbox>
                            </x-table.cell>
                            <x-table.cell class="col-1"><x-link route="incidents" id="{{ $incident->id }}" value="#{{ $incident->id }}"></x-link></x-table.cell>
                            <x-table.cell class="col-1">{{ $incident->start_date_time }}</x-table.cell>
                            <x-table.cell class="col-1"><x-link route="locations" id="{{ $incident->location->id }}" value="{{ $incident->location->name }}"></x-link></x-table.cell>
                            <x-table.cell class="col-1"><x-link route="distributionGroups" id="{{ $incident->group->id }}" value="{{ $incident->group->name }}"></x-link></x-table.cell>

                            <x-table.cell class="col">
                                @foreach($incident->issues as $issue)
                                    <x-link route="equipmentIssues" id="{{ $issue->id }}" value="x{{ $issue->pivot->quantity }} {{ $issue->title }}"></x-link><br>
                                @endforeach
                            </x-table.cell>

                            <x-table.cell class="col" title="{{ $incident->evidence }}">
                                @if(strlen($incident->evidence) > 300 && !in_array('evidence-'.$incident->id, $expandedCells))
                                    {{ substr($incident->evidence, 0, 297) }}...
                                    <div><x-button.link wire:click.prevent="expandCell('evidence-{{ $incident->id }}')">Show more</x-button.link></div>
                                @elseif(in_array('evidence-'.$incident->id, $expandedCells))
                                    {{ $incident->evidence }}
                                    <div><x-button.link wire:click.prevent="collapseCell('evidence-{{ $incident->id }}')">Show less</x-button.link></div>
                                @else
                                    {{ $incident->evidence }}
                                @endif
                            </x-table.cell>
                            <x-table.cell class="col" title="{{ $incident->details }}">
                                @if(strlen($incident->details) > 300 && !in_array('details-'.$incident->id, $expandedCells))
                                    {{ substr($incident->details, 0, 297) }}...
                                    <div><x-button.link wire:click.prevent="expandCell('details-{{ $incident->id }}')">Show more</x-button.link></div>
                                @elseif(in_array('details-'.$incident->id, $expandedCells))
                                    {{ $incident->details }}
                                    <div><x-button.link wire:click.prevent="collapseCell('details-{{ $incident->id }}')">Show less</x-button.link></div>
                                @else
                                    {{ $incident->details }}
                                @endif
                            </x-table.cell>
                            <x-table.cell class="col-2">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <x-button.primary wire:click="edit({{ $incident->id }})" ><x-loading wire:target="edit({{ $incident->id }})" />Edit</x-button.primary>
                                    <x-button.success wire:click="resolve({{ $incident->id }})" ><x-loading wire:target="resolve({{ $incident->id }})" />Resolve</x-button.success>
                                </div>
                            </x-table.cell>
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

    <!-- Delete Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.dialog type="confirmModal">
            <x-slot name="title">Delete Incidents</x-slot>

            <x-slot name="content">
                Are you sure you want to delete these incidents? This action is irreversible.
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','confirm')">Cancel</x-button.secondary>
                <x-button.danger type="submit">Delete</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <!-- Resolve Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog type="resolveModal">
            <x-slot name="title">Resolve Incident #</x-slot>

            <x-slot name="content">
                <!-- Details -->
                <x-input.group for="resolution" label="Resolution" :error="$errors->first('editing.resolution')">
                    <x-input.textarea wire:model="editing.resolution" id="resolution" rows="8" />
                </x-input.group>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','resolve')">Cancel</x-button.secondary>
                <x-button.success type="submit">Resolve</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <!-- Create/Edit Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog type="editModal" class="modal-xl">
            <x-slot name="title">Edit Incident</x-slot>

            <x-slot name="content">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Start Date Time -->
                        <x-input.group for="start_date_time" label="Start Date & Time" :error="$errors->first('editing.start_date_time')">
                            <x-input.datetime wire:model="editing.start_date_time" id="start_date_time" />
                        </x-input.group>

                        <!-- Distribution Group -->
                        <x-input.group for="distribution_id" label="Alert" :error="$errors->first('editing.distribution_id')">
                            <x-input.select wire:model="editing.distribution_id" id="distribution_id" placeholder="Select who to alert" fullWidth>
                                @foreach ($distributions as $distribution)
                                    <option value="{{ $distribution->id }}">{{ $distribution->name }}</option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>

                        <!-- Location -->
                        <x-input.group for="location_id" label="Location" :error="$errors->first('editing.location_id')">
                            <x-input.select wire:model="editing.location_id" id="location_id" placeholder="Select Location" fullWidth>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>

                        <!-- Equipment Issues -->
                        <x-input.group for="equipment_id" label="Equipment Issues"  :error="$errors->first('equipment_id')">
                            <x-input.select wire:model="equipment_id" id="equipment_id" placeholder="Select Issue" clearSelection fullWidth>
                                @foreach ($equipmentIssues as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->title }}</option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>

                        <!-- Evidence -->
                        <x-input.group for="evidence" label="Evidence" :error="$errors->first('editing.evidence')">
                            <x-input.text wire:model="editing.evidence" id="evidence" />
                        </x-input.group>

                        <!-- Details -->
                        <x-input.group for="details" label="Details" :error="$errors->first('editing.details')">
                            <x-input.textarea wire:model="editing.details" id="details" rows="8" />
                        </x-input.group>
                    </div>

                    <div class="col-md-6">
                        <!-- Shopping Cart -->
                        <div wire:model="shoppingCart">
                            <x-shoppingCart.group totalCost="£{{ $shoppingCost }}" >
                                @foreach ($shoppingCart as $key => $item)
                                    <x-shoppingCart.cartCard id="{{ $item['id'] }}" name="{{ $item['title'] }}" cost="{{ $item['cost'] }}" quantity="{{ $item['pivot']['quantity'] }}" />
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