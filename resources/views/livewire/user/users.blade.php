<div>
    <x-table.controls name="Student" perPage="{{ $perPage }}" deleteLabel="Archive" />

    <div class="row">
        <div wire:poll.10s class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('user_full_name')" :direction="$sorts['user_full_name'] ?? null" class="col-2">User</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('email')" :direction="$sorts['email'] ?? null" class="col-2">Email</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('description')" :direction="$sorts['description'] ?? null" class="col-3">Description</x-table.heading>
                        <x-table.heading class="col-2">Groups</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('booking_authoriser_user_id')" :direction="$sorts['booking_authoriser_user_id'] ?? null" class="col-2" title="Checkout Authoriser">Checkout Authoriser</x-table.heading>
                        <x-table.heading class="col"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.user_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.email" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-3" direction="null"><x-input.text wire:model="filters.description" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null" />
                            <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.booking_authoriser_user_id" class="form-control-sm p-0" /></x-table.heading>
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
                                            <span>You selected <strong> {{ $students->count() }} </strong> students, do you want to select all <strong> {{ $students->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $students->total() }} </strong> students.</span>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($students as $user)
                        <x-table.row wire:key="row-{{ $user->id }}">
                            <x-table.cell >
                                <x-input.checkbox wire:model="selected" value="{{ $user->id }}"></x-input.checkbox>
                            </x-table.cell>
                            <x-table.cell class="col-2"><x-link route="users" id="{{ $user->id }}" value="{{ $user->forename }} {{ $user->surname }}"></x-link></x-table.cell>
                            <x-table.cell class="col-2">{{ $user->email }}</x-table.cell>
                            <x-table.cell class="col-3">{{ $user->description ?: '-' }}</x-table.cell>
                            <x-table.cell class="col-2">
                                @forelse ($user->distributionGroups as $group)
                                    <x-link route="user-groups" id="{{ $group->id }}" value="{{ $group->name }}"></x-link><br>
                                @empty
                                    -
                                @endforelse
                            </x-table.cell>
                            <x-table.cell class="col-2"><x-link route="users" id="{{ $user->bookingAuthoriser->id ?? '' }}" value="{{ $user->bookingAuthoriser->forename ?? '' }} {{ $user->bookingAuthoriser->surname ?? '' }}"></x-link></x-table.cell>
                            <x-table.cell class="col">
                                <x-button.primary wire:click="edit({{ $user->id }})" ><x-loading wire:target="edit({{ $user->id }})" />Edit</x-button.primary>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    No students found
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <x-table.pagination-summary :model="$students" />
        </div>
    </div>

    <!-- Archive Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.dialog type="confirmModal">
            <x-slot name="title">Archive Students</x-slot>

            <x-slot name="content">
                Are you sure you want to archive these students? They can be restored from the Settings page.
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','confirm')">Cancel</x-button.secondary>
                <x-button.danger type="submit">Archive</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <!-- Create/Edit Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog type="editModal" class="modal-xl">
            <x-slot name="title">{{ $modalType }} Student</x-slot>

            <x-slot name="content">
                <div class="row">
                    <div class="col-md-6">
                        <x-input.group for="forename" label="Forename" :error="$errors->first('editing.forename')">
                            <x-input.text wire:model.defer="editing.forename" id="forename" />
                        </x-input.group>

                        <x-input.group for="surname" label="Surname" :error="$errors->first('editing.surname')">
                            <x-input.text wire:model.defer="editing.surname" id="surname" />
                        </x-input.group>

                        <x-input.group for="email" label="Email" :error="$errors->first('editing.email')">
                            <x-input.text wire:model.defer="editing.email" id="email" />
                        </x-input.group>

                        <x-input.group for="description" label="Description" :error="$errors->first('editing.description')">
                            <x-input.textarea wire:model.defer="editing.description" id="description" rows="6" />
                        </x-input.group>

                        <x-input.group label="Checkout Screen Authoriser" for="booking_authoriser_user_id" :error="$errors->first('editing.booking_authoriser_user_id')">
                            <x-input.select wire:model="editing.booking_authoriser_user_id" id="booking_authoriser_user_id" iteration="{{ $key }}">
                                <option value="">— None —</option>
                                @foreach ($allStaff as $staff)
                                    <option
                                        value="{{ $staff['id'] }}"
                                        @if (isset($editing->bookingAuthoriser->id) && $staff['id'] === $editing->bookingAuthoriser->id) selected @endif
                                    >
                                        {{ $staff['forename'] }} {{ $staff['surname'] }}
                                    </option>
                                @endforeach
                            </x-input.select>
                        </x-input.group>
                    </div>

                    <div class="col-md-6">
                        <x-input.group for="selected_group_ids" label="User Groups" :error="$errors->first('selectedGroupIds') ?: $errors->first('selectedGroupIds.*')">
                            <div class="border rounded p-2 ps-3" style="max-height: 280px; overflow-y: auto;">
                                @forelse ($allGroups as $group)
                                    @php
                                        $groupSelected = in_array((string) $group->id, array_map('strval', $selectedGroupIds ?? []));
                                        $expiryEnabled = $groupSelected && ($groupExpiryEnabled[(string) $group->id] ?? false);
                                    @endphp
                                    <div class="mb-2 d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center" style="flex:1; min-width:0;">
                                            <x-input.checkbox wire:model="selectedGroupIds" id="modal_group_{{ $group->id }}" value="{{ $group->id }}" style="margin-right: 0.5rem;" />
                                            <label class="form-check-label mb-0 text-truncate" for="modal_group_{{ $group->id }}" title="{{ $group->name }}">{{ $group->name }}</label>
                                        </div>
                                        <div class="d-flex align-items-center gap-1 flex-shrink-0" style="width:225px; {{ $groupSelected ? '' : 'opacity:0.35;' }}; justify-content: flex-end;">
                                            <input
                                                type="checkbox"
                                                wire:model="groupExpiryEnabled.{{ $group->id }}"
                                                id="expiry_toggle_{{ $group->id }}"
                                                style="margin-right: 0.5rem;"
                                                @unless($groupSelected) disabled @endunless
                                            />
                                            <label class="mb-0 text-muted small text-nowrap" for="expiry_toggle_{{ $group->id }}" style="margin-right: 0.5rem;">Expires</label>
                                            <x-input.text
                                                wire:model.defer="groupExpiries.{{ $group->id }}"
                                                type="date"
                                                class="form-control-sm"
                                            />
                                        </div>
                                    </div>
                                @empty
                                    <p class="mb-0">No user groups found.</p>
                                @endforelse
                            </div>
                        </x-input.group>
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