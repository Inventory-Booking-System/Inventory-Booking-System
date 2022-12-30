<?php

namespace App\Http\Livewire\Incident;

use Livewire\Component;
use App\Models\Incident;

class Show extends Component
{
    public $incident;

    public function mount($incident)
    {
        $this->incident = Incident::find($incident);
    }

    public function render()
    {
        return view('livewire.incident.show', [
            'incident' => $this->incident,
        ]);
    }
}
