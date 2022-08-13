<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\DistributionGroup;
use App\Models\EquipmentIssue;
use App\Models\Incident;

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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Get list of users
        $locations = Location::latest()->get();
        $distributions = DistributionGroup::latest()->get();
        $equipmentIssues = EquipmentIssue::latest()->get();

        //Render rest of the page
        return view('incident.create',[
            'locations' => $locations,
            'distributions' => $distributions,
            'equipmentIssues' => $equipmentIssues
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
        $incident = Incident::where('id', $id)->with('issues')->first();

        return view('incident.show',[
            'incident' => $incident
        ]);
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
        $locations = Location::latest()->get();
        $distributions = DistributionGroup::latest()->get();
        $equipmentIssues = EquipmentIssue::latest()->get();

        $incident = Incident::where('id', $id)->with('issues')->first();

        return view('incident.edit',[
            'locations' => $locations,
            'distributions' => $distributions,
            'equipmentIssues' => $equipmentIssues,
            'incident' => $incident,
        ]);
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
}
