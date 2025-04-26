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

        $attendance = Attendance::where('user_id', Auth::id())->whereDate('start_time', $carbon)->first();
        $restTotal = Rest::where('attendance_id', $attendance->id)->whereDate('start_time', $carbon)->selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(total_time))) as total_time')->first();

        // $restTotal = Attendance::where('user_id', Auth::id())->join('rests', function(JoinClause $join):void{
        //     $join->on('attendances.id', '=', 'rests.attendance_id')
        //     ->selectRaw('SEC_TO_TIME(SUM(TIME_TO_SEC(total_time))) as total');
        // })->get();

        // dd($rests->total);

        // $restTotal = Rest::where('attendance_id', $attendance->id)->whereDate('start_time', $carbon)->selectRaw(
        //     'SEC_TO_TIME(SUM(TIME_TO_SEC(total_time))) as total')->first();
        //     dd($restTotal->total);


        Attendance::where('user_id', Auth::id())->whereDate('start_time', $carbon)
            ->update([
                'end_time' => $carbon,
                'rest_total_time' => $restTotal->total_time
            ]);

        return redirect('attendance');
    }
}
