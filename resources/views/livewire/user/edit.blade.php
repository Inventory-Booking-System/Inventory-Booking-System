<div class="row justify-content-center">
    <div class="col-lg-5 p-3">
        <div class="card">
            <div class="card-header text-center">
                Modify User
            </div>

            <div class="card-body">
                <form wire:submit.prevent="save" >
                    <!-- Forename -->
                    <x-input.group label="Forename" for="forename" :error="$errors->first('forename')">
                        <x-input.text wire:model="forename" id="forename" />
                    </x-input.group>

                    <!-- Surname -->
                    <x-input.group label="Surname" for="surname" :error="$errors->first('surname')">
                        <x-input.textarea wire:model="surname" id="surname" rows="4" />
                    </x-input.group>

                    <!-- Email -->
                    <x-input.group label="Email" for="email" :error="$errors->first('email')">
                        <x-input.text wire:model="email" id="email" />
                    </x-input.group>

                    <!-- Submit Button -->
                    <x-button value="Modify User"></x-button>
                </form>
            </div>
        </div>
    </div>
</div>