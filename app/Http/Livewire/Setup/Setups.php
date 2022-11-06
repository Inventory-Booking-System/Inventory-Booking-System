<?php

namespace App\Http\Livewire\Setup;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\ShoppingCart\WithShoppingCart;
use App\Models\Setup;
use App\Models\Loan;
use App\Models\User;
use App\Models\Asset;
use App\Models\Location;
use Carbon\Carbon;

class Setups extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithShoppingCart;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'user_id' => null,
        'status_id' => null,
        'start_date_time' => null,
        'end_date_time' => null,
        'title' => null,
        'location_id' => null,
        'details' => null,
    ];

    public $counter = 0;
    public Setup $editing;
    public $equipment_id;

    public $shoppingCart = [];
    public $shoppingCost = 0;
    public $iteration = 0;
    public $avaliableEquipment = [];
    public $status_id = 1;

    public function rules()
    {
        return [
            'editing.loan.user_id' => 'required|integer',
            'editing.loan.status_id' => 'required|string|in:0,1',
            'editing.loan.start_date_time' => 'required|date|before:editing.end_date_time|nullable',
            'editing.loan.end_date_time' => 'required|date|after:editing.start_date_time|nullable',
            'editing.loan.details' => 'nullable|string',
            'editing.title' => 'required|string',
            'editing.location_id' => 'required|numeric|exists:locations,id',
            'equipment_id' => 'nullable|numeric|exists:assets,id',
        ];
    }

    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankSetup();
        $this->users = User::latest()->get();
        $this->locations = Location::latest()->get();

        dd($this->editing);
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankSetup()
    {
        $this->editing = Setup::make();
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
        }, 'setups.csv');
    }

    public function create()
    {
        if ($this->editing->getKey()){
            $this->makeBlankSetup();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(Setup $setup)
    {
        if($this->editing->isNot($setup)){
            $this->editing = $setup;
        }

        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        //dd($this->editing);

        $this->validate();

        dd("Valid");

        $this->editing->start_date_time = carbon::parse($this->editing->start_date_time);
        $this->editing->end_date_time = carbon::parse($this->editing->end_date_time);

        $this->editing->save();

        $loan = Setup::find($this->editing->id);

        //Add equipment issues into equipment_issue_incidents
        $ids = [];
        //dd($this->shoppingCart);
        foreach($this->shoppingCart as $key => $item){
            array_push($ids, ['setup_id' => $this->editing->id, 'setup_id' => $key, 'returned' => $item['returned']]);
        }

        $loan->assets()->sync($ids);

        $this->emit('hideModal', 'edit');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = Setup::query()
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

    public function updatedEquipmentId($id)
    {
        $item = Asset::find($id);

        $this->shoppingCart[$item->id] = [];
        $this->shoppingCart[$item->id]['title'] = $item->name;
        $this->shoppingCart[$item->id]['asset_id'] = $item->tag;
        $this->shoppingCart[$item->id]['returned'] = 0;

        //dd($this->shoppingCart);
    }

    public function removeItem($id)
    {
        if($this->shoppingCart[$id]['quantity'] == 1){
            unset($this->shoppingCart[$id]);
        }elseif($this->shoppingCart[$id]['quantity'] > 1){
            $this->shoppingCart[$id]['quantity'] -= 1;
        }
    }

    public function updatedEditingLoanEndDateTime()
    {
        $this->getBookableEquipment();
        $this->iteration ++;
    }
}
