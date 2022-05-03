<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\Loan;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetLoan;
use DataTables;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Populate data in table
        if($request->ajax()){
            $loans = Loan::latest()->with('assets')->get();

            return Datatables::of($loans)
                ->setRowId('id')
                ->addColumn('action', function ($loan){
                    return '<button class="modifyLoan btn btn-warning btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Modify"><i class="fa fa-pen-to-square"></i></button>
                            <button class="deleteLoan btn btn-danger btn-sm rounded-0" type="button" data-assetname="' . $loan->id . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button>';
                })
                ->make(true);
        }

        //Get list of users
        $users = User::latest()->get();

        //Render rest of the page
        return view('loan.loans',[
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer',
            'status_id' => 'boolean',
            'loanType' => 'required|string|in:loanTypeSingle,loanTypeMulti',
            'loanDate' => 'required_if:loanType,loanTypeSingle|date|nullable',
            'start_time' => 'required_if:loanType,loanTypeSingle|date_format:H:i|nullable',
            'end_time' => 'required_if:loanType,loanTypeSingle|date_format:H:i|nullable',
            'start_date' => 'required_if:loanType,loanTypeMulti|date|before:end_date|nullable',
            'end_date' => 'required_if:loanType,loanTypeMulti|date|after:start_date|nullable',
            'equipmentSelected' => 'required|array',
            'details' => 'nullable|string',
        ]);

        $loanId = Loan::create([
            'user_id' => $data['user_id'],
            'status_id' => $data['status_id'],
            'start_date_time' => carbon::parse(($data['start_date'] ?? $data['loanDate']) . ($data['start_time'] ?? "09:00:00")),
            'end_date_time' => carbon::parse(($data['end_date'] ?? $data['loanDate']) . ($data['end_time'] ?? "15:30:00")),
            'details' => $data['details'] ?? "",
        ])->id;

        $loan = Loan::find($loanId);

        $loan->assets()->sync($request->equipmentSelected);

        return Response::json($loan);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $loan = Loan::find($id);

        $loan->delete();

        return Response::json($loan);
    }

    /**
     * Fetches a list of equipment avaliable for the current selected input values.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookableEquipment(Request $request)
    {
        $data = $request->validate([
            'loanType' => 'required|string|in:loanTypeSingle,loanTypeMulti',
            'loanDate' => 'required_if:loanType,loanTypeSingle|date|nullable',
            'start_time' => 'required_if:loanType,loanTypeSingle|date_format:H:i|nullable',
            'end_time' => 'required_if:loanType,loanTypeSingle|date_format:H:i|nullable',
            'start_date' => 'required_if:loanType,loanTypeMulti|date|before:end_date|nullable',
            'end_date' => 'required_if:loanType,loanTypeMulti|date|after:start_date|nullable',
        ]);

        $validatedDate =[
            'start_date' => $data['start_date'] ?? $data['loanDate'],
            'end_date' => $data['end_date'] ?? $data['loanDate'],
            'start_time' => carbon::parse($data['start_time'] ?? "09:00:00")->format('H:i'),
            'end_time' => carbon::parse($data['end_time'] ?? "15:30:00")->format('H:i'),
            'start_date_time' => carbon::parse(($data['start_date'] ?? $data['loanDate']) . ($data['start_time'] ?? "09:00:00")),
            'end_date_time' => carbon::parse(($data['end_date'] ?? $data['loanDate']) . ($data['end_time'] ?? "15:30:00")),
        ];

        return Response::json(Asset::with('loans')
                                    ->where('bookable',true)
                                    ->where(function($query) use($validatedDate){
                                        $query->whereNotIn('assets.id', function($query) use($validatedDate){
                                            $query->select('asset_loan.asset_id')
                                                  ->from('loans')
                                                  ->join('asset_loan','loans.id','asset_loan.loan_id')
                                                  ->whereRaw('`assets`.`id` = `asset_loan`.`asset_id`')
                                                  ->where(function($query2) use($validatedDate){
                                                        $query2->where('loans.start_date_time', '>=', $validatedDate['start_date_time'])
                                                                ->where('loans.start_date_time', '<=', $validatedDate['end_date_time']);
                                                    })->orWhere(function($query2) use($validatedDate){
                                                        $query2->where('loans.end_date_time', '>=', $validatedDate['start_date_time'])
                                                            ->where('loans.end_date_time', '<=', $validatedDate['end_date_time']);
                                                    });
                                        })
                                        ->orWhereDoesntHave('loans');
                                    })
                                    ->get());
    }
}
