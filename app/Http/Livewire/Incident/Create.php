<?php

namespace App\Http\Livewire\Incident;

use Livewire\Component;
use App\Models\Incident;
use App\Models\Location;
use App\Models\DistributionGroup;
use App\Models\EquipmentIssue;

class Create extends Component
{
    public $locations;
    public $distributions;
    public $equipmentIssues;
    public $start_date_time;
    public $location_id;
    public $distribution_id;
    public $equipment_id;
    public $evidence;
    public $details;

    protected $rules = [
        'start_date_time' => 'required|string',
        'location_id' => 'required|numeric',
        'distribution_id' => 'required|numeric',
        'evidence' => 'required|string',
        'details' => 'required|string',
    ];

    public function mount()
    {
        $this->locations = Location::latest()->get();
        $this->distributions = DistributionGroup::latest()->get();
        $this->equipmentIssues = EquipmentIssue::latest()->get();
    }

    public function updated()
    {
        $this->validate();
    }

    public function save()
    {
        //Check that the data we have recieved is valid
        $this->validate();

        //Create Asset in the database
        // $asset = Asset::create([
        //     'name' => $this->name,
        //     'tag' => $this->tag,
        //     'description' => $this->description,
        // ]);

        // //Alert the user the result
        // if($asset->exists()){
        //     $this->emit('alert', ['type' => 'success', 'message' => "$asset->name has been created"]);
        // }else{
        //     $this->emit('alert', ['type' => 'failed', 'message' => "$asset->name was not created"]);
        // }

        //return redirect()->route('assets.index');
    }

    public function render()
    {
        return view('livewire.incident.create');
    }
}
