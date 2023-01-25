<?php

namespace App\Http\Livewire\User;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\User;
use App\Models\Loan;
use App\Models\Setup;

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public $user;

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
        $user = $this->user;

        $query = Loan::query()
            ->select('loans.*')
            ->join('users', 'loans.user_id', '=', 'users.id') // Join users table so we can search by user name
            ->whereHas('user', function($query) use($user){
                $query->where('user_id', '=', $user->id);
            })
            //->when($this->filters['search'], fn($query, $search) => $query->where('forename', 'like', '%'.$search.'%'))
            ->where(function($query) { // Search
                // Loan ID
                $query->when($this->filters['search'], fn($query, $search) =>
                    // Handle searching by ID if the user has entered a leading #
                    $query->where('loans.id', 'like', '%'.str_replace('#', '', $search).'%'))

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
                    $query->orWhere('loans.details', 'like', '%'.$search.'%'))

                // Assets
                ->when($this->filters['search'], fn($query, $search) => 
                    $query->orWhereHas('assets', function ($query) use ($search) {
                        $query->where(DB::raw("CONCAT(name, ' ', '(', tag, ')')"), 'like', '%'.$search.'%');
                    }));
            });

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
        ]);
    }

    public function mount($user)
    {
        $this->user = User::find($user);
    }
}