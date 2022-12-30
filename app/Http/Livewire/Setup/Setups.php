<?php

namespace App\Http\Livewire\Setup;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\ShoppingCart\WithShoppingCart;
use App\Models\Setup;
use App\Models\Loan;
use App\Models\User;
use App\Models\Asset;
use App\Models\Location;
use App\Mail\Setup\SetupOrder;
use Carbon\Carbon;

class Setups extends Component
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
        'title' => null,
        'location_id' => null,
        'details' => null,
        'assets' => null,
        'location' => null,
    ];

    public Setup $editing;                          #Data relating to the current setup excluding any assets
    public $equipment_id;                           #Used to trigger update on the select2 dropdown as we cannot use wire:model due to wire:ignore in place
    public $iteration = 0;                          #Increment anytime we want the Select2 Dropdown to update (as we are using wire:ignore to stop it updating each render)
    public $modalType;                              #Whether the user is creating/edit a loan so we can get correct wording

    #TODO: Is this needed?
    public $counter = 0;                            #???

    public function rules()
    {
        return [
            'editing.loan.user_id' => 'required|integer',
            'editing.loan.start_date_time' => 'required|date|before:editing.loan.end_date_time',
            'editing.loan.end_date_time' => 'required|date|after:editing.loan.start_date_time',
            'editing.loan.details' => 'nullable|string',
            'editing.title' => 'required|string',
            'editing.location_id' => 'required|numeric|exists:locations,id',
            'equipment_id' => 'nullable|numeric|exists:assets,id',
        ];
    }

    #TODO: Is this needed
    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankSetup();
        $this->users = User::latest()->get();
        $this->locations = Location::latest()->get();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankSetup()
    {
        #We must preload an empty loan so @entangle doesnt error
        $this->editing = Setup::make()->setRelation('loan', Loan::make());

        $equipment_id = null;
        $this->emptyCart();
        $this->iteration ++;
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
        }, 'setups.csv');
    }

    public function create()
    {
        //TODO: Can we re-implement this so form input is saved if modal closed on accident
        //I Removed for now as it wasen't resetting properly between editing/creating etc
        if ($this->editing->getKey()){
        }
        $this->makeBlankSetup();

        $this->modalType = "Create";
        $this->emit('showModal', 'create');
    }

    public function edit(Setup $setup)
    {
        #If the loan is the same as the previous loan that we have stored, just show the modal
        #in the current state that is was when it was last closed rather than wiping the data.
        //TODO: Can we re-implement this so form input is saved if modal closed on accident
        //I Removed for now as it wasen't resetting properly between editing/creating etc
        if($this->editing->isNot($setup)){
        }

        $this->emptyCart();
        $this->editing = $setup;

        //Load assets from loans model into the shopping cart
        $this->editing->loan->assets->each(function ($item, $key) {
            $this->addItemToCart($item, false);
        });

        //Display the modal to the user
        $this->modalType = "Edit";
        $this->emit('showModal', 'edit');

        //Populate equipment dropdown
        $this->getBookableEquipment($this->editing->loan->start_date_time, $this->editing->loan->end_date_time);
        $this->iteration ++;
    }

    public function save()
    {
        $this->validate();


        $this->editing->loan->start_date_time = carbon::parse($this->editing->loan->start_date_time);
        $this->editing->loan->end_date_time = carbon::parse($this->editing->loan->end_date_time);
        $this->editing->loan->status_id = 3;
        $this->editing->loan->created_by = Auth::id();

        $this->editing->loan->save();
        $this->editing->loan_id = $this->editing->loan->id;
        $this->editing->save();

        $setup = Setup::find($this->editing->id);

        //Add equipment issues into equipment_issue_incidents
        //TODO: We need to validate ShoppingCart Data. Can this be done through laravel rules?
        $ids = [];
        foreach($this->shoppingCart as $key => $item){
            $ids[$item['id']] = ['returned' => $item['pivot']['returned']];
        }
        $setup->loan->assets()->sync($ids);

        //Hide the modal from the user
        $this->emit('hideModal', 'edit');

        //Send the email to the user
        $user = User::find($setup->loan->user_id);
        Mail::to($user->email)->queue(new SetupOrder($this->editing, $this->editing->wasRecentlyCreated));
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = Setup::query()
            ->with('location')
            ->with('loan.user')
            ->whereHas('loan', function($query){
                $query->where('status_id', '=', '3');
            })
            ->when($this->filters['user_id'], fn($query, $user_id) => $query->where('user_id', $user_id))
            ->when($this->filters['status_id'], fn($query, $status_id) => $query->where('status_id', $status_id))
            ->when($this->filters['start_date_time'], fn($query, $start_date_time) => $query->where('start_date_time', $start_date_time))
            ->when($this->filters['end_date_time'], fn($query, $end_date_time) => $query->where('end_date_time', $end_date_time))
            ->when($this->filters['title'], fn($query, $title) => $query->where('title', $title))
            ->when($this->filters['location_id'], fn($query, $location_id) => $query->where('location_id', $location_id))
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

        return view('livewire.setup.setups', [
            'setups' => $this->rows,
            'users' => $this->users,
            'locations' => $this->locations,
        ]);
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

    public function isAssetPartofSetup($id)
    {
        $setup = Setup::find($this->editing->id);

        if($setup !== null){
            return $assetPresentInLoan = Setup::find($this->editing->id)->whereHas('loan.assets', function($query) use ($id){
                $query->where('asset_id', $id);
            });
        }

        return false;
    }

    public function bookSingleItem($id)
    {
        //Make sure id is part of the booking before making as returned
        //New items added to the cart should be ignored

        if($this->isAssetPartofSetup($id)){
            foreach($this->shoppingCart as $key => $equipment){
                if($equipment['id'] == $id){
                    if($this->shoppingCart[$key]['pivot']['returned'] == 1){
                        $this->shoppingCart[$key]['pivot']['returned'] = 0;
                    }else{
                        $this->shoppingCart[$key]['pivot']['returned'] = 1;
                    }
                }
            }
        }

        $this->iteration ++;
    }

    public function cancel($id){
        $this->updateSetupStatus($id, 4);
    }

    public function complete($id){
        $this->updateSetupStatus($id, 5);
    }

    protected function updateSetupStatus($id, $status)
    {
        $setup = Setup::find($id);
        $setup->loan->status_id = $status;
        $setup->push();

        //Update assets in database
        $ids = [];
        foreach($setup->loan->assets as $key => $asset){
            $ids[$asset['id']] = ['returned' => 1];
        }
        $setup->loan->assets()->sync($ids);

        //Send the email to the user
        $user = User::find($setup->loan->user_id);
        Mail::to($user->email)->queue(new SetupOrder($setup, $this->editing->wasRecentlyCreated));
    }

    public function updatedEquipmentId($id)
    {
        $item = Asset::find($id);

        if($this->isAssetPartofSetup($id)){
            $this->addItemToCart($item, false);
        }else{
            $this->addItemToCart($item, true);
        }

        foreach($this->equipmentList as $key => $equipment){
            if($equipment['id'] == $id){
                $this->equipmentList[$key]['avaliable'] = false;
            }
        }

        $this->iteration ++;
        $this->equipment_id = null;
    }

    //TODO: We should also check when start date changes and fetch equipment if end date present
    public function updatedEditingLoanEndDateTime()
    {
        $this->getBookableEquipment($this->editing->loan->start_date_time, $this->editing->loan->end_date_time);
        $this->iteration ++;
    }
}
