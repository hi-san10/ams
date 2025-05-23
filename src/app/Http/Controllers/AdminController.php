<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Http\Requests\LoginRequest;
use App\Models\User;

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
}
