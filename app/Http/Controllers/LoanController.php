<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\Loan;
use App\Models\User;
use App\Models\Asset;
use DataTables;

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
            $loans = Loan::latest()->get();

            return Datatables::of($loans)
                ->setRowId('id')
                ->addColumn('action', function ($loan){
                    return '<button class="modifyAsset btn btn-warning btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Modify"><i class="fa fa-pen-to-square"></i></button>
                            <button class="deleteAsset btn btn-danger btn-sm rounded-0" type="button" data-assetname="' . $loan->id . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button>';
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
        //
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
        //
    }

    /**
     * Fetches a list of equipment avaliable for the current selected input values.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getBookableEquipment()
    {
        return Response::json(Asset::latest()->get());
        //Check for single day or multi day booking
            //Single day booking
                //Select from assets where
            //Multi day booking
    }
}
