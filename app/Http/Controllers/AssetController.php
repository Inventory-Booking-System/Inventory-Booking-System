<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Validator;
use Response;
use App\Models\Asset;
use App\Models\Loan;
use App\Models\User;
use App\Mail\Loan\LoanOrder;
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
        $assetGroups = DB::table('asset_groups')->get();

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
        foreach ($nonAvailableAssets as $nonAvailableAsset) {
            foreach ($assets as $key => $asset) {
                if ($nonAvailableAsset->id === $asset['id']) {
                    $assets[$key]['available'] = false;
                }
            }
        }        

        $availableAssetCounts = [];
        // Count the available assets for each group
        foreach ($assets as $asset) {
            if ($asset->available) {
                $groupId = $asset->asset_group_id;
                if (!isset($availableAssetCounts[$groupId])) {
                    $availableAssetCounts[$groupId] = 0;
                }
                $availableAssetCounts[$groupId]++;
            }
        }

        // Update the $assetGroups array with the available asset counts
        foreach ($assetGroups as &$group) {
            $groupId = $group->id;
            $group->available_assets_count = $availableAssetCounts[$groupId] ?? 0;
        }

        return [
            'groups' => $assetGroups,
            'assets' => $assets
        ];
    }

    /**
     * Book in the asset from any existing loans or setups. Whitespace is trimmed
     * and leading 0s are removed.
     * @return \Illuminate\Http\Response
     */
    public function scanIn(Request $request, $id)
    {
        $loans = Loan::query()
            ->whereHas('assets', function($query) use($id) {
                $query->where('tag', '=', $id);
            })
            /**
             * Only 'booked' or 'overdue'. We can't easily do setups as we don't
             * know which setup is being completed.
             */
            ->whereIn('status_id', [0, 2])
            ->get();

        if ($loans->isEmpty()) {
            return response()->json([
                'error' => 'NO_OPEN_LOANS',
                'description' => 'There are no \'booked\' or \'overdue\' loans to scan in for asset.'
            ], 400);
        }

        foreach ($loans as $loan) {
            $assets = $loan->assets()->where('tag', '=', $id)->get();
            foreach ($assets as $asset) {
                $loan->assets()->updateExistingPivot($asset->id, ['returned' => 1]);
            }

            // Check if all assets for this loan are returned
            $totalAssets = $loan->assets->count();
            $returnedAssets = $loan->assets()->wherePivot('returned', 1)->count();

            if ($totalAssets === $returnedAssets) {
                // All assets are returned, update loan status
                $loan->status_id = 5;
                $loan->save();
            }

            $user = User::find($loan->user_id);
            if (Config::get('mail.cc.address')) {
                Mail::to($user->email)->cc(Config::get('mail.cc.address'))->queue(new LoanOrder($loan, false));
            } else {
                Mail::to($user->email)->queue(new LoanOrder($loan, false));
            }
        }

        return response()->json($loans, 200);
    }
}
