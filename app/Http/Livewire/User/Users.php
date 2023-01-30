<?php

namespace App\Http\Livewire\User;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\Auth\NewUser;
use App\Models\User;

class Users extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'user_id' => null,
        'email' => null,
    ];

    public $counter = 0;
    public User $editing;
    public $modalType;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.forename' => 'required|string',
            'editing.surname' => 'required|string',
            'editing.email' => 'required|email|unique:users,email,'.$this->editing->id,
            'editing.has_account' => 'required|boolean',
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
        $this->modalType = 'Create';

        if ($this->editing->getKey()){
            $this->makeBlankUser();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(User $user)
    {
        $this->modalType = 'Edit';

        if($this->editing->isNot($user)){
            $this->editing = $user;
        }

        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        $this->validate();

        //If value has been updated to true, we need to generate a one time password and email to user
        if($this->editing->has_account and $this->editing->isDirty('has_account')){
            $password = Str::random(8);
            $this->editing->password = Hash::make($password);

            //Send Email Code
            Mail::to($this->editing->email)->queue(new NewUser($this->editing, $password));
        }

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
            ->when($this->filters['user_id'], fn($query, $user_id) => $query->where('user_id', $user_id))
            ->when($this->filters['email'], fn($query, $email) => $query->where('email', $email))
            ->where(function($query) { // Search
                $query->when($this->filters['search'], fn($query, $search) => $query->where(DB::raw("CONCAT(forename, ' ', surname)"), 'like', '%'.$search.'%'))
                      ->when($this->filters['search'], fn($query, $search) => $query->orWhere('email', 'like', '%'.$search.'%'));
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

        return view('livewire.user.users', [
            'users' => $this->rows,
        ]);
    }
}
