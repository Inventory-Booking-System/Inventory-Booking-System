<?php

namespace App\Http\Livewire\EquipmentIssue;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\ShoppingCart\WithShoppingCart;
use App\Models\EquipmentIssue;
use App\Models\User;

class EquipmentIssues extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';       #Use boostrap theme when displaying data with pagination
    protected $queryString = [];                    #Use on tables when displaying data based on the user request
    public $showFilters = false;                    #These are displayed above each column in the table
    public $filters = [                             #Our default filters to apply to the tables
        'title' => null,
        'cost' => null,
        'search' => null,
    ];

    public EquipmentIssue $editing;                 #Data relating to the current Equipment Issue
    public $modalType;                              #Whether the user is creating/edit a EquipmentIssue so we can get correct wording

    #TODO: Is this needed?
    public $counter;                                #???

    #TODO: Start date rules look wrong?
    public function rules()
    {
        return [
            'editing.title' => 'required|string',
            'editing.cost' => 'required|numeric',
        ];
    }

    #TODO: Is this needed?
    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankEquipmentIssue();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankEquipmentIssue()
    {
        $this->editing = EquipmentIssue::make();
    }

    public function deleteSelected()
    {
        $this->makeBlankEquipmentIssue();
        $this->selectedRowsQuery->delete();

        $this->emit('hideModal', 'confirm');
    }

    public function exportSelected()
    {
        return response()->streamDownload(function() {
            echo $this->selectedRowsQuery->toCsv();
        }, 'equipmentIssues.csv');
    }

    public function create()
    {
        //TODO: Can we re-implement this so form input is saved if modal closed on accident
        //I Removed for now as it wasen't resetting properly between editing/creating etc
        if ($this->editing->getKey()){
        }

        $this->makeBlankEquipmentIssue();

        $this->modalType = "Create";
        $this->emit('showModal', 'create');
    }

    public function edit(EquipmentIssue $equipmentIssue)
    {
        #If the EquipmentIssue is the same as the previous Equipment Issue that we have stored, just show the modal
        #in the current state that is was when it was last closed rather than wiping the data.
        //TODO: Can we re-implement this so form input is saved if modal closed on accident
        //I Removed for now as it wasen't resetting properly between editing/creating etc
        if($this->editing->isNot($equipmentIssue)){
        }

        $this->editing = $equipmentIssue;

        //Display the modal to the user
        $this->modalType = "Edit";
        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        //Make sure all the data the user entered into the modal is valid
        $this->validate();

        $this->editing->push();

        //Hide the modal from the user
        $this->emit('hideModal', 'edit');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = EquipmentIssue::query()
            ->when($this->filters['title'], fn($query, $name) => $query->where('title', $name))
            ->when($this->filters['cost'], fn($query, $name) => $query->where('cost', $name))
            ->where(function($query) { // Search
                // Title
                $query->when($this->filters['search'], fn($query, $search) => 
                    $query->where('title', 'like', '%'.$search.'%'))

                // Cost
                ->when($this->filters['search'], fn($query, $search) => 
                    $query->orWhere('cost', 'like', '%'.$search.'%'));
            });

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

        return view('livewire.equipment-issue.equipment-issues', [
            'equipmentIssues' => $this->rows,
        ]);
    }
}
