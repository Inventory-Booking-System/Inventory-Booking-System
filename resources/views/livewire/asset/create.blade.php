<div class="row justify-content-center">
    <div class="col-lg-5 p-3">
        <div class="card">
            <div class="card-header text-center">
                New Asset
            </div>

            <div class="card-body">
                <form wire:submit.prevent="save" >
                    <!-- Asset Name -->
                    <x-input.group label="Name" for="name" :error="$errors->first('name')">
                        <x-input.text wire:model="name" id="name" />
                    </x-input.group>

                    <!-- Asset Description -->
                    <x-input.group label="Description" for="description" :error="$errors->first('description')">
                        <x-input.textarea wire:model="description" id="description" rows="4" />
                    </x-input.group>

                    <!-- Asset Tag -->
                    <x-input.group label="Tag" for="tag" :error="$errors->first('tag')">
                        <x-input.text wire:model="tag" id="tag" />
                    </x-input.group>

                    <!-- Submit Button -->
                    <x-button value="Create Asset"></x-button>
                </form>
            </div>
        </div>
    </div>
</div>