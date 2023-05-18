<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Validator;
use Redirect;
use Response;
use App\Models\Location;
use App\Models\DistributionGroup;
use App\Models\EquipmentIssue;
use App\Models\Incident;
use App\Mail\Incident\IncidentOrder;
use Carbon\Carbon;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Render rest of the page
        return view('incident.incidents');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('incident.show',[
            'incident' => $id
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'startDateTime' => 'required|integer',
            'distributionGroup' => 'required|integer',
            'location' => 'required|integer',
            'equipmentIssues' => 'required|array',
            'equipmentIssues.*.id' => 'required|integer',
            'equipmentIssues.*.quantity' => 'required|integer',
            'evidence' => 'required|string',
            'details' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $incident = Incident::make();
        $incident->start_date_time = Carbon::createFromTimestamp($validated['startDateTime']);
        $incident->distribution_id = $validated['distributionGroup'];
        $incident->location_id = $validated['location'];
        $incident->evidence = isset($validated['evidence']) ? $validated['evidence'] : null;
        $incident->details = isset($validated['details']) ? $validated['details'] : null;
        $incident->status_id = 0;
        $incident->created_by = $request->user()->id;
        $incident->push();
        $equipmentIssues = [];
        foreach ($validated['equipmentIssues'] as $key => $item) {
            $equipmentIssues[$item['id']] = ['quantity' => $item['quantity']];
        }
        $incident->issues()->sync($equipmentIssues);

        $equipmentIssues = $incident->issues()->get()->toArray();
        $cost = 0;
        foreach ($equipmentIssues as $key => $equipment) {
            $cost += ($equipment['cost'] * $equipment['pivot']['quantity']);
        }
        
        $users = $incident->group->users->pluck('email');
        if (Config::get('mail.cc.address')) {
            Mail::to($users)->cc(Config::get('mail.cc.address'))->queue(new IncidentOrder($incident, true, $cost));
        } else {
            Mail::to($users)->queue(new IncidentOrder($incident, true, $cost));
        }

        return $incident->toJSON();
    }

    public function put(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'startDateTime' => 'required|integer',
            'distributionGroup' => 'required|integer',
            'location' => 'required|integer',
            'equipmentIssues' => 'required|array',
            'equipmentIssues.*.id' => 'required|integer',
            'equipmentIssues.*.quantity' => 'required|integer',
            'evidence' => 'required|string',
            'details' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $incident = Incident::find($id);
        $incident->start_date_time = Carbon::createFromTimestamp($validated['startDateTime']);
        $incident->distribution_id = $validated['distributionGroup'];
        $incident->location_id = $validated['location'];
        $incident->evidence = isset($validated['evidence']) ? $validated['evidence'] : null;
        $incident->details = isset($validated['details']) ? $validated['details'] : null;
        $incident->status_id = 0;
        $incident->created_by = $request->user()->id;
        $incident->push();
        $equipmentIssues = [];
        foreach ($validated['equipmentIssues'] as $key => $item) {
            $equipmentIssues[$item['id']] = ['quantity' => $item['quantity']];
        }
        $incident->issues()->sync($equipmentIssues);

        $equipmentIssues = $incident->issues()->get()->toArray();
        $cost = 0;
        foreach ($equipmentIssues as $key => $equipment) {
            $cost += ($equipment['cost'] * $equipment['pivot']['quantity']);
        }
        
        $users = $incident->group->users->pluck('email');
        if (Config::get('mail.cc.address')) {
            Mail::to($users)->cc(Config::get('mail.cc.address'))->queue(new IncidentOrder($incident, false, $cost));
        } else {
            Mail::to($users)->queue(new IncidentOrder($incident, false, $cost));
        }

        return $incident->toJSON();
    }
}
