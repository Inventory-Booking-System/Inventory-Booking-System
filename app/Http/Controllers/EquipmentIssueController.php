<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\EquipmentIssue;

class EquipmentIssueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('equipment-issue.equipment-issues');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('equipment-issue.show',[
            'equipmentIssue' => $id
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $data = [];
        $issues = EquipmentIssue::latest()->get();
        foreach($issues as $key => $user) {
            $data[] = [
                'id' => $user['id'],
                'name' => $user['title'],
                'cost' => $user['cost']
            ];
        }
        return $data;
    }
}
