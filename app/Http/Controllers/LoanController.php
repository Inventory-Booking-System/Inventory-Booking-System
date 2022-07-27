<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Redirect;
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
            $loans = Loan::latest()->where('status_id', '<', '4')->with('assets')->get();

            //dd($loans);

            return Datatables::of($loans)
                ->setRowId('id')
                ->editColumn('start_date_time', function($loan){
                    return Carbon::parse($loan->start_date_time)->format('d F Y G:i');
                })
                ->editColumn('end_date_time', function($loan){
                    return Carbon::parse($loan->end_date_time)->format('d F Y G:i');
                })
                ->editColumn('status_id', function($loan){
                    switch($loan->status_id){
                        case 0:
                            return '<span class="badge bg-success">Booked</span>';
                            break;
                        case 1:
                            return '<span class="badge bg-warning text-dark">Reservation</span>';
                            break;
                        case 2:
                            return '<span class="badge bg-danger">Overdue</span>';
                            break;
                    }
                })
                ->rawColumns(['status_id','action'])
                ->addColumn('action', function ($loan){
                    if($loan->status_id == 1){
                        return '<button class="bookOutLoan btn btn-info btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Book out"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
                        <button class="modifyLoan btn btn-warning btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Modify"><i class="fa fa-pen-to-square"></i></button>
                        <button class="deleteLoan btn btn-danger btn-sm rounded-0" type="button" data-assetname="' . $loan->id . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button>';
                    }else{
                        return '<button class="completeLoan btn btn-success btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Complete"><i class="fa-solid fa-check"></i></button>
                        <button class="modifyLoan btn btn-warning btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Modify"><i class="fa fa-pen-to-square"></i></button>';
                    }
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
        //Get list of users
        $users = User::latest()->get();

        //Render rest of the page
        return view('loan.create',[
            'users' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Perform the first step of validation on the data. At this stage we are
        //just checking that data has arrived in the expected format for further
        //processing afterwards
        $validator = Validator::make($request->all(),[
            'user_id' => 'required|integer',
            'start_date' => 'required|date|before:end_date|nullable',
            'end_date' => 'required|date|after:start_date|nullable',
            'equipmentSelected' => 'required|json',
            'details' => 'nullable|string',
            'status_id' => 'string|in:0,1',
        ]);

        //We now need to verify the equipment the user has booked is actually bookable still.
        //This can fail for two reasons
        //The user has modified the array used to store equipment they are allowed to book client side. We should never trust this data.
        //Another user has booked the equipment after this user had opened the create loan form but had not selected submit yet.
        if(!($validator->errors()->has('equipmentSelected'))){
            //Blade php works much better with an arrays rather than JSON so lets convert
            $equipmentArr = json_decode($request->input('equipmentSelected'),true);
            //For some reason the JSON is returning length as part of the array so lets remove
            unset($equipmentArr['length']);

            $newEquipmentArr = [];


            //This is used to re-populate the dropdown of assets that are avaliable to book
            //We need to make sure the start and end dates have passed validation before
            //fetching this information from the database
            if(!($validator->errors()->has('start_date')) and !($validator->errors()->has('end_date'))){
                //This gives us all the equipment avaliable to be booked between the two dates
                $bookableEquipment = $this->getBookableEquipment($request)->getData();

                //We then need to mark the equipment the user has booked so we can add it back to the
                //shopping cart and not to the equipment dropdown menu
                foreach($equipmentArr as $id => $value){
                    $idFound = false;
                    foreach($bookableEquipment as &$equipment){
                        if($id == $equipment->id){
                            //We have found the ID is list of bookable equipment
                            $idFound = true;
                            $equipment->selected = true;
                        }
                    }

                    if($idFound == false){
                        //User has tried to book assets that are no longer avaliable
                    }
                }

                //Merge the bookable equipment back into the oldInput array which is passed back to the view
                //This is used to re-populate both the equipment dropdown menu and the shopping cart table
                $request->merge(['bookableEquipment' => $bookableEquipment]);

                //dd($bookableEquipment);
            }

        }

        //Return any errors during the validation
        if($validator->fails()){
            $test = redirect('loans/create')
                        ->withErrors($validator)
                        ->withInput();

            return $test;
            //dd($test);
        }

        //Retrieve the validated input
        $validated = $validator->validated();

        echo dd($validated);


        // $data = $request->validate([
        //     'user_id' => 'required|integer',
        //     'start_date' => 'required|date|before:end_date|nullable',
        //     'end_date' => 'required|date|after:start_date|nullable',
        //     'equipmentSelected' => 'required|json',
        //     'details' => 'nullable|string',
        //     'status_id' => 'string|in:1',
        // ]);



        // $loanId = Loan::create([
        //     'user_id' => $validator['user_id'],
        //     'status_id' => $validator['status_id'] ?? "0",
        //     'start_date_time' => carbon::parse($validator['start_date']),
        //     'end_date_time' => carbon::parse($validator['end_date']),
        //     'details' => $validator['details'] ?? "",
        // ])->id;

        // $loan = Loan::find($loanId);

        // $equipmentArr = json_decode($request->equipmentSelected,true);
        // unset($equipmentArr['length']);

        // //Add assets into asset_loan table
        // $loan->assets()->sync($equipmentArr);

        return redirect()->route('loans.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //When modifying an asset
        if($request->ajax()){
            $loan = Loan::with('assets')->find($id);

            return Response::json($loan);
        }

        //When displaying the seperate asset page
        return view('asset.assets');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Get list of users
        $users = User::latest()->get();

        //Render rest of the page
        return view('loan.edit',[
            'users' => $users
        ]);
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
        $data = $request->validate([
            'user_id' => 'required|integer',
            'status_id' => 'boolean',
            'start_date' => 'required_if:loanType,loanTypeMulti|date|before:end_date|nullable',
            'end_date' => 'required_if:loanType,loanTypeMulti|date|after:start_date|nullable',
            'equipmentSelected' => 'required|array',
            'details2' => 'nullable|string',
            'booked_in_equipment' => 'array',
        ]);

        Loan::where('id', $id)->update([
            'user_id' => $data['user_id'],
            'status_id' => $data['status_id'],
            'start_date_time' => carbon::parse($data['start_date']),
            'end_date_time' => carbon::parse($data['end_date']),
            'details' => $data['details'] ?? "",
        ]);

        $loan = Loan::find($id);

        //The sync function will remove any corrosponding data from the asset_loan table
        //when assets are removed from the booking
        $loan->assets()->sync($request->equipmentSelected);

        return Response::json($loan);
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
            'start_date' => 'required_if:loanType,loanTypeMulti|date|before:end_date|nullable',
            'end_date' => 'required_if:loanType,loanTypeMulti|date|after:start_date|nullable',
            'equipmentSelected' => 'required',
        ]);

        $ignoredIds = [];
        foreach ((array)$data['equipmentSelected'] as $key => $value) {
            array_push($ignoredIds, $key);
        }

        $validatedDate =[
            'start_date_time' => carbon::parse($data['start_date']),
            'end_date_time' => carbon::parse($data['end_date']),
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
                                                    })
                                                   ->where('asset_loan.returned','=',0);
                                        })
                                        ->orWhereDoesntHave('loans');
                                    })
                                    ->whereNotIn('assets.id', $ignoredIds)
                                    ->get());
    }

    /**
     * Mark booking as completed
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function completeBooking(Request $request, $id)
    {
        $loan = Loan::where('id', $request->id)->update([
            'status_id' => 4
        ]);

        return Response::json(Loan::find($id));
    }
}
