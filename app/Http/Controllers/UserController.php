<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Models\DistributionGroup;
use App\Models\Staff;
use App\Models\Student;
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
        return view('student.students');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('student.show', [
            'user' => $id,
        ]);
    }

    /**
     * Get all staff members for the POS staff screen.
     * Staff always appear regardless of group membership.
     *
     * @return array
     */
    public function getAll()
    {
        return Staff::orderBy('forename')
            ->orderBy('surname')
            ->get()
            ->map(fn ($staff) => [
                'id' => $staff->id,
                'forename' => $staff->forename,
                'surname' => $staff->surname,
            ]);
    }

    /**
     * Get all students that should appear on the POS student screen.
     * Access is managed by group membership.
     *
     * @return array
     */
    public function getUsersWithPosAccess()
    {
        return Student::with('bookingAuthoriser')
            ->whereHas('distributionGroups', function ($query) {
                $query->where('pos_student_screen_access', DistributionGroup::POS_ACCESS_ENABLED);
            })
            ->whereDoesntHave('distributionGroups', function ($query) {
                $query->where('pos_student_screen_access', DistributionGroup::POS_ACCESS_DISABLED);
            })
            ->orderBy('forename')
            ->orderBy('surname')
            ->distinct()
            ->get()
            ->map(fn ($student) => [
                'id' => $student->id,
                'forename' => $student->forename,
                'surname' => $student->surname,
                'booking_authoriser_user_id' => $student->booking_authoriser_user_id ?? $student->id,
            ]);
    }
}
