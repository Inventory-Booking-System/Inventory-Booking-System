<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!file_exists(storage_path('installed'))){
            return redirect()->route('LaravelInstaller::welcome');
        }

        return view('auth.login');
    }
}
