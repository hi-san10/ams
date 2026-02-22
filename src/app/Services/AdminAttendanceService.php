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
            $workingTime = $start->diffInSeconds($end);

            $rests = $attendance->rests;
            $number = 0;
            foreach ($rests as $rest) {
                $restStart = $rest->start_time;
                $restEnd = $rest->end_time;
                $diffRest = $restStart->diffInSeconds($restEnd);
                $number = $number + $diffRest;

                // $attendance->is_rest = $rest->end_time;
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }

        return $attendances;
    }
}
