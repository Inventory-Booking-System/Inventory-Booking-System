<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Validator;
use Redirect;
use Response;
use App\Models\Loan;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Mail\Loan\LoanOrder;
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
            'assets' => 'array',
            'assets.*.id' => 'required|integer',
            'assets.*.returned' => 'required|boolean',
            'groups' => 'array',
            'groups.*.id' => 'required|integer',
            'groups.*.quantity' => 'required|integer',
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

        $assets = [];
        foreach($validated['assets'] as $key => $asset) {
            $assets[$asset['id']] = ['returned' => $asset['returned']];
        }
        $loan->assets()->sync($assets);

        $groups = [];
        foreach($validated['groups'] as $key => $group) {
            $groups[$group['id']] = ['quantity' => $group['quantity']];
        }
        $loan->assetGroups()->sync($groups);

        $user = User::find($loan->user_id);
        if (Config::get('mail.cc.address')) {
            Mail::to($user->email)->cc(Config::get('mail.cc.address'))->queue(new LoanOrder($loan, true));
        } else {
            Mail::to($user->email)->queue(new LoanOrder($loan, true));
        }

        return $loan->toJSON();
    }

    public function put(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'startDateTime' => 'required|integer|lt:endDateTime',
            'endDateTime' => 'required|integer|gt:startDateTime',
            'user' => 'required|integer',
            'details' => 'nullable|string',
            'reservation' => 'required|boolean',
            'assets' => 'array',
            'assets.*.id' => 'required|integer',
            'assets.*.returned' => 'required|boolean',
            'groups' => 'array',
            'groups.*.id' => 'required|integer',
            'groups.*.quantity' => 'required|integer',
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
    
        $assets = [];
        foreach($validated['assets'] as $key => $asset) {
            $assets[$asset['id']] = ['returned' => $asset['returned']];
        }
        $loan->assets()->sync($assets);

        $groups = [];
        foreach($validated['groups'] as $key => $group) {
            $groups[$group['id']] = ['quantity' => $group['quantity']];
        }
        $loan->assetGroups()->sync($groups);

        $user = User::find($loan->user_id);
        if (Config::get('mail.cc.address')) {
            Mail::to($user->email)->cc(Config::get('mail.cc.address'))->queue(new LoanOrder($loan, false));
        } else {
            Mail::to($user->email)->queue(new LoanOrder($loan, false));
        }

        return $loan->toJSON();
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        return $loans = Loan::query()
            ->whereNotIn('status_id', [4, 5])  // Not cancelled or completed
            ->get();
    }
}
