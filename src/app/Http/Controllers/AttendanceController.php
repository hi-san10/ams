<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $carbon = CarbonImmutable::now();

        $user = Attendance::where('user_id', Auth::id())->whereDate('start_time', $carbon)->first();
        $rest = Rest::when($user, fn ($query) => $query->where('attendance_id', $user->id)->whereDate('start_time', $carbon)
        ->where('end_time', null))->exists();
        $workEnd = Attendance::where('user_id', Auth::id())->whereDate('end_time', $carbon)->exists();

        return view('attendance', compact('carbon', 'user', 'workEnd', 'rest'));
    }

    public function start()
    {
        Attendance::create([
            'user_id' => Auth::id(),
            'start_time' => CarbonImmutable::today()
        ]);

        return redirect('attendance');
    }

    public function end()
    {
        $carbon = CarbonImmutable::today();
        $currentTime = CarbonImmutable::now();

        $attendance = Attendance::where('user_id', Auth::id())->whereDate('start_time', $carbon)->first();
        $rest = Rest::where('attendance_id', $attendance->id)->whereDate('start_time', $carbon)->selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(total_time))) as total_time')->first();
        $restTotal = strtotime($rest->total_time);

        $workingTime = gmdate('H:i:s', strtotime($currentTime) - strtotime($attendance->start_time));
        $fixesWorkingTime = strtotime($workingTime);
        $totalWorkingTime = gmdate('H:i:s', $fixesWorkingTime - $restTotal);

        Attendance::where('user_id', Auth::id())->whereDate('start_time', $carbon)
            ->update([
                'end_time' => $carbon,
                'rest_total_time' => $rest->total_time,
                'total_working_time' => $totalWorkingTime
            ]);

        return redirect('attendance');
    }
}
