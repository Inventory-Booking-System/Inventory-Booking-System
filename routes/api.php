<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\Loan;
use App\Http\Controllers\SignageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/signage', function (Request $request) {
    return Loan::where(function($query){
            $query->whereDate('start_date_time', '=', Carbon::today())
                  ->whereIn('status_id', [0, 1, 2, 3]);
        })->orWhere(function($query){
            $query->whereDate('end_date_time', '=', Carbon::today())
                  ->whereIn('status_id', [0, 1, 2, 3]);
        })->orWhere(function($query){
            $query->orWhereDate('start_date_time', '<', Carbon::today())
                   ->where('status_id', '=', 2);
        })->orderBy('start_date_time', 'asc')->get();
});