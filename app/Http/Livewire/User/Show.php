<?php

namespace App\Http\Livewire\User;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithDetailsPerPagePagination;
use App\Models\DistributionGroup;
use App\Models\Student;
use App\Models\Loan;
use App\Models\Setup;
use App\Helpers\SQL;

class Show extends Component
{
    use WithDetailsPerPagePagination, WithSorting;

    public $user;
    public $selectedGroupIds = [];

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'id' => null,
        'user_id' => null,
        'status_id' => null,
        'start_date_time' => null,
        'end_date_time' => null,
        'details' => null,
        'assets' => null,
    ];

    protected $queryString = [];

    public function resetFilters()
    {
        $this->reset('filters');
    }

    private function searchById($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->where('loans.id', 'like', '%'.str_replace('#', '', $search).'%');
        } else {
            $query->orWhere('loans.id', 'like', '%'.str_replace('#', '', $search).'%');
        }
    }

    private function searchByUser($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere(DB::raw("CONCAT(users.forename, ' ', users.surname)"), 'like', '%'.$search.'%');
        } else {
            $query->where(DB::raw("CONCAT(users.forename, ' ', users.surname)"), 'like', '%'.$search.'%');
        }
    }

    private function searchByStatus($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        foreach (Loan::getStatusIds() as $id => $status) {
            if (str_contains(strtolower($status), strtolower($search))) {
                if ($orWhere) {
                    $query->orWhere('loans.status_id', $id);
                } else {
                    $query->where('loans.status_id', $id);
                }
            }
        }
    }

    private function searchByStartDate($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        try {
            $dateTimeString = Carbon::parse($search);
            $dateString = explode(' ', $dateTimeString)[0];
            $timeString = explode(' ', $dateTimeString)[1];
            if ($orWhere) {
                $query->orWhere('loans.start_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('loans.start_date_time', 'like', '%'.$timeString.'%');
            } else {
                $query->where('loans.start_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('loans.start_date_time', 'like', '%'.$timeString.'%');
            }
        } catch(\Throwable $e) {
            // Search string is not a date
        }
    }

    private function searchByEndDate($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        try {
            $dateTimeString = Carbon::parse($search);
            $dateString = explode(' ', $dateTimeString)[0];
            $timeString = explode(' ', $dateTimeString)[1];
            if ($orWhere) {
                $query->orWhere('loans.end_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('loans.end_date_time', 'like', '%'.$timeString.'%');
            } else {
                $query->where('loans.end_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('loans.end_date_time', 'like', '%'.$timeString.'%');
            }
        } catch(\Throwable $e) {
            // Search string is not a date
        }
    }

    private function searchByDetails($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('loans.details', 'like', '%'.$search.'%');
        } else {
            $query->where('loans.details', 'like', '%'.$search.'%');
        }
    }

    private function searchByAssets($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhereHas('assets', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT(name, ' ', '(', tag, ')')"), 'like', '%'.$search.'%');
            });
        } else {
            $query->whereHas('assets', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT(name, ' ', '(', tag, ')')"), 'like', '%'.$search.'%');
            });
        }
    }

    public function getRowsQueryProperty()
    {
        $user = $this->user;

        $query = Loan::query()
            ->select('loans.*')
            ->join('users', 'loans.user_id', '=', 'users.id') // Join users table so we can search by user name
            ->whereHas('user', function($query) use($user){
                $query->where('user_id', '=', $user->id);
            })
            ->when($this->filters['id'], fn($query, $search) => $this->searchById($query, $search))
            ->when($this->filters['user_id'], fn($query, $search) => $this->searchByUser($query, $search))
            ->when($this->filters['status_id'], fn($query, $search) => $this->searchByStatus($query, $search))
            ->when($this->filters['start_date_time'], fn($query, $search) => $this->searchByStartDate($query, $search))
            ->when($this->filters['end_date_time'], fn($query, $search) => $this->searchByEndDate($query, $search))
            ->when($this->filters['details'], fn($query, $search) => $this->searchByDetails($query, $search))
            ->when($this->filters['assets'], fn($query, $search) => $this->searchByAssets($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $query->where(function($query) use ($search) {
                $this->searchById($query, $search);
                $this->searchByUser($query, $search, true);
                $this->searchByStatus($query, $search, true);
                $this->searchByStartDate($query, $search, true);
                $this->searchByEndDate($query, $search, true);
                $this->searchByDetails($query, $search, true);
                $this->searchByAssets($query, $search, true);
            }));

        return $this->applySorting($query, 'start_date_time');
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function getRowsProperty()
    {
        return $this->applyPagination($this->rowsQuery);
    }

    public function render()
    {
        return view('livewire.user.show', [
            'loans' => $this->rows,
            'groups' => DistributionGroup::orderBy('name')->get(),
        ]);
    }

    public function mount($user)
    {
        $this->user = Student::with(['distributionGroups', 'bookingAuthoriser'])->findOrFail($user);
        $this->selectedGroupIds = $this->user->distributionGroups()->pluck('distribution_group_id')->map(fn($id) => (string) $id)->toArray();
    }

    public function saveGroups()
    {
        // Sync groups preserving existing expiry dates, adding nulls for new memberships
        $existing = $this->user->distributionGroups()->withPivot('expires_at')
            ->get()->keyBy('id');
        $syncData = [];
        foreach ($this->selectedGroupIds as $groupId) {
            $syncData[$groupId] = [
                'expires_at' => $existing->get($groupId)?->pivot->expires_at ?? null,
            ];
        }
        $this->user->distributionGroups()->sync($syncData);
        $this->user->load('distributionGroups');
    }

    public function getStudentCheckoutAccessProperty()
    {
        return $this->getCheckoutAccessLabel('pos_student_screen_access');
    }

    public function getCheckoutAuthoriserProperty()
    {
        $authoriser = $this->user->bookingAuthoriser ?? $this->user;

        return trim(($authoriser->forename ?? '') . ' ' . ($authoriser->surname ?? '')) ?: '-';
    }

    private function getCheckoutAccessLabel($column)
    {
        $groups = $this->user->distributionGroups;

        if ($groups->contains($column, DistributionGroup::POS_ACCESS_DISABLED)) {
            return 'No';
        }

        if ($groups->contains($column, DistributionGroup::POS_ACCESS_ENABLED)) {
            return 'Yes';
        }

        return 'No';
    }
}