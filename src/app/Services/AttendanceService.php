<?php

namespace App\Services;

class AttendanceService
{
    public function calculate($attendances)
    {
        foreach ($attendances as $attendance) {
            $workingTime = $attendance->start_time->diffInSeconds($attendance->end_time);

            $rests = $attendance->rests;
            $totalRestSeconds = $rests->sum(function ($rest) {
                return $rest->start_time->diffInSeconds($rest->end_time);
            });

            $attendance->total_rest_time = gmdate('H:i:s', $totalRestSeconds);
            $attendance->total_work_time = gmdate('H:i:s', $workingTime - $totalRestSeconds);
        }

        return $attendances;
    }
}
