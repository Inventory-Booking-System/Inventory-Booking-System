<?php

namespace App\Http\Livewire\Asset;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asset;

class Assets extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $showFilters = false;
    public $filters = [
        'search' => '',
        'name' => null,
        'tag' => null,
        'description' => null,
    ];
    public $counter = 0;
    public Asset $editing;

    protected $queryString = ['sortField', 'sortDirection'];

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

    public function sortBy($field)
    {
        if($this->sortField === $field){
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        }else{
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
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

    public function create()
    {
        if ($this->editing->getKey()){
            $this->makeBlankAsset();
        }

        $this->emit('showModal');
    }

    public function edit(Asset $asset)
    {
        if($this->editing->isNot($asset)){
            $this->editing = $asset;
        }

        $this->emit('showModal');
    }

    public function save()
    {
        $this->validate();

        $this->editing->save();

        $this->emit('hideModal');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function render()
    {
        return view('livewire.asset.assets', [
            'assets' => Asset::query()
            ->when($this->filters['name'], fn($query, $name) => $query->where('name', $name))
            ->when($this->filters['tag'], fn($query, $tag) => $query->where('tag', $tag))
            ->when($this->filters['description'], fn($query, $description) => $query->where('description', $description))
            ->when($this->filters['search'], fn($query, $search) => $query->where('name', 'like', '%'.$search.'%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(13),
        ]);
    }
}
