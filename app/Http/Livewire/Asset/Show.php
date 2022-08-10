<?php

namespace App\Http\Livewire\Asset;

use Livewire\Component;

class Show extends Component
{

    public $assetID = '';
    public $name = '';
    public $tag = '';
    public $description = '';

    public function mount($asset)
    {
        $this->assetID = $asset->id;
        $this->name = $asset->name;
        $this->tag = $asset->tag;
        $this->description = $asset->description;
    }

    public function render()
    {
        return view('livewire.asset.show');
    }
}
