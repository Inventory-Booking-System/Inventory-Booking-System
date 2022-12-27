<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DistributionGroupController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LogoutController;

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

/**
 * App Routes
 */
Route::middleware(['auth', 'checkpassword'])->group(function () {
    //Loans
    Route::resource('/', LoanController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);
    Route::resource('loans', LoanController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    //Setups
    Route::resource('setups', SetupController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    //Assets
    Route::resource('assets', AssetController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    //Locations
    Route::resource('locations', LocationController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    //Locations
    Route::resource('distributionGroups', DistributionGroupController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    //Users
    Route::resource('users', UserController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    //Incidents
    Route::resource('incidents', IncidentController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    //Logout
    Route::get('logout', [LogoutController::class, 'index'])->name('logout');
});

/**
 * Authentication
 */
Route::middleware('guest')->group(function () {
    //Login
    Route::get('/', [LoginController::class, 'index']);
    Route::get('login', [LoginController::class, 'index'])->name('login');
});

Route::middleware('auth')->group(function () {
    //Register
    Route::get('register', [RegisterController::class, 'index'])->name('register');
});
