<div>
    <div class="row mt-3">
        <div class="col-12">
            <div class="card w-100">
                <div class="card-header bg-dark text-center">
                    <h1>Archived Users</h1>
                </div>
                <div class="card-body p-0">
                    @if($archivedUsers->isEmpty())
                        <p class="text-center p-4 mb-0">No archived users found.</p>
                    @else
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Description</th>
                                    <th>Archived At</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($archivedUsers as $user)
                                    <tr wire:key="archived-{{ $user->id }}">
                                        <td>{{ $user->forename }} {{ $user->surname }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->description ?: '-' }}</td>
                                        <td>{{ $user->deleted_at->format('d M Y H:i') }}</td>
                                        <td class="text-right">
                                            <x-button.primary wire:click="restore({{ $user->id }})">
                                                <x-loading wire:target="restore({{ $user->id }})" />Restore
                                            </x-button.primary>
                                            <x-button.danger wire:click="confirmForceDelete({{ $user->id }})">
                                                Delete Permanently
                                            </x-button.danger>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Permanent Delete Confirmation Modal -->
    <x-modal.dialog type="confirmModal">
        <x-slot name="title">Permanently Delete User</x-slot>

        <x-slot name="content">
            @if($confirmingForceDeleteId)
                @php $userToDelete = $archivedUsers->firstWhere('id', $confirmingForceDeleteId); @endphp
                Are you sure you want to permanently delete
                <strong>{{ $userToDelete ? $userToDelete->forename . ' ' . $userToDelete->surname : 'this user' }}</strong>?
                This action cannot be undone.
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-button.secondary wire:click="$emit('hideModal', 'confirm')">Cancel</x-button.secondary>
            <x-button.danger wire:click="forceDelete">
                <x-loading wire:target="forceDelete" />Delete Permanently
            </x-button.danger>
        </x-slot>
    </x-modal.dialog>
</div>

