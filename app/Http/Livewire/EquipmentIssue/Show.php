<?php

namespace App\Http\Livewire\EquipmentIssue;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Carbon\Carbon;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\EquipmentIssue;
use App\Models\Incident;
use App\Helpers\SQL;

class Show extends Component
{
    use WithPerPagePagination, WithSorting;

    public $equipmentIssue;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'id' => null,
        'user_id' => null,
        'status_id' => null,
        'start_date_time' => null,
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
            $query->orWhere('incidents.id', 'like', '%'.str_replace('#', '', $search).'%');
        } else {
            $query->where('incidents.id', 'like', '%'.str_replace('#', '', $search).'%');
        }
    }

    private function searchByDistributionGroup($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('distribution_groups.name', 'like', '%'.$search.'%');
        } else {
            $query->where('distribution_groups.name', 'like', '%'.$search.'%');
        }
    }

    private function searchByStatus($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        foreach (Incident::getStatusIds() as $id => $status) {
            if (str_contains(strtolower($status), strtolower($search))) {
                if ($orWhere) {
                    $query->orWhere('incidents.status_id', $id);
                } else {
                    $query->where('incidents.status_id', $id);
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
                $query->orWhere('incidents.start_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('incidents.start_date_time', 'like', '%'.$timeString.'%');
            } else {
                $query->where('incidents.start_date_time', 'like', '%'.$dateString.'%');
                $query->orWhere('incidents.start_date_time', 'like', '%'.$timeString.'%');
            }
        } catch(\Throwable $e) {
            // Search string is not a date
        }
    }

    private function searchByDetails($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('incidents.details', 'like', '%'.$search.'%');
        } else {
            $query->where('incidents.details', 'like', '%'.$search.'%');
        }
    }

    private function searchByEquipmentIssues($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhereHas('issues', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT('x', equipment_issue_incident.quantity, ' ', equipment_issues.title, ' (£', equipment_issues.cost, ')')"), 'like', '%'.$search.'%');
            });
        } else {
            $query->whereHas('issues', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT('x', equipment_issue_incident.quantity, ' ', equipment_issues.title, ' (£', equipment_issues.cost, ')')"), 'like', '%'.$search.'%');
            });
        }
    }

    public function getRowsQueryProperty()
    {
        $equipmentIssue = $this->equipmentIssue;

        $query = Incident::query()
            ->select('incidents.*')
            ->join('locations', 'incidents.location_id', '=', 'locations.id') // Join locations table so we can search by location name
            ->join('distribution_groups', 'incidents.distribution_id', '=', 'distribution_groups.id') // Join distribution_groups table so we can search by distribution group name
            ->whereHas('issues', function($query) use($equipmentIssue){
                $query->where('equipment_issue_id', '=', $equipmentIssue->id);
            })
            ->when($this->filters['id'], fn($query, $search) => $this->searchById($query, $search))
            ->when($this->filters['user_id'], fn($query, $search) => $this->searchByDistributionGroup($query, $search))
            ->when($this->filters['status_id'], fn($query, $search) => $this->searchByStatus($query, $search))
            ->when($this->filters['start_date_time'], fn($query, $search) => $this->searchByStartDate($query, $search))
            ->when($this->filters['details'], fn($query, $search) => $this->searchByDetails($query, $search))
            ->when($this->filters['assets'], fn($query, $search) => $this->searchByEquipmentIssues($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $query->where(function($query) use ($search) {
                $this->searchById($query, $search);
                $this->searchByDistributionGroup($query, $search, true);
                $this->searchByStatus($query, $search, true);
                $this->searchByStartDate($query, $search, true);
                $this->searchByDetails($query, $search, true);
                $this->searchByEquipmentIssues($query, $search, true);
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
        return view('livewire.equipment-issue.show', [
            'incidents' => $this->rows,
        ]);
    }

    public function mount($equipmentIssue)
    {
        $this->equipmentIssue = EquipmentIssue::find($equipmentIssue);
    }
}
