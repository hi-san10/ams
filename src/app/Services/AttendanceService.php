<?php

namespace App\Services;

use Carbon\CarbonImmutable;
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
            $start = new CarbonImmutable($attendance->start_time);
            $end = new CarbonImmutable($attendance->end_time);
            $workingTime = $start->diffInSeconds($end);

            $rests = $attendance->rests;
            $number = 0;
            foreach ($rests as $rest) {
                $restStart = new CarbonImmutable($rest->start_time);
                $restEnd = new CarbonImmutable($rest->end_time);
                $diffRest = $restStart->diffInSeconds($restEnd);
                $number = $number + $diffRest;
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }

        return $attendances;
    }
}
