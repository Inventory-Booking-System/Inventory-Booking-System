<?php

namespace App\Http\Livewire\Asset;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Asset;
use App\Models\Loan;

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public Asset $asset;

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
        $asset = $this->asset;

        $query = Loan::query()
            ->whereHas('assets', function($query) use($asset){
                $query->where('tag', '=', $asset->tag);
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
        return view('livewire.asset.show', [
            'loans' => $this->rows,
        ]);
    }

    public function mount($asset)
    {
        $this->asset = Asset::find($asset->id);
    }
}
