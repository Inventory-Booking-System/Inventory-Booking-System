<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\SetupController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Loans
Route::get('loans/getBookableEquipment', [LoanController::class, 'getBookableEquipment']);
Route::patch('loans/completeBooking/{id}', [LoanController::class, 'completeBooking']);
Route::patch('loans/bookOutBooking/{id}', [LoanController::class, 'bookOutBooking']);
Route::resource('loans', LoanController::class);

//Setups
Route::resource('setups', SetupController::class)->except([
    'store', 'update'
]);

//Assets
Route::resource('assets', AssetController::class)->except([
    'store', 'update'
]);

//Accounts
Route::resource('users', UserController::class);

//Incidents
Route::resource('incidents', IncidentController::class)->except([
    'store', 'update'
]);