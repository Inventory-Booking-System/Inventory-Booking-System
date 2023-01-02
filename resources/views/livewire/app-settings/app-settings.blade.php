<div class="row" >
    <div class="col-12">
        <div class="row">
            <div class="card w-100">
                <div class="card-header bg-dark text-center">
                    <h1>Application Settings</h1>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="col-lg-4">
                            <p class="text-title text-center">Mail Settings</p>
                            <x-input.group class="" for="mailer" label="Mailer" :error="$errors->first('settings.mailer')">
                                <x-input.text class="form-control-full-width" wire:model="settings.mailer" id="mailer" />
                            </x-input.group>

                            <x-input.group class="" for="host" label="Host" :error="$errors->first('settings.host')">
                                <x-input.text class="form-control-full-width" wire:model="settings.host" id="host" />
                            </x-input.group>

                            <x-input.group class="" for="port" label="Port" :error="$errors->first('settings.port')">
                                <x-input.text class="form-control-full-width" wire:model="settings.port" id="port" />
                            </x-input.group>

                            <x-input.group class="" for="username" label="Username" :error="$errors->first('settings.username')">
                                <x-input.text class="form-control-full-width" wire:model="settings.username" id="username" />
                            </x-input.group>

                            <x-input.group class="" for="password" label="Password" :error="$errors->first('settings.password')">
                                <x-input.text class="form-control-full-width" wire:model="settings.password" id="password" />
                            </x-input.group>

                            <x-input.group class="" for="encryption" label="Encryption" :error="$errors->first('settings.encryption')">
                                <x-input.text class="form-control-full-width" wire:model="settings.encryption" id="encryption" />
                            </x-input.group>

                            <x-input.group class="" for="from_address" label="From Address" :error="$errors->first('settings.from_address')">
                                <x-input.text class="form-control-full-width" wire:model="settings.from_address" id="from_address" />
                            </x-input.group>

                        </div>
                        <x-button.primary class="mt-3" type="submit">Save</x-button.primary>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>