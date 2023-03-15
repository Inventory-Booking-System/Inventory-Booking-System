<?php

namespace App\Http\Livewire\Incident;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
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
use App\Helpers\SQL;
use Carbon\Carbon;

class Incidents extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions, WithShoppingCart;

    protected $paginationTheme = 'bootstrap';
    protected $queryString = [];
    public $showFilters = false;
    public $filters = [
        'search' => '',
        'id' => null,
        'start_date_time' => null,
        'location_id' => null,
        'distribution_id' => null,
        'equipment_id' => null,
        'evidence' => null,
        'details' => null,
    ];
    public $expandedCells = [];

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
            'editing.resolution' => 'string|nullable',
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
        $this->makeBlankIncident();
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

    public function resolve(Incident $incident)
    {
        $this->modalType = "resolve";

        $this->emptyCart();
        $this->editing = $incident;


        $this->editing->issues->each(function ($item, $key) {
            $this->addItemToCart($item, false, "issue");
        });

        $this->emit('showModal', 'resolve');
    }

    public function save()
    {
        $this->validate();

        $this->editing->start_date_time = carbon::parse($this->editing->start_date_time);
        $this->editing->created_by = Auth::id();
        if($this->modalType == "resolve"){
            $this->editing->status_id = 1;
        }

        $this->editing->save();

        $incident = Incident::find($this->editing->id);

        //Add equipment issues into equipment_issue_incidents
        //Load assets from loans model into the shopping cart
        $ids = [];
        foreach($this->shoppingCart as $key => $item){
            $ids[$item['id']] = ['quantity' => $item['pivot']['quantity']];
        }
        $incident->issues()->sync($ids);

        $this->emit('hideModal', strtolower($this->modalType));

        //Send the email to the user
        $users = $incident->group->users->pluck('email');        
        if (Config::get('mail.cc.address')) {
            Mail::to($users)->cc(Config::get('mail.cc.address'))->queue(new IncidentOrder($this->editing, $this->editing->wasRecentlyCreated, $this->shoppingCost));
        } else {
            Mail::to($users)->queue(new IncidentOrder($this->editing, $this->editing->wasRecentlyCreated, $this->shoppingCost));
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
        if ($orWhere) {
            $query->orWhere('incidents.id', 'like', '%'.str_replace('#', '', $search).'%');
        } else {            
            $query->where('incidents.id', 'like', '%'.str_replace('#', '', $search).'%');
        }
    }

    private function searchByStartDate($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        try {
            $dateTimeString = Carbon::parse($search);
            $dateString = explode(' ', $dateTimeString)[0];
            $timeString = explode(' ', $dateTimeString)[1];
            if ($orWhere) {
                $query->orWhere('incidents.start_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('incidents.start_date_time', 'like', '%'.$timeString.'%');
            } else {
                $query->where('incidents.start_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('incidents.start_date_time', 'like', '%'.$timeString.'%');
            }
        } catch(\Throwable $e) {
            // Search string is not a date
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

    private function searchByDistributionGroup($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('distribution_groups.name', 'like', '%'.$search.'%');
        } else {
            $query->where('distribution_groups.name', 'like', '%'.$search.'%');
        }
    }

    private function searchByEquipmentIssues($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhereHas('issues', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT('x', equipment_issue_incident.quantity, ' ', equipment_issues.title)"), 'like', '%'.$search.'%');
            });
        } else {
            $query->whereHas('issues', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT('x', equipment_issue_incident.quantity, ' ', equipment_issues.title)"), 'like', '%'.$search.'%');
            });
        }
    }

    private function searchByEvidence($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('incidents.evidence', 'like', '%'.$search.'%');
        } else {
            $query->where('incidents.evidence', 'like', '%'.$search.'%');
        }
    }

    private function searchByDetails($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('incidents.details', 'like', '%'.$search.'%');
        } else {
            $query->where('incidents.details', 'like', '%'.$search.'%');
        }
    }

    public function getRowsQueryProperty()
    {
        $query = Incident::query()
            ->select('incidents.*')
            ->join('locations', 'incidents.location_id', '=', 'locations.id') // Join locations table so we can search by location name
            ->join('distribution_groups', 'incidents.distribution_id', '=', 'distribution_groups.id') // Join distribution_groups table so we can search by distribution group name
            ->where('status_id', '=', '0')
            ->when($this->filters['id'], fn($query, $search) => $this->searchById($query, $search))
            ->when($this->filters['start_date_time'], fn($query, $search) => $this->searchByStartDate($query, $search))
            ->when($this->filters['location_id'], fn($query, $search) => $this->searchByLocation($query, $search))
            ->when($this->filters['distribution_id'], fn($query, $search) => $this->searchByDistributionGroup($query, $search))
            ->when($this->filters['equipment_id'], fn($query, $search) => $this->searchByEquipmentIssues($query, $search))
            ->when($this->filters['evidence'], fn($query, $search) => $this->searchByEvidence($query, $search))
            ->when($this->filters['details'], fn($query, $search) => $this->searchByDetails($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $query->where(function($query) use ($search) {
                $this->searchById($query, $search);
                $this->searchByStartDate($query, $search, true);
                $this->searchByLocation($query, $search, true);
                $this->searchByDistributionGroup($query, $search, true);
                $this->searchByEquipmentIssues($query, $search, true);
                $this->searchByEvidence($query, $search, true);
                $this->searchByDetails($query, $search, true);
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
