<?php

namespace App\Http\Livewire\Setup;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Setup;

class Setups extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'start_date' => null,
        'end_date' => null,
        'status_id' => null,
        'details' => null,
        'user_id' => null,
    ];

    public $counter = 0;
    public Loan $editing;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.user_id' => 'required|integer',
            'editing.start_date' => 'required|date|before:end_date|nullable',
            'editing.end_date' => 'required|date|after:start_date|nullable',
            'editing.details' => 'nullable|string',
            'editing.status_id' => 'required|string|in:0,1',
        ];
    }

    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankUser();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankUser()
    {
        $this->editing = User::make();
    }

    public function deleteSelected()
    {
        $this->selectedRowsQuery->delete();

        $this->emit('hideModal', 'confirm');
    }

    public function exportSelected()
    {
        return response()->streamDownload(function() {
            echo $this->selectedRowsQuery->toCsv();
        }, 'users.csv');
    }

    public function create()
    {
        if ($this->editing->getKey()){
            $this->makeBlankUser();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(User $user)
    {
        if($this->editing->isNot($user)){
            $this->editing = $user;
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

    public function getRowsQueryProperty()
    {
        $query = User::query()
            ->when($this->filters['start_date'], fn($query, $start_date) => $query->where('start_date', $start_date))
            ->when($this->filters['end_date'], fn($query, $end_date) => $query->where('end_date', $end_date))
            ->when($this->filters['details'], fn($query, $details) => $query->where('details', $details))
            ->when($this->filters['search'], fn($query, $search) => $query->where('details', 'like', '%'.$search.'%'));

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

        return view('livewire.setup.setups', [
            'setups' => $this->rows,
        ]);
    }
}
