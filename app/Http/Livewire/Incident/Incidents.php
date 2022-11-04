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

    public $shoppingCart = [];
    public $shoppingCost = 0;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.start_date_time' => 'required|date',
            'editing.location_id' => 'required|numeric|exists:locations,id',
            'editing.distribution_id' => 'required|numeric|exists:distribution_groups,id',
            'editing.equipment_id' => 'required|numeric|exists:equipment_issues,id',
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

        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        $this->validate();

        $this->editing->save();

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
}
