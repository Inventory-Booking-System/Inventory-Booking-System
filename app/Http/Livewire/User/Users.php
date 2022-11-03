<?php

namespace App\Http\Livewire\User;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\User;

class Users extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'forename' => null,
        'surname' => null,
        'email' => null,
    ];

    public $counter = 0;
    public User $editing;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.forename' => 'required|string',
            'editing.surname' => 'required|string',
            'editing.email' => 'required|email|unique:users,email,'.$this->editing->id,
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
            ->when($this->filters['forename'], fn($query, $forename) => $query->where('forename', $forename))
            ->when($this->filters['surname'], fn($query, $surname) => $query->where('surname', $surname))
            ->when($this->filters['email'], fn($query, $email) => $query->where('email', $email))
            ->when($this->filters['search'], fn($query, $search) => $query->where('forename', 'like', '%'.$search.'%'));

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

        return view('livewire.user.users', [
            'users' => $this->rows,
        ]);
    }
}
