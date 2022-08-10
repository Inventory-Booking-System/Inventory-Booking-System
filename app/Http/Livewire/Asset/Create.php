<?php

namespace App\Http\Livewire\Asset;

use Livewire\Component;
use App\Models\Asset;

class Create extends Component
{
    public $name;
    public $tag;
    public $description;

    protected $rules = [
        'name' => 'required|string',
        'tag' => 'required|numeric|unique:assets',
        'description' => 'string',
    ];

    public function updated()
    {
        $this->validate();
    }

    public function save()
    {
        //Check that the data we have recieved is valid
        $this->validate();

        //Create Asset in the database
        $asset = Asset::create([
            'name' => $this->name,
            'tag' => $this->tag,
            'description' => $this->description,
        ]);

        //Alert the user the result
        if($asset->exists()){
            $this->emit('alert', ['type' => 'success', 'message' => "$asset->name has been created"]);
        }else{
            $this->emit('alert', ['type' => 'failed', 'message' => "$asset->name was not created"]);
        }

        //return redirect()->route('assets.index');
    }

    public function render()
    {
        return view('livewire.asset.create');
    }
}
