<?php

namespace App\Http\Livewire\Asset;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Loan;

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public $asset;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'name' => null,
        'tag' => null,
        'description' => null,
    ];

    protected $queryString = [];

    public $expandedCells = [];

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function expandCell($cellId)
    {
        array_push($this->expandedCells, $cellId);
        array_unique($this->expandedCells);
    }

    public function collapseCell($cellId)
    {
        if (($key = array_search($cellId, $this->expandedCells)) !== false) {
            unset($this->expandedCells[$key]);
        }
    }

    public function getRowsQueryProperty()
    {
        $asset = $this->asset;

        $query = Loan::query()
            ->select('loans.*')
            ->join('users', 'loans.user_id', '=', 'users.id') // Join users table so we can search by user name
            ->whereHas('assets', function($query) use($asset){
                $query->where('tag', '=', $asset->tag);
            })
            ->where(function($query) { // Search
                // Loan ID
                $query->when($this->filters['search'], fn($query, $search) =>
                    // Handle searching by ID if the user has entered a leading #
                    $query->orWhere('loans.id', 'like', '%'.str_replace('#', '', $search).'%'))

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
        return view('livewire.asset.show', [
            'loans' => $this->rows,
        ]);
    }

    public function mount($asset)
    {
        $this->asset = Asset::find($asset);
    }
}
