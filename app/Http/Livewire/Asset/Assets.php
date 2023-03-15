<?php

namespace App\Http\Livewire\Asset;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Asset;
use App\Helpers\SQL;

class Assets extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'name' => null,
        'tag' => null,
        'description' => null,
    ];
    public $expandedCells = [];

    public $counter = 0;
    public Asset $editing;

    protected $queryString = [];
    public $modalType;

    public function rules()
    {
        return [
            'editing.name' => 'required|string',
            'editing.tag' => "required|numeric|unique:assets,tag," . $this->editing->id,
            'editing.description' => 'string',
        ];
    }

    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankAsset();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankAsset()
    {
        $this->editing = Asset::make();
    }

    public function deleteSelected()
    {
        $this->makeBlankAsset();
        $this->selectedRowsQuery->delete();

        $this->emit('hideModal', 'confirm');
    }

    public function exportSelected()
    {
        return response()->streamDownload(function() {
            echo $this->selectedRowsQuery->toCsv();
        }, 'assets.csv');
    }

    public function create()
    {
        $this->modalType = "Create";

        if ($this->editing->getKey()){
            $this->makeBlankAsset();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(Asset $asset)
    {
        $this->modalType = "Edit";

        if($this->editing->isNot($asset)){
            $this->editing = $asset;
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

    private function searchByName($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('name', 'like', '%'.$search.'%');
        } else {
            $query->where('name', 'like', '%'.$search.'%');
        }
    }

    private function searchByTag($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('tag', 'like', '%'.$search.'%');
        } else {            
            $query->where('tag', 'like', '%'.$search.'%');
        }
    }

    private function searchByDescription($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('description', 'like', '%'.$search.'%');
        } else {            
            $query->where('description', 'like', '%'.$search.'%');
        }
    }

    public function getRowsQueryProperty()
    {
        $query = Asset::query()
            ->when($this->filters['name'], fn($query, $search) => $this->searchByName($query, $search))
            ->when($this->filters['tag'], fn($query, $search) => $this->searchByTag($query, $search))
            ->when($this->filters['description'], fn($query, $search) => $this->searchByDescription($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $query->where(function($query) use ($search) {
                $this->searchByName($query, $search);
                $this->searchByTag($query, $search, true);
                $this->searchByDescription($query, $search, true);
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

        return view('livewire.asset.assets', [
            'assets' => $this->rows,
        ]);
    }
}
