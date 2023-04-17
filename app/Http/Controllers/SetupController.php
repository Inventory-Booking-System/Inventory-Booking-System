<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Response;
use App\Models\Loan;
use App\Models\Setup;
use Carbon\Carbon;

class SetupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Render rest of the page
        return view('setup.setups');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('setup.show',[
            'setup' => $id,
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'startDateTime' => 'required|integer|lt:endDateTime',
            'endDateTime' => 'required|integer|gt:startDateTime',
            'user' => 'required|integer',
            'location' => 'required|integer',
            'assets' => 'array',
            'assets.*.id' => 'required|integer',
            'assets.*.returned' => 'required|boolean',
            'details' => 'nullable|string',
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
        $loan->status_id = 3;
        $loan->created_by = $request->user()->id;
        $loan->push();
        $assets = [];
        foreach($validated['assets'] as $key => $asset) {
            $assets[$asset['id']] = ['returned' => $asset['returned']];
        }
        $loan->assets()->sync($assets);

        $setup = Setup::make()->setRelation('loan', $loan);
        $setup->loan_id = $loan->id;
        $setup->title = $validated['title'];
        $setup->location_id = $validated['location'];
        $setup->push();

        return $setup->toJSON();
    }

    public function put(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'startDateTime' => 'required|integer|lt:endDateTime',
            'endDateTime' => 'required|integer|gt:startDateTime',
            'user' => 'required|integer',
            'location' => 'required|integer',
            'assets' => 'array',
            'assets.*.id' => 'required|integer',
            'assets.*.returned' => 'required|boolean',
            'details' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $setup = Setup::find($id);
        $setup->loan->start_date_time = Carbon::createFromTimestamp($validated['startDateTime']);
        $setup->loan->end_date_time = Carbon::createFromTimestamp($validated['endDateTime']);
        $setup->loan->user_id = $validated['user'];
        $setup->loan->details = isset($validated['details']) ? $validated['details'] : null;
        $setup->loan->created_by = $request->user()->id;
        $setup->loan->push();
        $assets = [];
        foreach($validated['assets'] as $key => $asset) {
            $assets[$asset['id']] = ['returned' => $asset['returned']];
        }
        $setup->loan->assets()->sync($assets);

        $setup->title = $validated['title'];
        $setup->location_id = $validated['location'];
        $setup->push();

        return $setup->toJSON();
    }
}
