<?php

namespace App\Http\Livewire\Signage;

use Livewire\Component;
use App\Models\Loan;
use Carbon\Carbon;

class Signage extends Component
{
    public $loans;
    public $updateTime;

    public function mount()
    {
        $this->getTodaysLoans();
    }

    public function getTodaysLoans()
    {
        $today = Carbon::today()->toDateString();

        $this->loans = Loan::whereDate('start_date_time', Carbon::today())->orWhere('status_id', '=', '5')->get();

        $this->updateTime = Carbon::now()->format('l F jS Y H:m:s');
    }

    public function render()
    {
        $this->getTodaysLoans();

        return view('livewire.signage.signage');
    }
}
