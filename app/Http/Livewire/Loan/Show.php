<?php

namespace App\Http\Livewire\Loan;

use Livewire\Component;
use App\Models\Loan;
use App\Models\Asset;
use App\Models\User;

class Show extends Component
{
    public $loan;

    public function mount($loan)
    {
        $this->loan = Loan::find($loan);
    }

    public function render()
    {
        return view('livewire.loan.show', [
            'loan' => $this->loan,
        ]);
    }
}
