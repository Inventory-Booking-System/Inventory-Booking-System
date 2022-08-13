<?php

namespace App\Http\Livewire\Incident;

use Livewire\Component;
use App\Models\Incident;
use App\Models\Location;
use App\Models\DistributionGroup;
use App\Models\EquipmentIssue;
use App\Models\EquipmentIssueIncident;
use Carbon\Carbon;

class Create extends Component
{
    public $locations;
    public $distributions;
    public $equipmentIssues;

    public $shoppingCart = [];
    public $shoppingCost = 0;

    public $start_date_time;
    public $location_id;
    public $distribution_id;
    public $equipment_id;
    public $evidence;
    public $details;

    protected $rules = [
        'start_date_time' => 'required|date',
        'location_id' => 'required|numeric|exists:locations,id',
        'distribution_id' => 'required|numeric|exists:distribution_groups,id',
        'equipment_id' => 'required|numeric|exists:equipment_issues,id',
        'evidence' => 'required|string',
        'details' => 'required|string',
    ];

    public function mount()
    {
        $this->locations = Location::latest()->get();
        $this->distributions = DistributionGroup::latest()->get();
        $this->equipmentIssues = EquipmentIssue::latest()->get();
    }

    public function updatedEquipmentId($id)
    {
        $item = EquipmentIssue::find($id);

        if(isset($this->shoppingCart[$item->id])){
            $this->shoppingCart[$item->id]['quantity'] += 1;
        }else{
            $this->shoppingCart[$item->id] = [];
            $this->shoppingCart[$item->id]['quantity'] = 1;
            $this->shoppingCart[$item->id]['title'] = $item->title;
            $this->shoppingCart[$item->id]['cost'] = $item->cost;
        }

        $this->updateShoppingCartCost();
    }

    public function removeItem($id)
    {
        if($this->shoppingCart[$id]['quantity'] == 1){
            unset($this->shoppingCart[$id]);
        }elseif($this->shoppingCart[$id]['quantity'] > 1){
            $this->shoppingCart[$id]['quantity'] -= 1;
        }

        $this->updateShoppingCartCost();
    }

    private function updateShoppingCartCost()
    {
        $this->shoppingCost = 0;
        foreach($this->shoppingCart as $key => $item){
            $this->shoppingCost += $item['cost'];
        }
    }

    public function save()
    {
        //Check that the data we have recieved is valid
        $this->validate();

        //Create Incident in the database
        $incidentId = Incident::create([
            'start_date_time' => carbon::parse($this->start_date_time),
            'location_id' => $this->location_id,
            'distribution_id' => $this->distribution_id,
            'evidence' => $this->evidence,
            'details' => $this->details,
        ])->id;

        $incident = Incident::find($incidentId);

        //Add equipment issues into equipment_issue_incidents
        $ids = [];
        foreach($this->shoppingCart as $key => $item){
            array_push($ids, ['incident_id' => $incidentId, 'equipment_issue_id' => $key, 'quantity' => $item['quantity']]);
        }

        $incident->issues()->sync($ids);

        //Alert the user the result
        if($incident->exists()){
            $this->emit('alert', ['type' => 'success', 'message' => "Incident #$incident->id has been created"]);
        }else{
            $this->emit('alert', ['type' => 'failed', 'message' => "Incident was not created"]);
        }
    }

    public function render()
    {
        return view('livewire.incident.create');
    }
}
