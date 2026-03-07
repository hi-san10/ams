<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use App\Models\CorrectionAttendance;
use App\Models\StampCorrectionRequest;

class CorrectionService
{
    public function correction($request, $attendance)
    {
        DB::transaction(function () use(
            $request,
            $attendance,
        ) {
            $correction = StampCorrectionRequest::create([
                'user_id' => Auth::id(),
                'attendance_id' => $attendance->id,
                'target_date' => $attendance->date,
                'request_date' => CarbonImmutable::today(),
                'request_reason' => $request->remarks
                ]);

            $correction_attendance = CorrectionAttendance::create([
                'stamp_correction_request_id' => $correction->id,
                'start_time' => $request->start,
                'end_time' => $request->end,
            ]);

            $rests = [];
            foreach ($request->rests as $rest) {
                if (blank($rest['start_time']) || blank($rest['end_time'])) {
                    continue;
                }
                $rests[] = [
                    'start_time' => $rest['start_time'],
                    'end_time' => $rest['end_time'],
                ];
            }
            $correction_attendance->rests()->createMany($rests);
        });

    }
}
