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
                            <button class="deleteAsset btn btn-danger btn-sm rounded-0" type="button" data-assetname="' . $asset->name . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button>';
                })
                ->make(true);
        }

        //Render rest of the page
        return view('asset.assets');
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //When mdifying an asset
        if($request->ajax()){
            $asset = Asset::find($id);
            return Response::json($asset);
        }

        //When displaying the seperate asset page
        return view('asset.assets');
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
        $data = $request->validate([
            'name' => 'required|string',
            'tag' => 'required|numeric|unique:assets,tag,'.$id,
            'description' => 'string',
            'cost' => 'required|numeric',
            'bookable' => 'boolean'
        ]);

        $asset = Asset::where('id', $id)->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'tag' => $request->input('tag'),
            'cost' => $request->input('cost'),
            'bookable' => $request->input('bookable')
        ]);

        return Response::json(Asset::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $asset = Asset::find($id);

        $asset->delete();

        return Response::json($asset);
    }
}
