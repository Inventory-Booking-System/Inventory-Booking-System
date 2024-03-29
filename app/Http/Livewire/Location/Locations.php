<?php

namespace App\Http\Livewire\Location;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Location;
use App\Helpers\SQL;

class Locations extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'name' => null,
    ];

    public $counter = 0;
    public Location $editing;
    public $modalType;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.name' => 'required|string',
        ];
    }

    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankLocation();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankLocation()
    {
        $this->editing = Location::make();
    }

    public function deleteSelected()
    {
        $this->makeBlankLocation();
        $this->selectedRowsQuery->delete();

        $this->emit('hideModal', 'confirm');
    }

    public function exportSelected()
    {
        return response()->streamDownload(function() {
            echo $this->selectedRowsQuery->toCsv();
        }, 'locations.csv');
    }

    public function create()
    {
        $this->modalType = 'Create';

        if ($this->editing->getKey()){
            $this->makeBlankLocation();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(Location $location)
    {
        $this->modalType = 'Edit';

        if($this->editing->isNot($location)){
            $this->editing = $location;
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

    private function searchByName($query, $search) {
        $search = SQL::escapeLikeString($search);
        $query->where('name', 'like', '%'.$search.'%');
    }

    public function getRowsQueryProperty()
    {
        $query = Location::query()
            ->when($this->filters['name'], fn($query, $search) => $this->searchByName($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $this->searchByName($query, $search));

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

        return view('livewire.location.locations', [
            'locations' => $this->rows,
        ]);
    }
}
