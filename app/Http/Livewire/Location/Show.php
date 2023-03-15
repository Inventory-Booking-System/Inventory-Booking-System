<?php

namespace App\Http\Livewire\Location;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Location;
use App\Models\Setup;
use App\Models\Incident;
use App\Models\Loan;
use App\Helpers\SQL;

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public $location;

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
        // Handle searching by ID if the user has entered a leading #
        if ($orWhere) {
            $query->orWhere(DB::raw("CONCAT(setups.id, ' ', setups.title)"), 'like', '%'.str_replace('#', '', $search).'%');
        } else {
            $query->where(DB::raw("CONCAT(setups.id, ' ', setups.title)"), 'like', '%'.str_replace('#', '', $search).'%');
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
            $query->orWhereHas('loan.assets', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT(name, ' ', '(', tag, ')')"), 'like', '%'.$search.'%');
            });
        } else {
            $query->whereHas('loan.assets', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT(name, ' ', '(', tag, ')')"), 'like', '%'.$search.'%');
            });
        }
    }

    public function getRowsQueryProperty()
    {
        $location = $this->location;

        $query = Setup::query()
            ->select('setups.*')
            ->join('loans', 'setups.loan_id', '=', 'loans.id') // Join loans table so we can get user
            ->join('users', 'loans.user_id', '=', 'users.id') // Join users table so we can search by user name
            ->join('locations', 'setups.location_id', '=', 'locations.id') // Join locations table so we can search by location name
            ->whereHas('location', function($query) use($location){
                $query->where('location_id', '=', $location->id);
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

        return $this->applySorting($query);
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
        return view('livewire.location.show', [
            'setups' => $this->rows,
        ]);
    }

    public function mount($location)
    {
        $this->location = Location::find($location);
    }
}