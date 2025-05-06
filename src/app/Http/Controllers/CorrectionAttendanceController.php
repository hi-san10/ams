<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use App\Models\StampCorrectionRequest;
use Carbon\CarbonImmutable;

class CorrectionAttendanceController extends Controller
{
    public function correction(Request $request)
    {
        $Attendance = Attendance::where('id', $request->id)->first();

        $correction = StampCorrectionRequest::create([
            'attendance_id' => $Attendance->id,
            'target_date' => $Attendance->date,
            'request_date' => CarbonImmutable::today(),
            'request_reason' => $request->remarks
        ]);

        $correction_attendance = CorrectionAttendance::create([
            'stamp_correction_request_id' => $correction->id,
            'start_time' => $request->start,
            'end_time' => $request->end,
        ]);

        if ($request->rest_start)
        {
            $rest_starts = $request->rest_start;
            foreach($rest_starts as $rest_start)
            {
                $rest = CorrectionRest::create([
                    'correction_attendance_id' => $correction_attendance->id,
                    'start_time' => $rest_start,
                ]);
            }

            $rest_ends = $request->rest_end;
            foreach($rest_ends as $rest_end)
            {
                $end_time = new CorrectionRest;
                $end_time = CorrectionRest::where('correction_attendance_id', $rest->correction_attendance_id)->whereNull('end_time')->first();
                $end_time->end_time = $rest_end;
                $end_time->save();
            }
        }

        if ($request->newRest_start)
        {
            CorrectionRest::create([
                'correction_attendance_id' => $correction_attendance->id,
                'start_time' => $request->newRest_start,
                'end_time' => $request->newRest_end
            ]);
        }

        return redirect()->route('attendance_list');
    }
}
