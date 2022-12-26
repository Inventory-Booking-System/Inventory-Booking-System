<?php

namespace App\Http\Livewire\Incident;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\ShoppingCart\WithShoppingCart;
use App\Models\Incident;
use App\Models\Location;
use App\Models\User;
use App\Models\DistributionGroup;
use App\Models\EquipmentIssue;
use App\Models\EquipmentIssueIncident;
use App\Mail\Incident\IncidentOrder;
use Carbon\Carbon;

class Incidents extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithShoppingCart;

    protected $paginationTheme = 'bootstrap';
    protected $queryString = [];
    public $showFilters = false;
    public $filters = [
        'search' => '',
        'start_date_time' => null,
        'location_id' => null,
        'distribution_id' => null,
        'evidence' => null,
        'details' => null,
    ];

    public Incident $editing;               #Data relating to the current incident
    public $equipment_id;                   #Used to trigger update on the select2 dropdown as we cannot use wire:model due to wire:ignore in place
    public $iteration = 0;                  #Increment anytime we want the Select2 Dropdown to update (as we are using wire:ignore to stop it updating each render)
    public $modalType;                      #Whether the user is creating/edit a loan so we can get correct wording

    #TODO: Is this needed?
    public $counter = 0;                            #???

    public function rules()
    {
        return [
            'editing.start_date_time' => 'required|date',
            'editing.location_id' => 'required|numeric|exists:locations,id',
            'editing.distribution_id' => 'required|numeric|exists:distribution_groups,id',
            'editing.evidence' => 'required|string',
            'editing.details' => 'required|string',
            'equipment_id' => 'nullable|numeric|exists:equipment_issues,id',
        ];
    }

    #TODO: Is this needed
    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankIncident();
        $this->locations = Location::latest()->get();
        $this->distributions = DistributionGroup::latest()->get();
        $this->equipmentIssues = EquipmentIssue::latest()->get();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankIncident()
    {
        $this->editing = Incident::make();

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
        }, 'incidents.csv');
    }

    public function create()
    {
        //TODO: Can we re-implement this so form input is saved if modal closed on accident
        //I Removed for now as it wasen't resetting properly between editing/creating etc
        if ($this->editing->getKey()){
        }
        $this->makeBlankIncident();

        $this->modalType = "Create";
        $this->emit('showModal', 'create');
    }

    public function edit(Incident $incident)
    {
        #If the loan is the same as the previous loan that we have stored, just show the modal
        #in the current state that is was when it was last closed rather than wiping the data.
        //TODO: Can we re-implement this so form input is saved if modal closed on accident
        //I Removed for now as it wasen't resetting properly between editing/creating etc
        if($this->editing->isNot($incident)){
        }

        $this->emptyCart();
        $this->editing = $incident;

        $this->editing->issues->each(function ($item, $key) {
            $this->addItemToCart($item, false, "issue");
        });

        //Display the modal to the user
        $this->modalType = "Edit";
        $this->emit('showModal', 'edit');

        $this->updateItemCostInCart();
    }

    public function save()
    {
        $this->validate();

        $this->editing->start_date_time = carbon::parse($this->editing->start_date_time);

        $this->editing->save();

        $incident = Incident::find($this->editing->id);

        //Add equipment issues into equipment_issue_incidents
        //Load assets from loans model into the shopping cart
        $ids = [];
        foreach($this->shoppingCart as $key => $item){
            $ids[$item['id']] = ['quantity' => $item['pivot']['quantity']];
        }
        $incident->issues()->sync($ids);

        $this->emit('hideModal', 'edit');

        //Send the email to the user
        $users = $incident->group->users->pluck('email');
        Mail::to($users)->queue(new IncidentOrder($this->editing, $this->editing->wasRecentlyCreated, $this->shoppingCost));
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = Incident::query()
            ->with('issues')
            ->with('location')
            ->with('group')
            ->when($this->filters['start_date_time'], fn($query, $start_date_time) => $query->where('start_date_time', $start_date_time))
            ->when($this->filters['location_id'], fn($query, $location_id) => $query->where('location_id', $location_id))
            ->when($this->filters['distribution_id'], fn($query, $distribution_id) => $query->where('distribution_id', $distribution_id))
            ->when($this->filters['evidence'], fn($query, $evidence) => $query->where('evidence', $evidence))
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

        return view('livewire.incident.incidents', [
            'incidents' => $this->rows,
        ]);
    }

    public function updatedEquipmentId($id)
    {
        $item = EquipmentIssue::find($id);

        $this->addItemToCart($item, true, 'issue');

        $this->updateItemCostInCart();
    }

    public function removeItem($id)
    {
        $this->removeItemFromCart($id, true);

        $this->updateItemCostInCart();
    }


}
