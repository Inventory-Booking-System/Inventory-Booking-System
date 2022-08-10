<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\User;
use DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Populate data in table
        if($request->ajax()){
            $users = User::latest()->get();
            return Datatables::of($users)
                ->setRowId('id')
                ->addColumn('action', function ($user){
                    return '<button class="modifyUser btn btn-warning btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Modify" onclick="location.href=\'/users/' . $user->id . '/edit\';"><i class="fa fa-pen-to-square"></i></button>
                            <button class="archiveUser btn btn-danger btn-sm rounded-0" type="button" data-toggle="tooltip" data-placement="top" title="Archive"><i class="fa fa-box-archive"></i></button>';
                })
                ->make(true);
        }

        //Render rest of the page
        return view('user.users');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return view('user.show',[
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //Get list of users
        $user = User::find($id);

        //Render rest of the page
        return view('user.edit',[
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        $user = User::where('id', $id)->update([
            'forename' => $user->forename,
            'surname' => $user->surname,
            'email' => $user->email,
            'archived' => 1
        ]);

        return Response::json($user);
    }
}
