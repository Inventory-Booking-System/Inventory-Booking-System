<div>
    <x-button.secondary wire:click="$toggle('showModal')"><span>Import</span></x-button.secondary>

    <form wire:submit.prevent="import">
        <x-modal.dialog>
            <x-slot name="title">Import Transactions</x-slot>

            <x-slot name="content">
                @unless ($upload)
                <div>
                    <div>
                        <x-input.file-upload wire:model="upload" id="upload"><span>CSV File</span></x-input.file-upload>
                    </div>
                    @error('upload') <div>{{ $message }}</div> @enderror
                </div>
                @else
                <div>
                    <x-input.group for="name" label="Name" :error="$errors->first('fieldColumnMap.name')">
                        <x-input.select wire:model="fieldColumnMap.name" id="name">
                            <option value="" disabled>Select Column...</option>
                            @foreach ($columns as $column)
                                <option>{{ $column }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <x-input.group for="tag" label="Tag" :error="$errors->first('fieldColumnMap.tag')">
                        <x-input.select wire:model="fieldColumnMap.tag" id="tag">
                            <option value="" disabled>Select Column...</option>
                            @foreach ($columns as $column)
                                <option>{{ $column }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>

                    <x-input.group for="description" label="Description">
                        <x-input.select wire:model="fieldColumnMap.description" id="description">
                            <option value="" disabled>Select Column...</option>
                            @foreach ($columns as $column)
                                <option>{{ $column }}</option>
                            @endforeach
                        </x-input.select>
                    </x-input.group>
                </div>
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-button.secondary wire:click="$set('hideModal', false)">Cancel</x-button.secondary>

                <x-button.primary type="submit">Import</x-button.primary>
            </x-slot>
        </x-modal.dialog>
    </form>
</div>