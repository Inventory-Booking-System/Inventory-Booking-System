<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignageController extends Controller
{
    /**
     * Display the digital signage
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('signage.signage');
    }
}
