<?php

namespace App\Http\Livewire\Location;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Location;
use App\Models\Setup;
use App\Models\Incident;

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public $location;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'name' => null,
        'tag' => null,
        'description' => null,
    ];

    protected $queryString = [];

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $location = $this->location;

        $query = Setup::query()
            ->whereHas('location', function($query) use($location){
                $query->where('location_id', '=', $location->id);
            })
            ->when($this->filters['search'], fn($query, $search) => $query->where('name', 'like', '%'.$search.'%'));

        return $this->applySorting($query);
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function getRowsProperty()
    {
        return $this->applyPagination($this->rowsQuery);
    }

    public function render()
    {
        return view('livewire.location.show', [
            'setups' => $this->rows,
        ]);
    }

    public function mount($location)
    {
        $this->location = Location::find($location);
    }
}