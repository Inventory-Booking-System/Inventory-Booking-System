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
                    return '<button class="modifyAsset btn btn-warning btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Modify" onclick="location.href=\'/assets/' . $asset->id . '/edit\';"><i class="fa fa-pen-to-square"></i></button>
                            <button class="deleteAsset btn btn-danger btn-sm rounded-0" type="button" data-assetname="' . $asset->name . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-can"></i></button>';
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
        return view('asset.create');
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
        $asset = Asset::find($id);

        return view('asset.show',[
            'asset' => $asset
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
        $asset = Asset::find($id);

        //Render rest of the page
        return view('asset.edit',[
            'asset' => $asset
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
        $asset = Asset::find($id);

        $asset->delete();

        return Response::json($asset);
    }
}
