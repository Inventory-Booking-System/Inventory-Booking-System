<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Render rest of the page
        return view('user.users');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('user.show',[
            'user' => $id
        ]);
    }

    /**
     * Get all users that don't have a POS booking authoriser.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $users = User::whereNull('booking_authoriser_user_id')->get();

        $data = [];
        foreach($users as $key => $user) {
            $data[] = [
                'id' => $user['id'],
                'forename' => $user['forename'],
                'surname' => $user['surname']
            ];
        }
        return $data;
    }

    /**
     * Get all users where pos_access is 1.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsersWithPosAccess()
    {
        $users = User::where('pos_access', 1)->get();

        $data = [];
        foreach($users as $user) {
            $data[] = [
                'id' => $user['id'],
                'forename' => $user['forename'],
                'surname' => $user['surname'],
                'booking_authoriser_user_id' => $user['booking_authoriser_user_id'] ?? $user['id']
            ];
        }

        return $data;
    }
}
