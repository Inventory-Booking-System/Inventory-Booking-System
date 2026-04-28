<div>
    <ul class="nav nav-tabs mb-3" id="settingsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="mail-settings-tab" data-toggle="tab" href="#mail-settings" role="tab" aria-controls="mail-settings" aria-selected="true">Mail Settings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="archived-users-tab" data-toggle="tab" href="#archived-users" role="tab" aria-controls="archived-users" aria-selected="false">Archived Users</a>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabContent">
        <div class="tab-pane fade show active" id="mail-settings" role="tabpanel" aria-labelledby="mail-settings-tab">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-4 offset-4">
                        <div class="card w-100">
                            <div class="card-header bg-dark text-center">
                                <h1>Mail Settings</h1>
                            </div>
                            <div class="card-body">
                                <x-input.group class="" for="mailer" label="Mailer" :error="$errors->first('mail.mailer')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.mailer" id="mailer" />
                                </x-input.group>

                                <x-input.group class="" for="host" label="Host" :error="$errors->first('mail.host')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.host" id="host" />
                                </x-input.group>

                                <x-input.group class="" for="port" label="Port" :error="$errors->first('mail.port')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.port" id="port" />
                                </x-input.group>

                                <x-input.group class="" for="username" label="Username" :error="$errors->first('mail.username')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.username" id="username" />
                                </x-input.group>

                                <x-input.group class="" for="password" label="Password" :error="$errors->first('mail.password')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.password" id="password" />
                                </x-input.group>

                                <x-input.group class="" for="encryption" label="Encryption" :error="$errors->first('mail.encryption')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.encryption" id="encryption" />
                                </x-input.group>

                                <x-input.group class="" for="from_address" label="From Address" :error="$errors->first('mail.from_address')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.from_address" id="from_address" />
                                </x-input.group>

                                <x-input.group class="" for="cc_address" label="CC Address" :error="$errors->first('mail.cc_address')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.cc_address" id="cc_address" />
                                </x-input.group>

                                <x-input.group class="" for="reply_to_address" label="Reply To Address" :error="$errors->first('mail.reply_to_address')">
                                    <x-input.text class="form-control-full-width" wire:model="mail.reply_to_address" id="reply_to_address" />
                                </x-input.group>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 text-center">
                        <x-button.primary class="w-25 btn-lg" type="submit">Save</x-button.primary>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-pane fade" id="archived-users" role="tabpanel" aria-labelledby="archived-users-tab">
            @livewire('app-settings.archived-users')
        </div>
    </div>
</div>
