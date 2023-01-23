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

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public $location;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'name' => null,
        'tag' => null,
        'description' => null,
    ];

    protected $queryString = [];

    public function resetFilters()
    {
        $this->reset('filters');
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
            ->where(function($query) { // Search
                // Loan ID
                $query->when($this->filters['search'], fn($query, $search) =>
                    // Handle searching by ID if the user has entered a leading #
                    $query->where(DB::raw("CONCAT(setups.id, ' ', setups.title)"), 'like', '%'.str_replace('#', '', $search).'%'))

                // User
                ->when($this->filters['search'], fn($query, $search) =>
                    $query->orWhere(DB::raw("CONCAT(users.forename, ' ', users.surname)"), 'like', '%'.$search.'%'))

                // Status
                ->when($this->filters['search'], function($query, $search) {
                    foreach (Loan::getStatusIds() as $id => $status) {
                        if (str_contains(strtolower($status), strtolower($search))) {
                            $query->orWhere('loans.status_id', $id);
                        }
                    }
                })

                // Location
                ->when($this->filters['search'], fn($query, $search) =>
                    $query->orWhere('locations.name', 'like', '%'.$search.'%'))

                // Start Date
                ->when($this->filters['search'], function($query, $search) {
                    try {
                        $dateTimeString = Carbon::parse($search);
                        $dateString = explode(' ', $dateTimeString)[0];
                        $timeString = explode(' ', $dateTimeString)[1];
                        $query->orWhere('loans.start_date_time', 'like', '%'.$dateString.'%');
                        $query->orWhere('loans.start_date_time', 'like', '%'.$timeString.'%');
                    } catch(\Throwable $e) {
                        // Search string is not a date
                    }
                })

                // End Date
                ->when($this->filters['search'], function($query, $search) {
                    try {
                        $dateTimeString = Carbon::parse($search);
                        $dateString = explode(' ', $dateTimeString)[0];
                        $timeString = explode(' ', $dateTimeString)[1];
                        $query->orWhere('loans.end_date_time', 'like', '%'.$dateString.'%');
                        $query->orWhere('loans.end_date_time', 'like', '%'.$timeString.'%');
                    } catch(\Throwable $e) {
                        // Search string is not a date
                    }
                })

                // Details
                ->when($this->filters['search'], fn($query, $search) =>
                      $query->orWhere('loans.details', 'like', '%'.$search.'%'));
            });

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