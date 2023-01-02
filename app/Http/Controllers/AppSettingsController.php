<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppSettingsController extends Controller
{
    /**
     * Displays the application settings
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app-settings.app-settings');
    }
}
