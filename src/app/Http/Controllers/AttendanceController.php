<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use App\Models\Rest;
use App\Models\StampCorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\CarbonImmutable;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

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
        if (is_null($baseDate)) {
            $carbon = CarbonImmutable::today();
        } else {
            $carbon = new CarbonImmutable($baseDate);
        }
        $previousMonth = $carbon->subMonthNoOverflow(1);
        $nextMonth = $carbon->addMonthNoOverflow(1);

        $monthlyAttendances = Attendance::with('rests')
            ->where('user_id', Auth::id())
            ->whereYear('date', $carbon)
            ->whereMonth('date', $carbon)
            ->orderBy('date', 'asc')
            ->get();
        $attendances = $this->attendanceService->calculate($monthlyAttendances);

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
