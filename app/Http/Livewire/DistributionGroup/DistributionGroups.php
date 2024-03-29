<?php

namespace App\Http\Livewire\DistributionGroup;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\ShoppingCart\WithShoppingCart;
use App\Models\DistributionGroup;
use App\Models\User;
use App\Helpers\SQL;

class DistributionGroups extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithShoppingCart;

    protected $paginationTheme = 'bootstrap';       #Use boostrap theme when displaying data with pagination
    protected $queryString = [];                    #Use on tables when displaying data based on the user request
    public $showFilters = false;                    #These are displayed above each column in the table
    public $filters = [                             #Our default filters to apply to the tables
        'name' => null,
        'users' => null,
        'search' => null,
    ];

    public DistributionGroup $editing;                           #Data relating to the current Distribution Group excluding any assets
    public $user_id;                           #Used to trigger update on the select2 dropdown as we cannot use wire:model due to wire:ignore in place
    public $iteration = 0;                          #Increment anytime we want the Select2 Dropdown to update (as we are using wire:ignore to stop it updating each render)
    public $modalType;                              #Whether the user is creating/edit a Distribution Group so we can get correct wording

    #TODO: Is this needed?
    public $counter;                                #???

    #TODO: Start date rules look wrong?
    public function rules()
    {
        return [
            'editing.name' => 'nullable|string',
            'user_id' => 'nullable|numeric|exists:assets,id',
        ];
    }

    #TODO: Is this needed?
    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankDistributionGroup();
        $this->users = User::latest()->get();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankDistributionGroup()
    {
        $this->editing = DistributionGroup::make();
        $user_id = null;
        $this->emptyCart();
        $this->iteration ++;
    }

    public function deleteSelected()
    {
        $this->makeBlankDistributionGroup();
        $this->selectedRowsQuery->delete();

        $this->emit('hideModal', 'confirm');
    }

    public function exportSelected()
    {
        return response()->streamDownload(function() {
            echo $this->selectedRowsQuery->toCsv();
        }, 'distributionGroups.csv');
    }

    public function create()
    {
        //TODO: Can we re-implement this so form input is saved if modal closed on accident
        //I Removed for now as it wasen't resetting properly between editing/creating etc
        if ($this->editing->getKey()){
        }

        $this->makeBlankDistributionGroup();

        $this->modalType = "Create";
        $this->emit('showModal', 'create');

        //Populate users dropdown
        $this->getBookableUsers();
        $this->iteration ++;
    }

    public function edit(DistributionGroup $distributionGroup)
    {
        #If the Distribution Group is the same as the previous Distribution Group that we have stored, just show the modal
        #in the current state that is was when it was last closed rather than wiping the data.
        //TODO: Can we re-implement this so form input is saved if modal closed on accident
        //I Removed for now as it wasen't resetting properly between editing/creating etc
        if($this->editing->isNot($distributionGroup)){
        }

        $this->emptyCart();
        $this->editing = $distributionGroup;

        //Load users from distribution groups users model into the shopping cart
        $this->editing->users->each(function ($item, $key) {
            $this->addItemToCart($item, false);
        });

        //Display the modal to the user
        $this->modalType = "Edit";
        $this->emit('showModal', 'edit');

        //Populate users dropdown
        $this->getBookableUsers();
        $this->iteration ++;
    }

    public function save()
    {
        //Make sure all the data the user entered into the modal is valid
        $this->validate();

        $this->editing->push();

        //Update assets in database
        //TODO: We need to validate ShoppingCart Data. Can this be done through laravel rules?
        $distributionGroup = DistributionGroup::find($this->editing->id);
        $ids = [];
        foreach($this->shoppingCart as $key => $item){
            array_push($ids, $item['id']);
        }

        //dd($ids);

        $distributionGroup->users()->sync($ids);

        //Hide the modal from the user
        $this->emit('hideModal', 'edit');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    private function searchByName($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->where('name', 'like', '%'.$search.'%');
        } else {
            $query->orWhere('name', 'like', '%'.$search.'%');
        }
    }

    private function searchByUsers($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhereHas('users', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT(forename, ' ', surname)"), 'like', '%'.$search.'%');
            });
        } else {
            $query->whereHas('users', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT(forename, ' ', surname)"), 'like', '%'.$search.'%');
            });
        }
    }

    public function getRowsQueryProperty()
    {
        $query = DistributionGroup::query()
            ->with('users')
            ->when($this->filters['name'], fn($query, $search) => $this->searchByName($query, $search))
            ->when($this->filters['users'], fn($query, $search) => $this->searchByUsers($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $query->where(function($query) use ($search) {
                $this->searchByName($query, $search);
                $this->searchByUsers($query, $search, true);
            }));

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

        return view('livewire.distribution-group.distribution-groups', [
            'distributionGroups' => $this->rows,
        ]);
    }

    public function removeItem($id)
    {
        $this->removeItemFromCart($id);

        $this->iteration ++;
    }

    public function isUserPartofDistributionGroup($id)
    {
        $distributionGroup = DistributionGroup::find($this->editing->id);

        if($distributionGroup !== null){
            return DistributionGroup::find($this->editing->id)->whereHas('users', function($query) use ($id){
                $query->where('user_id', $id);
            });
        }

        return false;
    }

    public function updatedUserId($id)
    {
        $item = User::find($id);

        if($this->isUserPartofDistributionGroup($id)){
            $this->addItemToCart($item, false);
        }else{
            $this->addItemToCart($item, true);
        }

        $this->iteration ++;
        $this->user_id = null;
    }
}
