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
use App\Http\Requests\CorrectionRequest;
use Illuminate\Support\Facades\Response;

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

        $previousMonth = $carbon->subMonthNoOverflow(1);
        $nextMonth = $carbon->addMonthNoOverflow(1);

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
        if ($correction->is_approval == false)
        {
            $attendance = CorrectionAttendance::with('rests')->where('stamp_correction_request_id', $request_id)->first();
        }else{
            $attendance = Attendance::with('rests')->where('id', $correction->attendance_id)->first();
        }
        return view('admins.approve', compact('correction', 'attendance'));
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

    public function correction(CorrectionRequest $request)
    {
        $attendance = Attendance::with('user')->where('id', $request->id)->first();
        $attendance->update(['start_time' => $request->start, 'end_time' => $request->end]);

        $rests = Rest::where('attendance_id', $request->id)->get();
        if($rests)
        {
            Rest::destroy($rests);
        }

        if($request->rest_start)
        {
            $rest_starts = $request->rest_start;
            foreach($rest_starts as $rest_start)
            {
                $rest = new Rest;
                $rest->attendance_id = $attendance->id;
                $rest->start_time = $rest_start;
                $rest->save();
            }

            $rest_end = $request->rest_end;
            foreach($rest_end as $rest_end)
            {
                $rest = Rest::where('attendance_id', $attendance->id)->whereNull('end_time')->first();
                $rest->end_time = $rest_end;
                $rest->save();
            }
        }

        if ($request->newRest_start)
        {
            Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => $request->newRest_start,
                'end_time' => $request->newRest_end]);
        }

        $stamp_correction_request = StampCorrectionRequest::where('attendance_id', $attendance->id)->first();
        if ($stamp_correction_request)
        {
            $stamp_correction_request->update([
                'is_approval' => true,
                'request_date' => CarbonImmutable::today(),
                'request_reason' => $request->remarks]);
        }else{
            StampCorrectionRequest::create([
                'user_id' => $attendance->user->id,
                'attendance_id' => $attendance->id,
                'is_approval' => true,
                'target_date' => $attendance->date,
                'request_date' => CarbonImmutable::today(),
                'request_reason' => $request->remarks]);
        }

        return redirect()->route('attendance_detail', ['id' => $attendance->id]);
    }

    public function csv(Request $request)
    {
        $carbon = CarbonImmutable::today();
        $attendances = Attendance::with('rests')->where('user_id', $request->id)->whereYear('date', $carbon)->whereMonth('date', $carbon)->oldest('date')->get();

        $head = ['日付', '出勤', '退勤', '休憩', '合計'];

        $temps = [];
        array_push($temps, $head);

        foreach($attendances as $attendance)
        {
            $start = new CarbonImmutable($attendance->start_time);
            $end = new CarbonImmutable($attendance->end_time);
            $workingTime = $start->diffInSeconds($end);

            $number = 0;
            foreach($attendance->rests as $rest)
            {
                $restStart = new CarbonImmutable($rest->start_time);
                $restEnd = new CarbonImmutable($rest->end_time);
                $diffRest = $restStart->diffInSeconds($restEnd);
                $number = $number + $diffRest;
            }
            $attendance->totalRest = gmdate('H:i:s', $number);
            $attendance->totalWork = gmdate('H:i:s', $workingTime - $number);

            $temp = [
                substr($attendance->date, 0, 10),
                substr($attendance->start_time, 0, 5),
                substr($attendance->end_time, 0, 5),
                substr($attendance->totalRest, 0, 5),
                substr($attendance->totalWork, 0, 5)
            ];
            array_push($temps, $temp);
        }

        $f = fopen('php://temp', 'r+b');
        foreach($temps as $temp)
        {
            fputcsv($f, $temp);
        }

        rewind($f);
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($f));
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
        $headers = array(
            'Content-Type' => 'text/csv',
        );

        return Response::make($csv, 200, $headers);
    }
}
