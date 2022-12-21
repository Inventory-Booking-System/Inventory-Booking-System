<?php

namespace App\Http\Livewire\Loan;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\ShoppingCart\WithShoppingCart;
use App\Models\Loan;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Mail\LoanCreated;
use Carbon\Carbon;

class Loans extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithShoppingCart;

    protected $paginationTheme = 'bootstrap';       #Use boostrap theme when displaying data with pagination
    protected $queryString = [];                    #Use on tables when displaying data based on the user request
    public $showFilters = false;                    #These are displayed above each column in the table
    public $filters = [                             #Our default filters to apply to the tables
        'search' => '',
        'id' => null,
        'user_id' => null,
        'status_id' => null,
        'start_date_time' => null,
        'end_date_time' => null,
        'details' => null,
        'assets' => null,
    ];

    public Loan $editing;                           #Data relating to the current loan excluding any assets
                   #List of assets that are avaliable for the current loan


    public $equipment_id;                          #???
    public $counter;                               #???
    //public $status_id;

    public $iteration = 0;                          #Increment anytime we want the Select2 Dropdown to update (as we are using wire:ignore to stop it updating each render)
    public $iteration2 = 0;                          #Increment anytime we want the Select2 Dropdown to update (as we are using wire:ignore to stop it updating each render)


    public function rules()
    {
        return [
            'editing.user_id' => 'required|integer',
            'editing.status_id' => 'required|integer|in:0,1',
            'editing.start_date_time' => 'required|date|before:editing.end_date_time|nullable',
            'editing.end_date_time' => 'required|date|after:editing.start_date_time|nullable',
            'editing.details' => 'nullable|string',
            'editing.assets' => 'nullable',
            'equipment_id' => 'nullable|numeric|exists:assets,id',
        ];
    }

    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankLoan();
        $this->users = User::latest()->get();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankLoan()
    {
        $this->editing = Loan::make();
        $shoppingCart = [];
        $equipment_id = null;
    }

    public function deleteSelected()
    {
        $this->selectedRowsQuery->delete();

        $this->emit('hideModal', 'confirm');
    }

    public function exportSelected()
    {
        return response()->streamDownload(function() {
            echo $this->selectedRowsQuery->toCsv();
        }, 'loans.csv');
    }

    public function create()
    {
        if ($this->editing->getKey()){
            $this->makeBlankLoan();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(Loan $loan)
    {
        #If the loan is the same as the previous loan that we have stored, just show the modal
        #in the current state that is was when it was last closed rather than wiping the data.
        $this->editing = $loan;

        if($this->editing->isNot($loan)){
        }

        //dd($this->shoppingCart);

        //Load assets from loans model into the shopping cart
        $this->emptyCart();
        $this->editing->assets->each(function ($item, $key) {
            $this->addItemToCart($item);
        });

        //Populate equipment dropdown
        $this->getBookableEquipment();
        $this->iteration ++;

        //Display the modal to the user
        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        //Make sure all the data the user entered into the modal is valid
        $this->validate();

        //Update database
        $this->editing->push();

        //Update assets in database
        $loan = Loan::find($this->editing->id);
        $ids = [];
        foreach($this->shoppingCart as $key => $item){
            array_push($ids, ['loan_id' => $this->editing->id, 'asset_id' => $item['id'], 'returned' => $item['pivot']['returned']]);
        }
        $loan->assets()->sync($ids);

        //dd($ids);

        //Hide the modal from the user
        $this->emit('hideModal', 'edit');

        // $this->makeBlankLoan();

        // //Send the email to the user
        // $user = User::find($loan->user_id);

        // Mail::to($user->email)->queue(new LoanCreated($loan, false));
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = Loan::query()
            ->with('user')
            ->with('assets')
            ->when($this->filters['user_id'], fn($query, $user_id) => $query->where('user_id', $user_id))
            ->when($this->filters['status_id'], fn($query, $status_id) => $query->where('status_id', $status_id))
            ->when($this->filters['start_date_time'], fn($query, $start_date_time) => $query->where('start_date_time', $start_date_time))
            ->when($this->filters['end_date_time'], fn($query, $end_date_time) => $query->where('end_date_time', $end_date_time))
            ->when($this->filters['details'], fn($query, $details) => $query->where('details', $details))
            ->when($this->filters['search'], fn($query, $search) => $query->where('details', 'like', '%'.$search.'%'));

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->applyPagination($this->rowsQuery);
    }

    public function render()
    {
        if($this->selectAll){
           $this->selectPageRows();
        }

        return view('livewire.loan.loans', [
            'loans' => $this->rows,
        ]);
    }

    public function updatedEquipmentId($id)
    {
        $item = Asset::find($id);
        $this->addItemToCart($item);

        foreach($this->equipmentList as $key => $equipment){
            if($equipment['id'] == $id){
                $this->equipmentList[$key]['avaliable'] = false;
            }
        }

        $this->iteration ++;
    }

    public function removeItem($id)
    {
        $this->removeItemFromCart($id);

        foreach($this->equipmentList as $key => $equipment){
            if($equipment['id'] == $id){
                $this->equipmentList[$key]['avaliable'] = true;
            }
        }

        $this->iteration ++;
    }

    public function bookSingleItem($id)
    {
        foreach($this->shoppingCart as $key => $equipment){
            if($equipment['id'] == $id){
                if($this->shoppingCart[$key]['pivot']['returned'] == 1){
                    $this->shoppingCart[$key]['pivot']['returned'] = 0;
                }else{
                    $this->shoppingCart[$key]['pivot']['returned'] = 1;
                }
            }
        }

        $this->iteration ++;
    }

    public function book($id){
        $loan = Loan::find($id);
        $loan->status_id = 0;
        $loan->push();

        //TODO: Send email stuff
    }

    public function cancel($id){
        $loan = Loan::find($id);
        $loan->status_id = 4;
        $loan->push();

        //TODO: Send email stuff
    }

    public function updatedEditingEndDateTime()
    {
        $this->getBookableEquipment();
        $this->iteration ++;
    }
}
