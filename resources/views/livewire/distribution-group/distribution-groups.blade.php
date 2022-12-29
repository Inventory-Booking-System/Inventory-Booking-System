<div>
    <x-table.controls name="Distribution Group" />

    <div class="row">
        <div class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('name')" :direction="$sorts['name'] ?? null" class="col-2">Name</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('users')" :direction="$sorts['users'] ?? null" class="col-2">Users</x-table.heading>
                        <x-table.heading class="col"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.name" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-2" direction="null"><x-input.text wire:model="filters.users" class="form-control-sm p-0" /></x-table.heading>
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
                                            <span>You selected <strong> {{ $distributionGroups->count() }} </strong> distribution groups, do you want to select all <strong> {{ $distributionGroups->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $distributionGroups->total() }} </strong> distribution groups.</span>
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
                            <x-table.cell class="col-2"><x-link route="distributionGroups" id="{{ $distributionGroup->id }}" value="{{ $distributionGroup->name }}"></x-link></x-table.cell>
                            <x-table.cell class="col-2">
                                @foreach($distributionGroup->users as $user)
                                    <x-link route="users" id="{{ $user->id }}" value="{{ $user->forename }} {{ $user->surname }}"></x-link><br>
                                @endforeach
                            </x-table.cell>
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
                                    No distribution groups found
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
                Are you sure you want to delete these distribution groups? This action is irreversible.
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
            <x-slot name="title">{{ $modalType }} Distribution Group</x-slot>

            <x-slot name="content">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Name -->
                        <x-input.group label="Name" for="name" :error="$errors->first('editing.name')">
                            <x-input.text wire:model="editing.name" id="name" rows="8" />
                        </x-input.group>

                        <!-- Users -->
                        <x-input.group label="Users" for="user_id" :error="$errors->first('user_id')">
                            <x-input.select wire:model="user_id" id="user_id" clearSelection disabledSelected iteration="{{ $iteration }}" placeholder="Select User">
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