<?php

namespace App\Http\Livewire\Staff;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\Auth\NewUser;
use App\Models\Staff as StaffModel;
use App\Helpers\SQL;
use Illuminate\Support\Facades\DB;

class Staff extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'user_id' => null,
        'email' => null,
    ];

    public StaffModel $editing;
    public $modalType;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.forename' => 'required|string',
            'editing.surname' => 'required|string',
            'editing.email' => 'required|email|unique:users,email,' . $this->editing->id,
            'editing.has_account' => 'required|boolean',
        ];
    }

    public function mount()
    {
        $this->makeBlankStaff();
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function makeBlankStaff()
    {
        $this->editing = StaffModel::make();
    }

    public function deleteSelected()
    {
        $this->makeBlankStaff();
        $this->selectedRowsQuery->delete();

        $this->emit('hideModal', 'confirm');
    }

    public function exportSelected()
    {
        return response()->streamDownload(function () {
            echo $this->selectedRowsQuery->toCsv();
        }, 'staff.csv');
    }

    public function create()
    {
        $this->modalType = 'Create';

        if ($this->editing->getKey()) {
            $this->makeBlankStaff();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(StaffModel $staff)
    {
        $this->modalType = 'Edit';

        if ($this->editing->isNot($staff)) {
            $this->editing = $staff;
        }

        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        $this->validate();

        if ($this->editing->has_account && $this->editing->isDirty('has_account')) {
            $password = Str::random(8);
            $this->editing->password = Hash::make($password);

            Mail::to($this->editing->email)->queue(new NewUser($this->editing, $password));
        }

        $this->editing->save();

        $this->emit('hideModal', 'edit');
    }

    public function resetPassword($id)
    {
        $staff = StaffModel::findOrFail($id);
        $staff->password_set = false;
        $staff->save();
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    private function searchByName($query, $search, $orWhere = false)
    {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere(DB::raw("CONCAT(forename, ' ', surname)"), 'like', '%' . $search . '%');
        } else {
            $query->where(\Illuminate\Support\Facades\DB::raw("CONCAT(forename, ' ', surname)"), 'like', '%' . $search . '%');
        }
    }

    private function searchByEmail($query, $search, $orWhere = false)
    {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('email', 'like', '%' . $search . '%');
        } else {
            $query->where('email', 'like', '%' . $search . '%');
        }
    }

    public function getRowsQueryProperty()
    {
        $query = StaffModel::query()
            ->when($this->filters['user_id'], fn ($query, $search) => $this->searchByName($query, $search))
            ->when($this->filters['email'], fn ($query, $search) => $this->searchByEmail($query, $search))
            ->when($this->filters['search'], fn ($query, $search) => $query->where(function ($query) use ($search) {
                $this->searchByName($query, $search);
                $this->searchByEmail($query, $search, true);
            }));

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->applyPagination($this->rowsQuery);
    }

    public function render()
    {
        if ($this->selectAll) {
            $this->selectPageRows();
        }

        return view('livewire.staff.staff', [
            'staffMembers' => $this->rows,
        ]);
    }
}
