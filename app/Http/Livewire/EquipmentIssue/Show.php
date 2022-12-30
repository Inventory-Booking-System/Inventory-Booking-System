<?php

namespace App\Http\Livewire\EquipmentIssue;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\EquipmentIssue;
use App\Models\Incident;

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public $equipmentIssue;

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
        $equipmentIssue = $this->equipmentIssue;

        $query = Incident::query()
            ->whereHas('issues', function($query) use($equipmentIssue){
                $query->where('equipment_issue_id', '=', $equipmentIssue->id);
            })
            ->when($this->filters['search'], fn($query, $search) => $query->where('title', 'like', '%'.$search.'%'));

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
        return view('livewire.equipment-issue.show', [
            'incidents' => $this->rows,
        ]);
    }

    public function mount($equipmentIssue)
    {
        $this->equipmentIssue = EquipmentIssue::find($equipmentIssue);
    }
}
