<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
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
        $validator = Validator::make($request->all(), [
            'startDateTime' => 'required|integer|lt:endDateTime',
            'endDateTime' => 'required|integer|gt:startDateTime'
        ]);

        if ($validator->fails()) {
            return response($validator->errors(), 400);
        }

        $validated = $validator->validated();

        $validatedDate = [
            'start_date_time' => Carbon::createFromTimestamp($validated['startDateTime']),
            'end_date_time' => Carbon::createFromTimestamp($validated['endDateTime'])
        ];

        $assets = Asset::latest()->get();

        $startDateTime = $validatedDate['start_date_time'];
        $endDateTime = $validatedDate['end_date_time'];

        $nonAvailableAssets = DB::table('assets')
            ->join('asset_loan', 'assets.id', '=', 'asset_loan.asset_id')
            ->join('loans', 'asset_loan.loan_id', '=', 'loans.id')
            ->select('assets.id', 'assets.name')
            ->where('asset_loan.returned', 0)
            ->where('loans.status_id', '<>', 4) // Cancelled
            ->where('loans.status_id', '<>', 5) // Completed
            ->where(function ($query) use ($startDateTime, $endDateTime) {
                $query->where(function ($query) use ($startDateTime, $endDateTime) {
                    $query
                        ->whereBetween('loans.start_date_time', [$startDateTime, $endDateTime])
                        ->orWhereBetween('loans.end_date_time', [$startDateTime, $endDateTime])
                        /**
                         * We can't use a value as the first parameter in Laravel's 
                         * orWhereBetweenColumns(), so implement it using orWhere()
                         *
                         * OR :startDateTime BETWEEN loans.start_date_time AND loans.end_date_time
                         */
                        ->orWhere(function ($query) use ($startDateTime) {
                            $query
                                ->where('loans.start_date_time', '<=', $startDateTime)
                                ->where('loans.end_date_time', '>=', $startDateTime);
                        })
                        /**
                         * OR :endDateTime BETWEEN loans.start_date_time AND loans.end_date_time
                         */
                        ->orWhere(function ($query) use ($endDateTime) {
                            $query
                                ->where('loans.start_date_time', '<=', $endDateTime)
                                ->where('loans.end_date_time', '>=', $endDateTime);
                        });
                })
                ->orWhere(function ($query) {
                    /**
                     * If a reservation/setup end time is before the current 
                     * time, but it hasn't been completed/cancelled, the assets 
                     * should not be bookable
                     */
                    $query->where('loans.end_date_time', '<=', Carbon::now());
                });
            })
            ->get();

        foreach($assets as $key => $value){
            $assets[$key]['available'] = true;
        }

        // Mark non-available equipment in master equipment list
        foreach($nonAvailableAssets as $nonAvailableAsset){
            foreach($assets as $key => $asset){
                if($nonAvailableAsset->id === $asset['id']){
                    $assets[$key]['available'] = false;
                }
            }
        }

        return $assets;
    }
}
