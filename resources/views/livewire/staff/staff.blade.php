<div>
    <x-table.controls name="Staff" perPage="{{ $perPage }}" deleteLabel="Archive" />

    <div class="row">
        <div wire:poll.10s class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('user_full_name')" :direction="$sorts['user_full_name'] ?? null" class="col-3">Name</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('email')" :direction="$sorts['email'] ?? null" class="col-4">Email</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('has_account')" :direction="$sorts['has_account'] ?? null" class="col-2">Dashboard Access</x-table.heading>
                        <x-table.heading class="col"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-3" direction="null"><x-input.text wire:model="filters.user_id" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-4" direction="null"><x-input.text wire:model="filters.email" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null" />
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
                                            <span>You selected <strong> {{ $staffMembers->count() }} </strong> staff, do you want to select all <strong> {{ $staffMembers->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $staffMembers->total() }} </strong> staff.</span>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($staffMembers as $staff)
                        <x-table.row wire:key="row-{{ $staff->id }}">
                            <x-table.cell>
                                <x-input.checkbox wire:model="selected" value="{{ $staff->id }}"></x-input.checkbox>
                            </x-table.cell>
                            <x-table.cell class="col-3"><x-link route="staff" id="{{ $staff->id }}" value="{{ $staff->forename }} {{ $staff->surname }}"></x-link></x-table.cell>
                            <x-table.cell class="col-4">{{ $staff->email }}</x-table.cell>
                            <x-table.cell class="col-2">{{ $staff->has_account ? 'Yes' : 'No' }}</x-table.cell>
                            <x-table.cell class="col">
                                <x-button.primary wire:click="edit({{ $staff->id }})" ><x-loading wire:target="edit({{ $staff->id }})" />Edit</x-button.primary>
                                @if($staff->has_account)
                                    <x-button.danger wire:click="resetPassword({{ $staff->id }})" ><x-loading wire:target="resetPassword({{ $staff->id }})" />Reset Password</x-button.danger>
                                @endif
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    No staff found
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <x-table.pagination-summary :model="$staffMembers" />
        </div>
    </div>

    <!-- Archive Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.dialog type="confirmModal">
            <x-slot name="title">Archive Staff</x-slot>

            <x-slot name="content">
                Are you sure you want to archive these staff members? They can be restored from the Settings page.
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','confirm')">Cancel</x-button.secondary>
                <x-button.danger type="submit">Archive</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <!-- Create/Edit Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog type="editModal">
            <x-slot name="title">{{ $modalType }} Staff Member</x-slot>

            <x-slot name="content">
                <x-input.group for="forename" label="Forename" :error="$errors->first('editing.forename')">
                    <x-input.text wire:model.defer="editing.forename" id="forename" />
                </x-input.group>

                <x-input.group for="surname" label="Surname" :error="$errors->first('editing.surname')">
                    <x-input.text wire:model.defer="editing.surname" id="surname" />
                </x-input.group>

                <x-input.group for="email" label="Email" :error="$errors->first('editing.email')">
                    <x-input.text wire:model.defer="editing.email" id="email" />
                </x-input.group>

                <x-input.group for="has_account" label="Enable Dashboard Access" :error="$errors->first('editing.has_account')">
                    <x-input.checkbox wire:model.defer="editing.has_account" id="has_account" />
                </x-input.group>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','edit')">Cancel</x-button.secondary>
                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>
</div>
