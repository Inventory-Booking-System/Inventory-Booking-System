<?php

namespace App\Http\Livewire\Incident;

use Livewire\Component;

class Show extends Component
{
    public $shoppingCart = [];
    public $shoppingCost = 0;

    public $incidentId;
    public $start_date_time;
    public $location_id;
    public $distribution_id;
    public $equipment_id;
    public $evidence;
    public $details;

    public function mount($incident)
    {
        //Populate Incident
        $this->start_date_time = $incident->start_date_time;
        $this->location_id = $incident->location_id;
        $this->distribution_id = $incident->distribution_id;
        $this->evidence = $incident->evidence;
        $this->details = $incident->details;
        $this->incidentId = $incident->id;

        //Populate Incident Issues

        foreach($incident->issues as $issue){
            $this->shoppingCart[$issue->id] = [];
            $this->shoppingCart[$issue->id]['quantity'] = $issue->pivot->quantity;
            $this->shoppingCart[$issue->id]['title'] = $issue->title;
            $this->shoppingCart[$issue->id]['cost'] = $issue->cost;

            $this->shoppingCost += $issue->cost;
        }
    }

    public function render()
    {
        return view('livewire.incident.show');
    }
}
