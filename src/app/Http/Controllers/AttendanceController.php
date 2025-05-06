<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
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
        $workEnd = Attendance::where('user_id', Auth::id())->whereNull('end_time')->exists();

        return view('attendances.attendance', compact('carbon', 'user', 'workEnd', 'rest'));
    }

    public function start()
    {
        Attendance::create([
            'user_id' => Auth::id(),
            'date' => CarbonImmutable::today(),
            'start_time' => CarbonImmutable::now()
        ]);

        return redirect('attendance');
    }

    public function end()
    {
        $carbon = new CarbonImmutable;
        $date = $carbon->toDateString();
        $time = $carbon->toTimeString();

        $attendance = Attendance::where('user_id', Auth::id())->where('date', $date)->first();
        $rest = Rest::where('attendance_id', $attendance->id)->selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(total_time))) as total_time')->first();
        $restTotal = strtotime($rest->total_time);

        $workingTime = gmdate('H:i:s', strtotime($time) - strtotime($attendance->start_time));
        $fixesWorkingTime = strtotime($workingTime);
        $totalWorkingTime = gmdate('H:i:s', $fixesWorkingTime - $restTotal);

        Attendance::where('user_id', Auth::id())->whereDate('date', $carbon)
            ->update([
                'end_time' => $time,
                'rest_total_time' => $rest->total_time,
                'total_working_time' => $totalWorkingTime
            ]);

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
        $previousMonth = $carbon->subMonth(1);
        $nextMonth = $carbon->addMonth(1);

        $attendances = Attendance::where('user_id', Auth::id())->whereYear('date', $carbon)->whereMonth('date', $carbon)->get();


        return view('attendances.list', compact('attendances', 'carbon', 'previousMonth', 'nextMonth'));
    }

    public function detail(Request $request)
    {
        $attendance = Attendance::with('user')->where('id', $request->id)->first();
        $rests = Rest::where('attendance_id', $attendance->id)->get();

        $rest_start = 'rest_start1';

        return view('attendances.detail', compact('attendance', 'rests', 'rest_start'));
    }
    // attendance_appliesに保存する
    // public function request(Request $request)
    // {
    //     $attendance = Attendance::with('user')->where('id', $request->id)->first();
    //     $attendance->update(['pending' => CarbonImmutable::now()]);

    //     $rests = Rest::where('attendance_id', $attendance->id)->get();

    //     if (is_null($request))
    //     {
    //         $pending[] = [
    //             'start' => $attendance->start_time,
    //             'end' => $attendance->end_time,
    //             'rest_start' => $attendance->rest_start,
    //             'rest_end' => $attendance->rest_end
    //         ];
    //     }
    //     dd($pending);
    //     $pending[] = [
    //         'start' => $request->start,
    //         'end' => $request->end,
    //         'rest_start' => $request->rest_start,
    //         'rest_end' => $request->rest_end
    //     ];
    //     $request_start = $request->start;
    //     $request_end = $request->end;
    //     $request_restStart = $request->rest_start;
    //     $request_restEnd = $request->rest_end;

    //     return view('attendances.detail', compact('attendance', 'rests', 'request_start', 'request_end', 'request_restStart', 'request_restEnd'));
    // }
}
