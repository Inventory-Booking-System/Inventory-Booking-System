<div>
    <div class="row">
        <div class="col-lg-3 mb-3">
            <x-input.text wire:model="filters.search" placeholder="Search Users..." />
        </div>

        <div class="col-lg-1">
            <x-button.primary wire:loading.style.delay='"' class="" wire:click="$toggle('showFilters')">Toggle Filters</x-button.primary>
        </div>

        <div class="col-lg-1" >
            <x-input.select wire:model="perPage" id="perPage" label="Per Page">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </x-input.select>
        </div>

        <div class="col">
            <x-dropdown class="float-right" label="Actions">
                <x-dropdown.item wire:click="exportSelected">Export</x-dropdown.item>
                <x-dropdown.item wire:click="$emit('showModal','confirm')">Delete</x-dropdown.item>
            </x-dropdown>
            <x-button.primary class="float-right mx-2 px-5" wire:click="create">New User</x-button.primary>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <x-table>
                <x-slot name="head">
                    <x-table.row>
                        <x-table.heading direction="null">
                            <x-input.checkbox wire:model="selectPage" />
                        </x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('forename')" :direction="$sorts['forename'] ?? null" class="col-3">Forename</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('surname')" :direction="$sorts['surname'] ?? null" class="col-1">Surname</x-table.heading>
                        <x-table.heading sortable wire:click="sortBy('email')" :direction="$sorts['email'] ?? null" class="col">Email</x-table.heading>
                        <x-table.heading class="col-2"/>
                    </x-table.row>

                    @if($showFilters)
                        <x-table.row>
                            <x-table.heading direction="null">
                                <x-input.checkbox />
                            </x-table.heading>
                            <x-table.heading class="col-3" direction="null"><x-input.text wire:model="filters.forename" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col-1" direction="null"><x-input.text wire:model="filters.surname" class="form-control-sm p-0" /></x-table.heading>
                            <x-table.heading class="col" direction="null"><x-input.text wire:model="filters.email" class="form-control-sm p-0" /></x-table.heading>
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
                                            <span>You selected <strong> {{ $users->count() }} </strong> users, do you want to select all <strong> {{ $users->total() }} </strong>?</span>
                                            <x-button.link wire:click="selectAll">Select All</x-button.link>
                                        </div>
                                    @else
                                        <span>You have selected all <strong> {{ $users->total() }} </strong> users.</span>
                                    @endif
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endif

                    @forelse ($users as $user)
                        <x-table.row wire:key="row-{{ $user->id }}">
                            <x-table.cell >
                                <x-input.checkbox wire:model="selected" value="{{ $user->id }}"></x-input.checkbox>
                            </x-table.cell>
                            <x-table.cell class="col-3">{{ $user->forename }}</x-table.cell>
                            <x-table.cell class="col-1">{{ $user->surname }}</x-table.cell>
                            <x-table.cell class="col">{{ $user->email }}</x-table.cell>
                            <x-table.cell class="col-2">
                                <x-button.primary wire:click="edit({{ $user->id }})" ><x-loading wire:target="edit({{ $user->id }})" />Edit</x-button.primary>
                                @if($user->has_account)
                                    <x-button.danger wire:click="resetPassword({{ $user->id }})" ><x-loading wire:target="resetPassword({{ $user->id }})" />Reset Password</x-button.danger>
                                @endif
                            </x-table.cell>
                        </x-table.row>
                    @empty
                        <x-table.row>
                            <x-table.cell width="12">
                                <div class="d-flex justify-content-center">
                                    No users found
                                </div>
                            </x-table.cell>
                        </x-table.row>
                    @endforelse
                </x-slot>
            </x-table>

            <div class="row mt-2">
                <div class="col-lg-3 d-flex flex-row">
                    <span>Showing {{ ($users->currentPage() * $users->count()) - ($users->count() - 1) }} to {{ $users->currentPage() * $users->count() }} of {{ $users->total() }} results</span>
                </div>
                <div class="col-lg-9 d-flex flex-row-reverse">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <form wire:submit.prevent="deleteSelected">
        <x-modal.dialog type="confirmModal">
            <x-slot name="title">Delete Users</x-slot>

            <x-slot name="content">
                Are you sure you want to delete these users? This action is irreversible.
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','confirm')">Cancel</x-button.secondary>
                <x-button.danger type="submit">Delete</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>

    <!-- Create/Edit Modal -->
    <form wire:submit.prevent="save">
        <x-modal.dialog type="editModal">
            <x-slot name="title">Edit Asset</x-slot>

            <x-slot name="content">
                <x-input.group for="forename" label="Forename" :error="$errors->first('editing.forename')">
                    <x-input.text wire:model="editing.forename" id="forename" />
                </x-input.group>

                <x-input.group for="surname" label="Surname" :error="$errors->first('editing.surname')">
                    <x-input.text wire:model="editing.surname" id="surname" />
                </x-input.group>

                <x-input.group for="email" label="Email" :error="$errors->first('editing.email')">
                    <x-input.text wire:model="editing.email" id="email" />
                </x-input.group>

                <x-input.group for="has_account" label="Has Account" :error="$errors->first('editing.has_account')">
                    <x-input.checkbox wire:model="editing.has_account" id="has_account" />
                </x-input.group>
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$emit('hideModal','edit')">Cancel</x-button.secondary>
                <x-button.primary type="submit">Save</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>
</div>