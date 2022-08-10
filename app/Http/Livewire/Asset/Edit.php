<?php

namespace App\Http\Livewire\Asset;

use Livewire\Component;
use App\Models\Asset;

class Edit extends Component
{
    public $assetID = '';
    public $name = '';
    public $tag = '';
    public $description = '';

    public function rules()
    {
        return [
            'name' => 'required|string',
            'tag' => "required|numeric|unique:assets,tag," . $this->assetID,
            'description' => 'string',
        ];
    }

    public function mount($asset)
    {
        $this->assetID = $asset->id;
        $this->name = $asset->name;
        $this->tag = $asset->tag;
        $this->description = $asset->description;
    }

    public function updated()
    {
        $this->validate();
    }

    public function save()
    {
        $this->validate();

        $asset = Asset::where('id', $this->assetID)->update([
            'name' => $this->name,
            'description' => $this->description,
            'tag' => $this->tag,
        ]);

        //Alert the user the result
        if($asset){
            $this->emit('alert', ['type' => 'success', 'message' => "$this->name has been modified"]);
        }else{
            $this->emit('alert', ['type' => 'failed', 'message' => "$this->name was not modified"]);
        }
    }

    public function render()
    {
        return view('livewire.asset.edit');
    }
}
