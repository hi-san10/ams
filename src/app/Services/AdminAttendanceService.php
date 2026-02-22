<?php

namespace App\Services;

use App\Models\Attendance;

class AdminAttendanceService
{
    public function attendanceList($carbon)
    {
        $attendances = Attendance::with('user', 'rests')
            ->whereDate('date', $carbon)
            ->get();
        foreach ($attendances as $attendance) {
            $start = $attendance->start_time;
            $end = $attendance->end_time;
            $workingSeconds = $start->diffInSeconds($end);

            $rests = $attendance->rests;
            // $totalRestSeconds = 0;
            $totalRestSeconds = $rests->sum(function ($rest) {
                return $rest->start_time->diffInSeconds($rest->end_time);
            });
            // foreach ($rests as $rest) {
            //     $restStart = $rest->start_time;
            //     $restEnd = $rest->end_time;
            //     $diffRest = $restStart->diffInSeconds($restEnd);
            //     $totalRestSeconds = $totalRestSeconds + $diffRest;

            //     // $attendance->is_rest = $rest->end_time;
            // }

            $attendance->total_rest_time = gmdate('H:i:s', $totalRestSeconds);
            $attendance->total_work_time = gmdate('H:i:s', $workingSeconds - $totalRestSeconds);
        }

        return $attendances;
    }
}
