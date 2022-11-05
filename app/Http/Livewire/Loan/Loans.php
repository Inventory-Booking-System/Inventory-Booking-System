<?php

namespace App\Http\Livewire\Loan;

use Livewire\Component;
use App\Http\Livewire\DataTable\WithSorting;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Models\Loan;
use App\Models\User;
use App\Models\Asset;
use Carbon\Carbon;

class Loans extends Component
{
    use WithPerPagePagination, WithSorting, WithBulkActions;

    protected $paginationTheme = 'bootstrap';

    public $showFilters = false;

    public $filters = [
        'search' => '',
        'user_id' => null,
        'status_id' => null,
        'start_date_time' => null,
        'end_date_time' => null,
        'details' => null,
    ];

    public $counter = 0;
    public Loan $editing;
    public $equipment_id;

    public $shoppingCart = [];
    public $shoppingCost = 0;
    public $iteration = 0;
    public $avaliableEquipment = [];
    public $status_id = 1;

    protected $queryString = [];

    public function rules()
    {
        return [
            'editing.user_id' => 'required|integer',
            'editing.status_id' => 'required|string|in:0,1',
            'editing.start_date_time' => 'required|date|before:editing.end_date_time|nullable',
            'editing.end_date_time' => 'required|date|after:editing.start_date_time|nullable',
            'editing.details' => 'nullable|string',
            'equipment_id' => 'nullable|numeric|exists:assets,id',
        ];
    }

    public function showModal()
    {
        $this->emit('showModal');
    }

    public function mount()
    {
        $this->makeBlankLoan();
        $this->users = User::latest()->get();
    }

    public function updatedFilters($filed)
    {
        $this->resetPage();
    }

    public function makeBlankLoan()
    {
        $this->editing = Loan::make();
        $shoppingCart = [];
        $equipment_id = null;
    }

    public function deleteSelected()
    {
        $this->selectedRowsQuery->delete();

        $this->emit('hideModal', 'confirm');
    }

    public function exportSelected()
    {
        return response()->streamDownload(function() {
            echo $this->selectedRowsQuery->toCsv();
        }, 'loans.csv');
    }

    public function create()
    {
        if ($this->editing->getKey()){
            $this->makeBlankLoan();
        }

        $this->emit('showModal', 'edit');
    }

    public function edit(Loan $loan)
    {
        if($this->editing->isNot($loan)){
            $this->editing = $loan;
        }

        $this->emit('showModal', 'edit');
    }

    public function save()
    {
        $this->validate();

        $this->editing->start_date_time = carbon::parse($this->editing->start_date_time);
        $this->editing->end_date_time = carbon::parse($this->editing->end_date_time);

        $this->editing->save();

        $loan = Loan::find($this->editing->id);

        //Add equipment issues into equipment_issue_incidents
        $ids = [];
        //dd($this->shoppingCart);
        foreach($this->shoppingCart as $key => $item){
            array_push($ids, ['loan_id' => $this->editing->id, 'asset_id' => $key, 'returned' => $item['returned']]);
        }

        $loan->assets()->sync($ids);

        $this->emit('hideModal', 'edit');
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function getRowsQueryProperty()
    {
        $query = Loan::query()
            ->when($this->filters['user_id'], fn($query, $user_id) => $query->where('user_id', $user_id))
            ->when($this->filters['status_id'], fn($query, $status_id) => $query->where('status_id', $status_id))
            ->when($this->filters['start_date_time'], fn($query, $start_date_time) => $query->where('start_date_time', $start_date_time))
            ->when($this->filters['end_date_time'], fn($query, $end_date_time) => $query->where('end_date_time', $end_date_time))
            ->when($this->filters['details'], fn($query, $details) => $query->where('details', $details))
            ->when($this->filters['search'], fn($query, $search) => $query->where('details', 'like', '%'.$search.'%'));

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

        return view('livewire.loan.loans', [
            'loans' => $this->rows,
        ]);
    }

    /**
     * Fetches a list of equipment avaliable for the current selected input values.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookableEquipment()
    {
        $validatedDate =[
            'start_date_time' => carbon::parse($this->editing->start_date_time),
            'end_date_time' => carbon::parse($this->editing->end_date_time),
            'id' =>  $this->loanId ?? -1
        ];

        $this->avaliableEquipment = Asset::with('loans')
            ->where(function($query) use($validatedDate){
                $query->whereNotIn('assets.id', function($query) use($validatedDate){
                    $query->select('asset_loan.asset_id')
                            ->from('loans')
                            ->join('asset_loan','loans.id','asset_loan.loan_id')
                            ->whereRaw('`assets`.`id` = `asset_loan`.`asset_id`')
                            ->where('loans.id', '!=', $validatedDate['id'])
                            ->where(function($query2) use($validatedDate){
                                $query2->where('loans.start_date_time', '>=', $validatedDate['start_date_time'])
                                        ->where('loans.start_date_time', '<=', $validatedDate['end_date_time'])
                                        ->where('loans.id', '!=', $validatedDate['id']);
                            })->orWhere(function($query2) use($validatedDate){
                                $query2->where('loans.end_date_time', '>=', $validatedDate['start_date_time'])
                                    ->where('loans.end_date_time', '<=', $validatedDate['end_date_time'])
                                    ->where('loans.id', '!=', $validatedDate['id']);
                            })
                            ->where('asset_loan.returned','=',0);
                })
                ->orWhereDoesntHave('loans');
            })
            ->get();

        $this->emit('refresh');
    }

    public function updatedEquipmentId($id)
    {
        $item = Asset::find($id);

        $this->shoppingCart[$item->id] = [];
        $this->shoppingCart[$item->id]['title'] = $item->name;
        $this->shoppingCart[$item->id]['asset_id'] = $item->tag;
        $this->shoppingCart[$item->id]['returned'] = 0;

        //dd($this->shoppingCart);
    }

    public function removeItem($id)
    {
        if($this->shoppingCart[$id]['quantity'] == 1){
            unset($this->shoppingCart[$id]);
        }elseif($this->shoppingCart[$id]['quantity'] > 1){
            $this->shoppingCart[$id]['quantity'] -= 1;
        }
    }

    public function updatedEditingEndDateTime()
    {
        $this->getBookableEquipment();
        $this->iteration ++;
    }
}
