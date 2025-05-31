<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use App\Models\Rest;
use App\Models\StampCorrectionRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $carbon = new CarbonImmutable;
        $date = $carbon->toDateString();

        $user = Attendance::where('user_id', Auth::id())->where('date', $date)->first();
        $rest = Rest::when($user, fn ($query) => $query->where('attendance_id', $user->id)->whereNull('end_time'))->exists();
        if ($user)
        {
            $workEnd = $user->end_time;
        }else{
            $workEnd = null;
        }

        return view('attendances.attendance', compact('carbon', 'user', 'workEnd', 'rest'));
    }

    public function start()
    {
        $carbon = new CarbonImmutable;

        Attendance::create([
            'user_id' => Auth::id(),
            'date' => $carbon,
            'start_time' => $carbon
        ]);

        return redirect('attendance');
    }

    public function end()
    {
        $carbon = new CarbonImmutable;

        Attendance::where('user_id', Auth::id())->whereDate('date', $carbon)
            ->update(['end_time' => $carbon]);

        return redirect('attendance');
    }

    public function list(Request $request)
    {
        $baseDate = $request->month;

        if (is_null($baseDate))
        {
            $carbon = CarbonImmutable::today();
        } else {
            $carbon = new CarbonImmutable($baseDate);
        }
        $previousMonth = $carbon->subMonthNoOverflow(1);
        $nextMonth = $carbon->addMonthNoOverflow(1);

        $attendances = Attendance::with('rests')->where('user_id', Auth::id())->whereYear('date', $carbon)->whereMonth('date', $carbon)
            ->orderBy('date', 'asc')->get();
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

        return view('attendances.list', compact('attendances', 'carbon', 'previousMonth', 'nextMonth'));
    }

    public function detail(Request $request)
    {
        $attendance = Attendance::with('user', 'rests')->where('id', $request->id)->first();

        $hasStampCorrectionRequest = StampCorrectionRequest::with('user')->where('attendance_id', $attendance->id)->first();
        if (is_null($hasStampCorrectionRequest))
        {
            $is_approval = false;
        }else{
            $is_approval = $hasStampCorrectionRequest->is_approval;
        }

        if ($hasStampCorrectionRequest && $is_approval == false && Auth::check())
        {
            $attendance = CorrectionAttendance::with('rests')->where('stamp_correction_request_id', $hasStampCorrectionRequest->id)->first();
        }
        return view('attendances.detail', compact('attendance', 'hasStampCorrectionRequest', 'is_approval'));



        $correctionAttendance = CorrectionAttendance::where('stamp_correction_request_id', $hasStampCorrectionRequest->id)->first();
        $correctionRests = CorrectionRest::where('correction_attendance_id', $correctionAttendance->id)->get();

        return view('attendances.detail', compact('attendance', 'hasStampCorrectionRequest', 'correctionAttendance', 'correctionRests', 'is_approval'));
    }
}
