<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DistributionGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('distribution-group.distribution-groups');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $distributionGroup = DistributionGroup::find($id);

        return view('distribution-group.show',[
            'distributionGroup' => $distributionGroup
        ]);
    }
}
