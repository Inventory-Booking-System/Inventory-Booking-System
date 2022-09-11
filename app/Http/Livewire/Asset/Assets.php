<?php

namespace App\Http\Livewire\Asset;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asset;

class Assets extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $sortField;
    public $sortDirection = 'asc';
    #->orderBy($this->sortField, $this->sortDirection)

    public function sortBy($field)
    {

    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.asset.assets', [
            'assets' => Asset::search('name', $this->search)->paginate(13),
        ]);
    }
}
