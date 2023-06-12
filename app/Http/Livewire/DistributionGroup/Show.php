<?php

namespace App\Http\Livewire\DistributionGroup;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithDetailsPerPagePagination;
use App\Models\DistributionGroup;
use App\Models\Incident;
use App\Helpers\SQL;
use Carbon\Carbon;

class Show extends Component
{
    use WithDetailsPerPagePagination, WithSorting;

    public $distributionGroup;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'id' => null,
        'status_id' => null,
        'start_date_time' => null,
        'location_id' => null,
        'equipment_id' => null,
        'evidence' => null,
        'details' => null,
    ];

    protected $queryString = [];

    public function resetFilters()
    {
        $this->reset('filters');
    }

    private function searchById($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('incidents.id', 'like', '%'.str_replace('#', '', $search).'%');
        } else {            
            $query->where('incidents.id', 'like', '%'.str_replace('#', '', $search).'%');
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

    private function searchByLocation($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('locations.name', 'like', '%'.$search.'%');
        } else {
            $query->where('locations.name', 'like', '%'.$search.'%');
        }
    }

    private function searchByEquipmentIssues($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhereHas('issues', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT('x', equipment_issue_incident.quantity, ' ', equipment_issues.title)"), 'like', '%'.$search.'%');
            });
        } else {
            $query->whereHas('issues', function ($query) use ($search) {
                $query->where(DB::raw("CONCAT('x', equipment_issue_incident.quantity, ' ', equipment_issues.title)"), 'like', '%'.$search.'%');
            });
        }
    }

    private function searchByEvidence($query, $search, $orWhere = false) {
        $search = SQL::escapeLikeString($search);
        if ($orWhere) {
            $query->orWhere('incidents.evidence', 'like', '%'.$search.'%');
        } else {
            $query->where('incidents.evidence', 'like', '%'.$search.'%');
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

    public function getRowsQueryProperty()
    {
        $distributionGroup = $this->distributionGroup;

        $query = Incident::query()
            ->select('incidents.*')
            ->whereHas('group', function($query) use($distributionGroup){
                $query->where('distribution_id', '=', $distributionGroup->id);
            })
            ->join('locations', 'incidents.location_id', '=', 'locations.id') // Join locations table so we can search by location name
            ->when($this->filters['id'], fn($query, $search) => $this->searchById($query, $search))
            ->when($this->filters['status_id'], fn($query, $search) => $this->searchByStatus($query, $search))
            ->when($this->filters['start_date_time'], fn($query, $search) => $this->searchByStartDate($query, $search))
            ->when($this->filters['location_id'], fn($query, $search) => $this->searchByLocation($query, $search))
            ->when($this->filters['equipment_id'], fn($query, $search) => $this->searchByEquipmentIssues($query, $search))
            ->when($this->filters['evidence'], fn($query, $search) => $this->searchByEvidence($query, $search))
            ->when($this->filters['details'], fn($query, $search) => $this->searchByDetails($query, $search))
            ->when($this->filters['search'], fn($query, $search) => $query->where(function($query) use ($search) {
                $this->searchById($query, $search);
                $this->searchByStatus($query, $search, true);
                $this->searchByStartDate($query, $search, true);
                $this->searchByLocation($query, $search, true);
                $this->searchByEquipmentIssues($query, $search, true);
                $this->searchByEvidence($query, $search, true);
                $this->searchByDetails($query, $search, true);
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
        return view('livewire.distribution-group.show', [
            'incidents' => $this->rows,
        ]);
    }

    public function mount($distributionGroup)
    {
        $this->distributionGroup = DistributionGroup::find($distributionGroup);
    }
}
