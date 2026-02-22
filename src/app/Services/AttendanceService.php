<?php

namespace App\Services;

use App\Models\Attendance;

class AttendanceService
{
    public function attendanceList($authId, $carbon)
    {
        $attendances = Attendance::with('rests')
        ->where('user_id', $authId)
        ->whereYear('date', $carbon)
        ->whereMonth('date', $carbon)
        ->orderBy('date', 'asc')
        ->get();

        foreach ($attendances as $index => $attendance) {
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
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }

        return $attendances;
    }
}
