<?php

namespace App\Http\Livewire\ShoppingCart;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Loan;

trait WithShoppingCart
{
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
}