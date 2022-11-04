<?php

namespace App\Http\Livewire\Loan;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Loan;
use App\Models\User;

class Loans extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'user_id' => null,
        'status_id' => null,
        'start_date_time' => null,
        'end_date_time' => null,
        'details' => null,
    ];

    public $counter = 0;
    public Loan $editing;

    public $shoppingCart = [];
    public $shoppingCost = 0;
    public $iteration = 0;
    public $avaliableEquipment = [];
    public $status_id = 1;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.user_id' => 'required|integer',
            'editing.status_id' => 'required|string|in:0,1',
            'editing.start_date_time' => 'required|date|before:end_date|nullable',
            'editing.end_date_time' => 'required|date|after:start_date|nullable',
            'editing.details' => 'nullable|string',
        ];
    }

    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankLoan();
        $this->users = User::latest()->get();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankLoan()
    {
        $this->editing = Loan::make();
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
        }, 'loans.csv');
    }

    public function create()
    {
        if ($this->editing->getKey()){
            $this->makeBlankLoan();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(Loan $loan)
    {
        if($this->editing->isNot($loan)){
            $this->editing = $loan;
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
        $query = Loan::query()
            ->when($this->filters['user_id'], fn($query, $user_id) => $query->where('user_id', $user_id))
            ->when($this->filters['status_id'], fn($query, $status_id) => $query->where('status_id', $status_id))
            ->when($this->filters['start_date_time'], fn($query, $start_date_time) => $query->where('start_date_time', $start_date_time))
            ->when($this->filters['end_date_time'], fn($query, $end_date_time) => $query->where('end_date_time', $end_date_time))
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

        return view('livewire.loan.loans', [
            'loans' => $this->rows,
        ]);
    }
}
