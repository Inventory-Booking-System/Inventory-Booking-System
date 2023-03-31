@props([
	'name' => null,
    'perPage' => 10,
    'legacyModal' => true
])

<div class="row">
    <div class="col-lg-3 mb-3">
        <x-input.text wire:model="filters.search" placeholder="Search {{ $name }}..." />
    </div>

    <div class="col-lg-1">
        <x-button.primary wire:loading.style.delay='"' class="" wire:click="$toggle('showFilters')">Toggle Filters</x-button.primary>
    </div>

    <div class="col-lg-1" >
        <x-input.select wire:model="perPage" id="perPage" label="Per Page" noSearch>
            <option value="10" @if($perPage == 10) selected @endif>10</option>
            <option value="25" @if($perPage == 25) selected @endif>25</option>
            <option value="50" @if($perPage == 50) selected @endif>50</option>
        </x-input.select>
    </div>

    <div class="col">
        <x-dropdown class="float-right" label="Actions">
            <x-dropdown.item wire:click="exportSelected">Export</x-dropdown.item>
            <x-dropdown.item wire:click="$emit('showModal','confirm')">Delete</x-dropdown.item>
        </x-dropdown>
        <x-button.primary class="float-right mx-2 px-5" id="create" wire:click="{{ $legacyModal ? 'create' : '' }}">New {{ $name }}</x-button.primary>
    </div>
</div>