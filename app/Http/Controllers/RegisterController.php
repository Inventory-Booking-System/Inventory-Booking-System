<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Display the register form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.register');
    }
}
