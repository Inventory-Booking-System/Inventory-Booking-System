<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LoanController;

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
Route::resource('loans', LoanController::class);

//Assets
Route::resource('/assets', AssetController::class);