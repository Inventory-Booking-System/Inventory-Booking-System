<form wire:submit.prevent="save">
    <div class="row" >
        <div class="col-4 offset-2">
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

        <div class="col-4 ">
            <div class="card w-100">
                <div class="card-header bg-dark text-center">
                    <h1>Notification Settings</h1>
                </div>
                <div class="card-body">
                    <!-- Overdue Emails -->
                    <x-input.group label="Send Overdue Emails" for="overdue_emails" :error="$errors->first('notification.overdue_emails')" buttonGroup>
                        <x-input.radioButton wire:model="notification.overdue_emails" id="overdue_emails_yes" value="1" text="Yes" checked="{{ $notification['overdue_emails'] }}" />
                        <x-input.radioButton wire:model="notification.overdue_emails" id="overdue_emails_no" value="0" text="No" checked="{{ $notification['overdue_emails'] }}" />
                    </x-input.group>

                    <!-- Setup Emails -->
                    <x-input.group label="Send Setup Emails" for="setup_emails" :error="$errors->first('notification.setup_emails')" buttonGroup>
                        <x-input.radioButton wire:model="notification.setup_emails" id="setup_emails_yes" value="1" text="Yes" checked="{{ $notification['setup_emails']}}" />
                        <x-input.radioButton wire:model="notification.setup_emails" id="setup_emails_no" value="0" text="No" checked="{{ $notification['setup_emails'] }}" />
                    </x-input.group>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 text-center">
            <x-button.primary class="w-25 btn-lg" type="submit">Save</x-button.primary>
        </div>
    </div>
</form>
