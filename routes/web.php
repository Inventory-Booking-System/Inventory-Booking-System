<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Install\WelcomeController;
use App\Http\Controllers\Install\EnvironmentController;
use App\Http\Controllers\Install\RequirementsController;
use App\Http\Controllers\Install\PermissionsController;
use App\Http\Controllers\Install\FinalController;

use App\Http\Controllers\AssetController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\DistributionGroupController;
use App\Http\Controllers\EquipmentIssueController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppSettingsController;
use App\Http\Controllers\SignageController;
use App\Http\Controllers\PosController;

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
 * Installer
 */
Route::middleware(['guest', 'canInstall'])->prefix('install')->namespace('Install')->name('LaravelInstaller::')->group(function () {
    Route::get('/', [WelcomeController::class, 'welcome'])->name('welcome');
    Route::get('environment', [EnvironmentController::class, 'environmentMenu'])->name('environment');
    Route::get('environment/wizard', [EnvironmentController::class, 'environmentWizard'])->name('environmentWizard');
    Route::post('environment/saveWizard', [EnvironmentController::class, 'saveWizard'])->name('environmentSaveWizard');

    Route::get('requirements', [RequirementsController::class, 'requirements'])->name('requirements');
    Route::get('permissions', [PermissionsController::class, 'permissions'])->name('permissions');
    Route::get('final', [FinalController::class, 'finish'])->name('final');
});


/**
 * App Routes
 */
Route::middleware(['auth', 'checkpassword'])->group(function () {
    //Loans
    Route::resource('/', LoanController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    Route::resource('loans', LoanController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);
    Route::resource('setups', SetupController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);
    Route::resource('assets', AssetController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);
    Route::resource('locations', LocationController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);
    Route::resource('distributionGroups', DistributionGroupController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);
    Route::resource('equipmentIssues', EquipmentIssueController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);
    Route::resource('users', UserController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);
    Route::resource('incidents', IncidentController::class)->except(['store', 'update', 'destroy', 'edit', 'create']);

    Route::get('logout', [LogoutController::class, 'index'])->name('logout');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('settings', [AppSettingsController::class, 'index'])->name('settings');
    
    Route::get('pos', [PosController::class, 'index'])->name('pos');
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

/**
 * Route all remaining requests
 */
Route::get('signage', [SignageController::class, 'index'])->name('signage');
Route::middleware('canInstall')->any('{any}', [WelcomeController::class, 'welcome']);
