<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\DistributionGroup;
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
     * Get all users that should appear on the POS staff screen.
     * 
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $users = User::query()
            ->whereNull('booking_authoriser_user_id')
            ->whereHas('distributionGroups', function ($query) {
                $query->where('pos_staff_screen_access', DistributionGroup::POS_ACCESS_ENABLED);
            })
            ->whereDoesntHave('distributionGroups', function ($query) {
                $query->where('pos_staff_screen_access', DistributionGroup::POS_ACCESS_DISABLED);
            })
            ->orderBy('forename')
            ->orderBy('surname')
            ->distinct()
            ->get();

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
     * Get all users that should appear on the POS student screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsersWithPosAccess()
    {
        $users = User::query()
            ->with('bookingAuthoriser')
            ->whereHas('distributionGroups', function ($query) {
                $query->where('pos_student_screen_access', DistributionGroup::POS_ACCESS_ENABLED);
            })
            ->whereDoesntHave('distributionGroups', function ($query) {
                $query->where('pos_student_screen_access', DistributionGroup::POS_ACCESS_DISABLED);
            })
            ->orderBy('forename')
            ->orderBy('surname')
            ->distinct()
            ->get();

        $data = [];
        foreach($users as $user) {
            $authoriser = $user->bookingAuthoriser ?? $user;
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
