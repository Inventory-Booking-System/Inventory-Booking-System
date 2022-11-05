<?php

namespace App\Http\Livewire\Incident;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Incident;
use App\Models\Location;
use App\Models\DistributionGroup;
use App\Models\EquipmentIssue;
use App\Models\EquipmentIssueIncident;
use Carbon\Carbon;

class Incidents extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'start_date_time' => null,
        'location_id' => null,
        'distribution_id' => null,
        'evidence' => null,
        'details' => null,
    ];

    public $counter = 0;
    public Incident $editing;
    public $equipment_id;

    public $shoppingCart = [];
    public $shoppingCost = 0;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.start_date_time' => 'required|date',
            'editing.location_id' => 'required|numeric|exists:locations,id',
            'editing.distribution_id' => 'required|numeric|exists:distribution_groups,id',
            'equipment_id' => 'nullable|numeric|exists:equipment_issues,id',
            'editing.evidence' => 'required|string',
            'editing.details' => 'required|string',
        ];
    }

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
        $this->shoppingCart = [];
        $this->shoppingCost = 0;
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
        if ($this->editing->getKey()){
            $this->makeBlankIncident();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(Incident $incident)
    {
        if($this->editing->isNot($incident)){
            $this->editing = $incident;
        }

        //Populate Shopping Cart
        $this->shoppingCart = [];
        $this->editing->issues->each(function ($item, $key) {
            $this->shoppingCart[$item->id] = [];
            $this->shoppingCart[$item->id]['quantity'] = $item->pivot->quantity;
            $this->shoppingCart[$item->id]['title'] = $item->title;
            $this->shoppingCart[$item->id]['cost'] = $item->cost;
        });

        $this->updateShoppingCartCost();

        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        $this->validate();

        $this->editing->start_date_time = carbon::parse($this->editing->start_date_time);

        $this->editing->save();

        $incident = Incident::find($this->editing->id);

        //Add equipment issues into equipment_issue_incidents
        $ids = [];
        foreach($this->shoppingCart as $key => $item){
            array_push($ids, ['incident_id' => $this->editing->id, 'equipment_issue_id' => $key, 'quantity' => $item['quantity']]);
        }

        $incident->issues()->sync($ids);

        $this->emit('hideModal', 'edit');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = Incident::query()
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

        if(isset($this->shoppingCart[$item->id])){
            $this->shoppingCart[$item->id]['quantity'] += 1;
        }else{
            $this->shoppingCart[$item->id] = [];
            $this->shoppingCart[$item->id]['quantity'] = 1;
            $this->shoppingCart[$item->id]['title'] = $item->title;
            $this->shoppingCart[$item->id]['cost'] = $item->cost;
        }

        $this->updateShoppingCartCost();
    }

    public function removeItem($id)
    {
        if($this->shoppingCart[$id]['quantity'] == 1){
            unset($this->shoppingCart[$id]);
        }elseif($this->shoppingCart[$id]['quantity'] > 1){
            $this->shoppingCart[$id]['quantity'] -= 1;
        }

        $this->updateShoppingCartCost();
    }

    private function updateShoppingCartCost()
    {
        $this->shoppingCost = 0;
        foreach($this->shoppingCart as $key => $item){
            $this->shoppingCost += $item['cost'];
        }
    }
}
