<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\DistributionGroup;

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
        return view('distribution-group.show',[
            'distributionGroup' => $id
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $data = [];
        $groups = DistributionGroup::latest()->get();
        foreach($users as $key => $user) {
            $data[] = [
                'id' => $user['id'],
                'name' => $user['name']
            ];
        }
        return $data;
    }
}
