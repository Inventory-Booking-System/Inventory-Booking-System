<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosController extends Controller
{
    /**
     * Point of sale system
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pos.pos');
    }
}
