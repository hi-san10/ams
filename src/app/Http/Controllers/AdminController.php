<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\StampCorrectionRequest;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use App\Http\Requests\LoginRequest;
use PhpParser\Node\Stmt\Foreach_;

class AdminController extends Controller
{
    public function getLogin(Request $request)
    {
        return view('admins.admin_login');
    }

    public function postLogin(LoginRequest $request)
    {
        $admin = AdminUser::where('email', $request->email)->first();

        if (is_null($admin) or $admin && !Hash::check($request->password, $admin->password))
        {
            return back()->with('message', 'ログイン情報が登録されていません');
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        Auth::guard('admins')->attempt($credentials);
        $request->session()->regenerate();

        return redirect('/admin/attendance/list');
    }

    public function list(Request $request)
    {
        $baseDate = $request->day;

        if (is_null($baseDate)) {
            $carbon = CarbonImmutable::today();
        } else {
            $carbon = new CarbonImmutable($baseDate);
        }

        $previousDay = new CarbonImmutable($carbon->subDay(1));
        $nextDay = new CarbonImmutable($carbon->addDay(1));

        $attendances = Attendance::with('user', 'rests')->whereDate('date', $carbon)->get();
        foreach ($attendances as $attendance)
        {
            $start = new CarbonImmutable($attendance->start_time);
            $end = new CarbonImmutable($attendance->end_time);
            $workingTime = $start->diffInSeconds($end);

            $rests = $attendance->rests;
            $number = 0;
            foreach ($rests as $rest)
            {
                $restStart = new CarbonImmutable($rest->start_time);
                $restEnd = new CarbonImmutable($rest->end_time);
                $diffRest = $restStart->diffInSeconds($restEnd);
                $number = $number + $diffRest;

                $attendance->is_rest = $rest->end_time;
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);

        }

        return view('admins.attendance_list', compact('carbon', 'attendances', 'previousDay', 'nextDay'));
    }

    public function staff_list()
    {
        $users = User::select('id', 'name', 'email')->get();

        return view('admins.staff_list', compact('users'));
    }

    public function staff_attendance_list(Request $request)
    {
        $baseDate = $request->month;
        $user = User::where('id', $request->id)->select('id', 'name')->first();

        if (is_null($baseDate)) {
            $carbon = CarbonImmutable::today();
        } else {
            $carbon = new CarbonImmutable($baseDate);
        }
        $previousMonth = $carbon->subMonth(1);
        $nextMonth = $carbon->addMonth(1);

        $attendances = Attendance::with('rests')->where('user_id', $user->id)->whereYear('date', $carbon)->whereMonth('date', $carbon)
            ->orderBy('date', 'asc')->get();
        foreach ($attendances as $attendance) {
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

        return view('admins.staff_attendance_list', compact('attendances', 'carbon', 'previousMonth', 'nextMonth', 'user'));
    }

    public function approval_detail(Request $request)
    {
        $request_id = $request->attendance_correct_request;
        $correction = StampCorrectionRequest::with('user')->where('id', $request_id)->first();
        $attendance = CorrectionAttendance::with('correction_rests')->where('stamp_correction_request_id', $request_id)->first();
        $correctionRests = CorrectionRest::where('correction_attendance_id', $attendance->id)->get();

        return view('admins.approve', compact('correction', 'attendance', 'correctionRests'));
    }

    public function approve(Request $request)
    {
        $stamp_correction_request = StampCorrectionRequest::where('id', $request->id)->first();
        $stamp_correction_request->update(['is_approval' => true]);

        $attendance = Attendance::with('rests')->where('id', $stamp_correction_request->attendance_id)->first();

        $rests = $attendance->rests;
        foreach($rests as $rest)
        {
            Rest::find($rest->id)->delete();
        }

        $correctionRests = $attendance->rests;
        foreach($correctionRests as $rest)
        {
            Rest::create(['attendance_id' => $attendance->id, 'start_time' => $rest->start_time, 'end_time' => $rest->end_time]);
        }

        return redirect()->route('approval_detail', ['attendance_correct_request' => $request->id]);
    }
}
