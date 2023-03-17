<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\Asset;
use Carbon\Carbon;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('asset.assets');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('asset.show',[
            'asset' => $id
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request)
    {
        $startDateTime = $request->query('startDateTime');
        $endDateTime = $request->query('endDateTime');
        $loanId = $request->query('loanId');

        $assets = Asset::latest()->get();

        $validatedDate =[
            'start_date_time' => Carbon::createFromTimestamp($startDateTime),
            'end_date_time' => Carbon::createFromTimestamp($endDateTime),
            'id' =>  $loanId ?? -1
        ];

        //Get equipment that is available for booking
        $availableAssets = Asset::where(function($query) use($validatedDate){
                $query->whereNotIn('assets.id', function($query) use($validatedDate){
                    $query->select('asset_loan.asset_id')
                        ->from('loans')
                        ->join('asset_loan','loans.id','asset_loan.loan_id')
                        ->whereRaw('`assets`.`id` = `asset_loan`.`asset_id`')
                        ->where('loans.id', '!=', $validatedDate['id'])
                        ->where(function($query2) use($validatedDate){
                            $query2->where('loans.start_date_time', '>=', $validatedDate['start_date_time'])
                                ->where('loans.start_date_time', '<=', $validatedDate['end_date_time'])
                                ->where('loans.id', '!=', $validatedDate['id']);
                        })
                        ->orWhere(function($query2) use($validatedDate){
                            $query2->where('loans.end_date_time', '>=', $validatedDate['start_date_time'])
                                ->where('loans.end_date_time', '<=', $validatedDate['end_date_time'])
                                ->where('loans.id', '!=', $validatedDate['id']);
                        })
                        ->where('asset_loan.returned','=',0);
                })
                ->orWhereDoesntHave('loans');
            })
            ->get(['id','name','tag'])->toArray();

        //Mark available equipment in master equipment list
        foreach($availableAssets as $equipment){
            foreach($assets as $key => $equipment2){
                if($equipment['id'] == $equipment2['id']){
                    $assets[$key]['available'] = true;
                }
            }
        }

        return $assets;
    }
}
