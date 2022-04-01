<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\Asset;
use DataTables;

class AssetController extends Controller
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
            $assets = Asset::latest()->get();
            return Datatables::of($assets)
                ->setRowId('id')
                ->addColumn('action', function ($asset){
                    return '<button class="modifyAsset btn btn-warning btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Modify"><i class="fa fa-pen-to-square"></i></button>
                            <button class="deleteAsset btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button>';
                })
                ->make(true);
        }

        //Render rest of the page
        return view('asset.assets');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //https://www.positronx.io/laravel-ajax-example-tutorial/

        $data = $request->validate([
            'name' => 'required|string',
            'tag' => 'required|numeric|unique:assets',
            'description' => 'string',
            'cost' => 'required|numeric',
            'bookable' => 'boolean'
        ]);

        $asset = Asset::create($data);

        return Response::json($asset);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asset = Asset::find($id)->delete($id);

        return Response::json($asset);
    }
}
