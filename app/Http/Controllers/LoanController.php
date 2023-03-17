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
        //Get list of users
        $users = User::latest()->get();

        //Render rest of the page
        return view('loan.loans',[
            'users' => $users
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('loan.show',[
            'loan' => $id,
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startDateTime' => 'required|integer|lt:endDateTime',
            'endDateTime' => 'required|integer|gt:startDateTime',
            'user' => 'required|integer',
            'details' => 'nullable|string',
            'reservation' => 'required|boolean',
            'assets' => 'required|array',
            'assets.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $loan = Loan::make();
        $loan->start_date_time = Carbon::createFromTimestamp($validated['startDateTime']);
        $loan->end_date_time = Carbon::createFromTimestamp($validated['endDateTime']);
        $loan->user_id = $validated['user'];
        $loan->details = isset($validated['details']) ? $validated['details'] : null;
        $loan->status_id = $validated['reservation'] ? 1 : 0;
        $loan->created_by = $request->user()->id;
        $loan->push();
        $loan->assets()->sync($validated['assets']);

        return $loan;
    }

    public function put(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'startDateTime' => 'required|integer|lt:endDateTime',
            'endDateTime' => 'required|integer|gt:startDateTime',
            'user' => 'required|integer',
            'details' => 'nullable|string',
            'reservation' => 'required|boolean',
            'assets' => 'required|array',
            'assets.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $loan = Loan::find($id);
        $loan->start_date_time = Carbon::createFromTimestamp($validated['startDateTime']);
        $loan->end_date_time = Carbon::createFromTimestamp($validated['endDateTime']);
        $loan->user_id = $validated['user'];
        $loan->details = isset($validated['details']) ? $validated['details'] : null;
        $loan->status_id = $validated['reservation'] ? 1 : 0;
        $loan->created_by = $request->user()->id;
        $loan->push();
        $loan->assets()->sync($validated['assets']);

        return $loan;
    }
}
