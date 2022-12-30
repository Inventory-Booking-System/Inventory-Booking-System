<?php

namespace App\Http\Livewire\DistributionGroup;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\DistributionGroup;
use App\Models\Incident;

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public $distributionGroup;

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
        $distributionGroup = $this->distributionGroup;

        $query = Incident::query()
            ->whereHas('group', function($query) use($distributionGroup){
                $query->where('distribution_id', '=', $distributionGroup->id);
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
        return view('livewire.distribution-group.show', [
            'incidents' => $this->rows,
        ]);
    }

    public function mount($distributionGroup)
    {
        $this->distributionGroup = DistributionGroup::find($distributionGroup);
    }
}
