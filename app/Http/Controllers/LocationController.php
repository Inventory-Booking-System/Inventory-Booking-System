<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\Location;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('location.locations');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('location.show',[
            'location' => $id
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        return Location::latest()->get()->toJSON();
    }
}
