<?php

namespace App\Http\Livewire\Signage;

use Livewire\Component;
use App\Models\Loan;
use Carbon\Carbon;

class Signage extends Component
{
    public function render()
    {
        return view('livewire.signage.signage');
    }
}
