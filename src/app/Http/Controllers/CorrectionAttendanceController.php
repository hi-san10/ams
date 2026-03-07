<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use App\Models\StampCorrectionRequest;
use Carbon\CarbonImmutable;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CorrectionAttendanceController extends Controller
{
    public function correction(CorrectionRequest $request, Attendance $attendance)
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

        return redirect()->route('attendance_list');
    }

    public function list(Request $request)
    {
        $prm = $request->page;
        $approval = $prm === 'approved';

        $query = StampCorrectionRequest::with('user', 'attendance')
        ->where('is_approval', $approval);

        if (Auth::check()) {
            $correction_requests = $query
            ->where('user_id', Auth::id())
            ->get();

            return view('request_list', compact('correction_requests', 'prm'));

        } elseif (Auth::guard('admins')->check()) {
            $correction_requests = $query
            ->get();

            return view('admins.request_list', compact('correction_requests', 'prm'));
        }
    }
}
