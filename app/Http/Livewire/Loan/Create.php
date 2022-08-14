<?php

namespace App\Http\Livewire\Loan;

use Livewire\Component;
use App\Models\User;
use App\Models\Asset;
use Carbon\Carbon;
use Response;

class Create extends Component
{
    public $start_date;
    public $end_date;
    public $status_id = 1;
    public $details;
    public $user_id;
    public $shoppingCart = [];
    public $shoppingCost = 0;
    public $users;
    public $equipment_id;

    public $avaliableEquipment = [];

    public $iteration = 0;

    public function updatingEquipmentId()
    {

    }


    protected $rules = [
        'user_id' => 'required|integer',
        'start_date' => 'required|date|before:end_date|nullable',
        'end_date' => 'required|date|after:start_date|nullable',
        // 'equipmentSelected' => 'required|json',
        'details' => 'nullable|string',
        'status_id' => 'required|string|in:0,1',
    ];

    public function updatedStartDate()
    {

    }

    public function updatedEndDate()
    {
        $this->getBookableEquipment();
        $this->iteration ++;
    }

    public function updatedEquipmentId($id)
    {
        $item = Asset::find($id);

        $this->shoppingCart[$item->id] = [];
        $this->shoppingCart[$item->id]['title'] = $item->name;
    }

    public function removeItem($id)
    {
        if($this->shoppingCart[$id]['quantity'] == 1){
            unset($this->shoppingCart[$id]);
        }elseif($this->shoppingCart[$id]['quantity'] > 1){
            $this->shoppingCart[$id]['quantity'] -= 1;
        }
    }

    public function mount()
    {
        $this->users = User::latest()->get();


    }

    public function save()
    {
        $this->validate();
    }

    /**
     * Fetches a list of equipment avaliable for the current selected input values.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookableEquipment()
    {
        $validatedDate =[
            'start_date_time' => carbon::parse($this->start_date),
            'end_date_time' => carbon::parse($this->end_date),
            'id' =>  $this->loanId ?? -1
        ];


        $this->avaliableEquipment = Asset::with('loans')
            ->where(function($query) use($validatedDate){
                $query->whereNotIn('assets.id', function($query) use($validatedDate){
                    $query->select('asset_loan.asset_id')
                            ->from('loans')
                            ->join('asset_loan','loans.id','asset_loan.loan_id')
                            ->whereRaw('`assets`.`id` = `asset_loan`.`asset_id`')
                            ->where('loans.id', '!=', $validatedDate['id'])
                            ->where(function($query2) use($validatedDate){
                                $query2->where('loans.start_date_time', '>=', $validatedDate['start_date_time'])
                                        ->where('loans.start_date_time', '<=', $validatedDate['end_date_time'])
                                        ->where('loans.id', '!=', $validatedDate['id']);
                            })->orWhere(function($query2) use($validatedDate){
                                $query2->where('loans.end_date_time', '>=', $validatedDate['start_date_time'])
                                    ->where('loans.end_date_time', '<=', $validatedDate['end_date_time'])
                                    ->where('loans.id', '!=', $validatedDate['id']);
                            })
                            ->where('asset_loan.returned','=',0);
                })
                ->orWhereDoesntHave('loans');
            })
            ->get();

        $this->emit('refresh');
    }

    public function render()
    {
        return view('livewire.loan.create');
    }
}
