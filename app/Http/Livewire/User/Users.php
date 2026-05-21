<?php

namespace App\Http\Livewire\User;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Staff;
use App\Helpers\SQL;
use App\Models\DistributionGroup;

class Users extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'user_id' => null,
        'email' => null,
        'description' => null,
    ];

    public $counter = 0;
    public Student $editing;
    public $modalType;
    public $key = 0;
    public $allStaff = [];
    public $allGroups = [];
    public $selectedGroupIds = [];
    public $groupExpiries = [];  // keyed by group id => expires_at string or null
    public $groupExpiryEnabled = [];  // keyed by group id => bool

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.forename' => 'required|string',
            'editing.surname' => 'required|string',
            'editing.email' => 'required|email|unique:users,email,' . $this->editing->id,
            'editing.description' => 'nullable|string',
            'editing.booking_authoriser_user_id' => ['nullable', \Illuminate\Validation\Rule::exists('users', 'id')->where('type', 'staff')],
            'selectedGroupIds' => 'nullable|array',
            'selectedGroupIds.*' => 'exists:distribution_groups,id',
            'groupExpiries' => 'nullable|array',
            'groupExpiries.*' => 'nullable|date',
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
        $this->editing = Student::make();
        $this->selectedGroupIds = [];
        $this->groupExpiries = [];
        $this->groupExpiryEnabled = [];
    }

    public function deleteSelected()
    {
        $this->makeBlankUser();
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

        $this->populateAllStaff();
        $this->populateAllGroups();
        $this->selectedGroupIds = [];
        $this->groupExpiries = [];
        $this->groupExpiryEnabled = [];
        $this->key = rand();

        $this->emit('showModal', 'edit');
    }

    private function populateAllStaff()
    {
        $this->allStaff = Staff::orderBy('forename')->orderBy('surname')->get();
    }

    private function populateAllGroups()
    {
        $this->allGroups = DistributionGroup::orderBy('name')->get();
    }

    public function edit(Student $student)
    {
        $this->modalType = 'Edit';

        if($this->editing->isNot($student)){
            $this->editing = $student;
        }

        $this->populateAllStaff();
        $this->populateAllGroups();
        $this->selectedGroupIds = $this->editing
            ->distributionGroups()
            ->pluck('distribution_group_id')
            ->map(fn($id) => (string) $id)
            ->toArray();

        // Load existing expiry dates keyed by group id
        $this->groupExpiries = [];
        $this->groupExpiryEnabled = [];
        foreach ($this->editing->distributionGroups()->withPivot('expires_at')->get() as $group) {
            $expiresAt = $group->pivot->expires_at;
            $this->groupExpiries[(string) $group->id] = $expiresAt
                ? \Carbon\Carbon::parse($expiresAt)->format('Y-m-d')
                : null;
            $this->groupExpiryEnabled[(string) $group->id] = !empty($expiresAt);
        }

        $this->key = rand();

        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        $this->validate();

        $this->editing->save();

        // Sync groups with expiry dates
        $syncData = [];
        foreach ($this->selectedGroupIds as $groupId) {
            $enabled = $this->groupExpiryEnabled[(string) $groupId] ?? false;
            $expires = $enabled ? ($this->groupExpiries[(string) $groupId] ?? null) : null;
            $syncData[$groupId] = ['expires_at' => $expires ?: null];
        }
        $this->editing->distributionGroups()->sync($syncData);

        $this->emit('hideModal', 'edit');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    private function searchByUser($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere(DB::raw("CONCAT(forename, ' ', surname)"), 'like', '%'.$search.'%');
        } else {
            $query->where(DB::raw("CONCAT(forename, ' ', surname)"), 'like', '%'.$search.'%');
        }
    }

    private function searchByEmail($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('email', 'like', '%'.$search.'%');
        } else {
            $query->where('email', 'like', '%'.$search.'%');
        }
    }

    private function searchByDescription($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('description', 'like', '%'.$search.'%');
        } else {
            $query->where('description', 'like', '%'.$search.'%');
        }
    }

    public function getRowsQueryProperty()
    {
        $query = Student::query()
            ->with(['distributionGroups', 'bookingAuthoriser'])
            ->when($this->filters['user_id'], fn($query, $search) => $this->searchByUser($query, $search))
            ->when($this->filters['email'], fn($query, $search) => $this->searchByEmail($query, $search))
            ->when($this->filters['description'], fn($query, $search) => $this->searchByDescription($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $query->where(function($query) use ($search) {
                $this->searchByUser($query, $search);
                $this->searchByEmail($query, $search, true);
                $this->searchByDescription($query, $search, true);
            }));

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
            'students' => $this->rows,
        ]);
    }
}
