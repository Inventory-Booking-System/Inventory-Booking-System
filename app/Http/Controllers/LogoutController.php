<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Logs the user out
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
