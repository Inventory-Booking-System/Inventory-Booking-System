<div class="row" >
    <div class="col-lg-4 mx-auto">
        <div class="row">
            <div class="card w-100">
                <div class="card-header bg-dark text-center">
                    <h1>Profile Settings</h1>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="col-lg-12">
                            <x-input.group class="" for="forename" label="Forename" :error="$errors->first('editing.forename')">
                                <x-input.text class="form-control-full-width" wire:model="editing.forename" id="forename" />
                            </x-input.group>

                            <x-input.group for="surname" label="Surname" :error="$errors->first('editing.surname')">
                                <x-input.text wire:model="editing.surname" id="surname" />
                            </x-input.group>

                            <x-input.group for="email" label="Email" :error="$errors->first('editing.email')">
                                <x-input.text wire:model="editing.email" id="email" />
                            </x-input.group>

                            <x-input.group for="password" label="Password" :error="$errors->first('newPassword')">
                                <x-input.text type="password" wire:model="newPassword" id="password" />
                            </x-input.group>

                            <x-button.primary class="mt-3 w-100" type="submit">Save</x-button.primary>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>