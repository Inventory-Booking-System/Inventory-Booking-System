<?php

namespace App\Http\Livewire\Setup;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
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
use App\Helpers\SQL;
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
    public $expandedCells = [];

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

    public function makeBlankSetup($clearDateTime = false)
    {
        #We must preload an empty loan so @entangle doesnt error
        $this->editing = Setup::make()->setRelation('loan', Loan::make());
        $this->editing->loan->start_date_time = Carbon::now();
        $this->editing->loan->end_date_time = Carbon::now()->add(1, 'hour');

        $equipment_id = null;
        $this->emptyCart();
        $this->iteration ++;

        if ($clearDateTime) {
            $this->dispatchBrowserEvent('datetime-clear');
        }
    }

    public function deleteSelected()
    {
        $this->makeBlankSetup();
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
        $this->makeBlankSetup(true);

        $this->modalType = "Create";
        $this->emit('showModal', 'create');

        $this->updatedEditingLoanEndDateTime();

        // Clear datetime errors from previous modals
        $this->updated();
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

        // Clear datetime errors from previous modals
        $this->updated();
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
        /**
         * We must unset this when using defer on inputs, as otherwise it will
         * try to insert the loan object into the setups table. Presumably this
         * is a bug in Livewire?
         */
        unset($this->editing->loan);
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
        if (Config::get('mail.cc.address')) {
            Mail::to($user->email)->cc(Config::get('mail.cc.address'))->queue(new SetupOrder($this->editing, $this->editing->wasRecentlyCreated));
        } else {
            Mail::to($user->email)->queue(new SetupOrder($this->editing, $this->editing->wasRecentlyCreated));
        }
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function expandCell($cellId)
    {
        array_push($this->expandedCells, $cellId);
        array_unique($this->expandedCells);
    }

    public function collapseCell($cellId)
    {
        if (($key = array_search($cellId, $this->expandedCells)) !== false) {
            unset($this->expandedCells[$key]);
        }
    }

    private function searchById($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        // Handle searching by ID if the user has entered a leading #
        if ($orWhere) {
            $query->orWhere(DB::raw("CONCAT(setups.id, ' ', setups.title)"), 'like', '%'.str_replace('#', '', $search).'%');
        } else {
            $query->where(DB::raw("CONCAT(setups.id, ' ', setups.title)"), 'like', '%'.str_replace('#', '', $search).'%');
        }
    }

    private function searchByUser($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere(DB::raw("CONCAT(users.forename, ' ', users.surname)"), 'like', '%'.$search.'%');
        } else {
            $query->where(DB::raw("CONCAT(users.forename, ' ', users.surname)"), 'like', '%'.$search.'%');
        }
    }

    private function searchByLocation($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('locations.name', 'like', '%'.$search.'%');
        } else {
            $query->where('locations.name', 'like', '%'.$search.'%');
        }
    }

    private function searchByStartDate($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        try {
            $dateTimeString = Carbon::parse($search);
            $dateString = explode(' ', $dateTimeString)[0];
            $timeString = explode(' ', $dateTimeString)[1];
            if ($orWhere) {
                $query->orWhere('loans.start_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('loans.start_date_time', 'like', '%'.$timeString.'%');
            } else {
                $query->where('loans.start_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('loans.start_date_time', 'like', '%'.$timeString.'%');
            }
        } catch(\Throwable $e) {
            // Search string is not a date
        }
    }

    private function searchByEndDate($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        try {
            $dateTimeString = Carbon::parse($search);
            $dateString = explode(' ', $dateTimeString)[0];
            $timeString = explode(' ', $dateTimeString)[1];
            if ($orWhere) {
                $query->orWhere('loans.end_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('loans.end_date_time', 'like', '%'.$timeString.'%');
            } else {
                $query->where('loans.end_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('loans.end_date_time', 'like', '%'.$timeString.'%');
            }
        } catch(\Throwable $e) {
            // Search string is not a date
        }
    }

    private function searchByDetails($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('loans.details', 'like', '%'.$search.'%');
        } else {
            $query->where('loans.details', 'like', '%'.$search.'%');
        }
    }

    private function searchByAssets($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhereHas('loan.assets', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT(name, ' ', '(', tag, ')')"), 'like', '%'.$search.'%');
            });
        } else {
            $query->whereHas('loan.assets', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT(name, ' ', '(', tag, ')')"), 'like', '%'.$search.'%');
            });
        }
    }

    public function getRowsQueryProperty()
    {
        $query = Setup::query()
            ->select('setups.*')
            ->with('location')
            ->with('loan.user')
            ->join('loans', 'setups.loan_id', '=', 'loans.id') // Join loans table so we can get user
            ->join('users', 'loans.user_id', '=', 'users.id') // Join users table so we can search by user name
            ->join('locations', 'setups.location_id', '=', 'locations.id') // Join locations table so we can search by location name
            ->whereHas('loan', function($query){
                $query->where('status_id', '=', '3');
            })
            ->when($this->filters['id'], fn($query, $search) => $this->searchById($query, $search))
            ->when($this->filters['user_id'], fn($query, $search) => $this->searchByUser($query, $search))
            ->when($this->filters['location'], fn($query, $search) => $this->searchByLocation($query, $search))
            ->when($this->filters['start_date_time'], fn($query, $search) => $this->searchByStartDate($query, $search))
            ->when($this->filters['end_date_time'], fn($query, $search) => $this->searchByEndDate($query, $search))
            ->when($this->filters['details'], fn($query, $search) => $this->searchByDetails($query, $search))
            ->when($this->filters['assets'], fn($query, $search) => $this->searchByAssets($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $query->where(function($query) use ($search) {
                $this->searchById($query, $search);
                $this->searchByUser($query, $search, true);
                $this->searchByLocation($query, $search, true);
                $this->searchByStartDate($query, $search, true);
                $this->searchByEndDate($query, $search, true);
                $this->searchByDetails($query, $search, true);
                $this->searchByAssets($query, $search, true);
            }));

        return $this->applySorting($query, 'start_date_time', 'asc');
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
        if (Config::get('mail.cc.address')) {
            Mail::to($user->email)->cc(Config::get('mail.cc.address'))->queue(new SetupOrder($setup, $this->editing->wasRecentlyCreated));
        } else {
            Mail::to($user->email)->queue(new SetupOrder($setup, $this->editing->wasRecentlyCreated));
        }        
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

    public function updated() {
        $this->validateOnly('editing.loan.end_date_time');
    }
}
