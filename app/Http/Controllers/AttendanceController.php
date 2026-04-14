<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Activity;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function store(StoreAttendanceRequest $request)
    {
        $activity = Activity::findOrFail((int) $request->integer('activity_id'));

        $attendance = Attendance::updateOrCreate(
            [
                'activity_id' => $activity->id,
                'user_id' => $request->user()->id,
            ],
            [
                'status' => $request->input('status', 'present'),
                'checked_in_at' => now(),
            ]
        );

        return response()->json($attendance, 201);
    }
}
