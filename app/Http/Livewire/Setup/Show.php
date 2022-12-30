<?php

namespace App\Http\Livewire\Setup;

use Livewire\Component;
use App\Models\Setup;
use App\Models\Loan;
use App\Models\Asset;
use App\Models\User;

class Show extends Component
{
    public $setup;

    public function mount($setup)
    {
        $this->setup = Setup::find($setup);
    }

    public function render()
    {
        return view('livewire.setup.show', [
            'setup' => $this->setup,
        ]);
    }
}
