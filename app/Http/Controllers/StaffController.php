<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff as StaffModel;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        return view('staff.staff');
    }

    public function show($id)
    {
        return view('staff.show', [
            'user' => $id,
        ]);
    }
}
