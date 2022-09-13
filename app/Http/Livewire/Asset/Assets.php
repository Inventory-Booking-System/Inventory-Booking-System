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
    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = ['sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if($this->sortField === $field){
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        }else{
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.asset.assets', [
            'assets' => Asset::search('name', $this->search)->orderBy($this->sortField, $this->sortDirection)->paginate(13),
        ]);
    }
}
