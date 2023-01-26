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
        $equipmentIssue = $this->equipmentIssue;

        $query = Incident::query()
            ->select('incidents.*')
            ->join('locations', 'incidents.location_id', '=', 'locations.id') // Join locations table so we can search by location name
            ->join('distribution_groups', 'incidents.distribution_id', '=', 'distribution_groups.id') // Join distribution_groups table so we can search by distribution group name
            ->whereHas('issues', function($query) use($equipmentIssue){
                $query->where('equipment_issue_id', '=', $equipmentIssue->id);
            })
            ->when($this->filters['search'], fn($query, $search) =>
                $query->where(function($query) use ($search) { // Search
                    $search = SQL::escapeLikeString($search);

                    // Incident ID
                    // Handle searching by ID if the user has entered a leading #
                    $query->where('incidents.id', 'like', '%'.str_replace('#', '', $search).'%');

                    // Distribution group
                    $query->orWhere('distribution_groups.name', 'like', '%'.$search.'%');

                    // Status
                    foreach (Incident::getStatusIds() as $id => $status) {
                        if (str_contains(strtolower($status), strtolower($search))) {
                            $query->orWhere('incidents.status_id', $id);
                        }
                    }

                    // Start Date
                    try {
                        $dateTimeString = Carbon::parse($search);
                        $dateString = explode(' ', $dateTimeString)[0];
                        $timeString = explode(' ', $dateTimeString)[1];
                        $query->orWhere('incidents.start_date_time', 'like', '%'.$dateString.'%');
                        $query->orWhere('incidents.start_date_time', 'like', '%'.$timeString.'%');
                    } catch(\Throwable $e) {
                        // Search string is not a date
                    }

                    // Details
                    $query->orWhere('incidents.details', 'like', '%'.$search.'%');

                    // Equipment issues
                    $query->orWhereHas('issues', function ($query) use ($search) {
                        $query->where(DB::raw("CONCAT('x', equipment_issue_incident.quantity, ' ', equipment_issues.title, ' (£', equipment_issues.cost, ')')"), 'like', '%'.$search.'%');
                    });
                })
            );

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
