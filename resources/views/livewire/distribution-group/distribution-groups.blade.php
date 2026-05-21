<div>
    <x-table.controls name="User Group" perPage="{{ $perPage }}" />

    <div class="row">
        <div wire:poll.10s class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('name')" :direction="$sorts['name'] ?? null" class="col-3">Name</x-table.heading>
                        <x-table.heading class="col-2">Student Checkout</x-table.heading>
                        <x-table.heading class="col-1">Members</x-table.heading>
                        <x-table.heading class="col"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-3" direction="null"><x-input.text wire:model="filters.name" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null" />
                            <x-table.heading class="col-2" direction="null" />
                            <x-table.heading class="col-1" direction="null" />
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
                                            <span>You selected <strong> {{ $distributionGroups->count() }} </strong> user groups, do you want to select all <strong> {{ $distributionGroups->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $distributionGroups->total() }} </strong> user groups.</span>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($distributionGroups as $distributionGroup)
                        <x-table.row wire:key="row-{{ $distributionGroup->id }}">
                            <x-table.cell>
                                <x-input.checkbox wire:model="selected" value="{{ $distributionGroup->id }}"></x-input.checkbox>
                            </x-table.cell>
                            <x-table.cell class="col-3"><x-link route="user-groups" id="{{ $distributionGroup->id }}" value="{{ $distributionGroup->name }}"></x-link></x-table.cell>
                            <x-table.cell class="col-2">{{ $this->getAccessStateLabel($distributionGroup->pos_student_screen_access) }}</x-table.cell>
                            <x-table.cell class="col-1">{{ $distributionGroup->users_count }}</x-table.cell>
                            <x-table.cell class="col">
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <x-button.primary wire:click="edit({{ $distributionGroup->id }})" ><x-loading wire:target="edit({{ $distributionGroup->id }})" />Edit</x-button.primary>
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    No user groups found
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <x-table.pagination-summary :model="$distributionGroups" />
        </div>
    </div>

    <!-- Delete Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.dialog type="confirmModal">
            <x-slot name="title">Delete Loans</x-slot>

            <x-slot name="content">
                Are you sure you want to delete these user groups? This action is irreversible.
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','confirm')">Cancel</x-button.secondary>
                <x-button.danger type="submit">Delete</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <!-- Create/Edit Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog type="editModal" class="modal-xl">
            <x-slot name="title">{{ $modalType }} User Group</x-slot>

            <x-slot name="content">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Name -->
                        <x-input.group label="Name" for="name" :error="$errors->first('editing.name')">
                            <x-input.text wire:model.defer="editing.name" id="name" rows="8" />
                        </x-input.group>

                        <x-input.group for="pos_student_screen_access" label="Student Checkout Access" :error="$errors->first('editing.pos_student_screen_access')">
                            <select wire:model.defer="editing.pos_student_screen_access" id="pos_student_screen_access" class="form-control">
                                <option value="1">Enabled</option>
                                <option value="-1">Disabled (overwrites enabled groups)</option>
                                <option value="0">Not Configured</option>
                            </select>
                        </x-input.group>

                        <!-- Users -->
                        <x-input.group label="Users" for="user_id" :error="$errors->first('user_id')">
                            <x-input.select wire:model="user_id" id="user_id" clearSelection disabledSelected iteration="{{ $iteration }}" placeholder="Select User" fullWidth inModal>
                                @foreach ($equipmentList as $user)
                                    @if($user['avaliable'] == true)
                                        <option value="{{ $user['id'] }}">{{ $user['forename'] }} {{ $user['surname'] }}</option>
                                    @else
                                        <option value="{{ $user['id'] }}" disabled>{{ $user['forename'] }} ({{ $user['surname'] }})</option>
                                    @endif
                                @endforeach
                            </x-input.select>
                        </x-input.group>
                    </div>

                    <div class="col-md-6">
                        <!-- Shopping Cart -->
                        <div wire:model="shoppingCart" iteration="{{ $iteration }}">
                            <x-shoppingCart.group>
                                @foreach ($shoppingCart as $key => $user)
                                    <x-shoppingCart.cartCard id="{{ $user['id'] }}" name="{{ $user['forename'] }} {{ $user['surname'] }}" />
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