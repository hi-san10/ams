<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Http\Requests\LoginRequest;
use App\Models\Rest;
use Carbon\CarbonInterval;

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
        foreach ($attendances as $index => $attendance)
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
            }

            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);
        }

        return view('admins.attendance_list', compact('carbon', 'attendances', 'previousDay', 'nextDay'));
    }
}
