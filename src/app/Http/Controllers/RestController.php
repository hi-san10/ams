<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;

class RestController extends Controller
{
    public function start()
    {
        $user = Attendance::where('user_id', Auth::id())->whereDate('date', CarbonImmutable::today())->first();

        Rest::create([
            'attendance_id' => $user->id,
            'start_time' => CarbonImmutable::now()
        ]);

        return redirect('attendance');
    }

    public function end()
    {
        $user = Attendance::where('user_id', Auth::id())->whereDate('date', CarbonImmutable::today())->first();

        $lastRest = Rest::where('attendance_id', $user->id)->latest('id')->first();

        $restStart = strtotime($lastRest->start_time);
        $restEnd = strtotime(CarbonImmutable::now());
        $restTotal = gmdate('H:i:s', $restEnd - $restStart);

        Rest::where('attendance_id', $user->id)->where('end_time', null)
            ->update(['end_time' => CarbonImmutable::now(), 'total_time' => $restTotal]);

        return redirect('attendance');
    }
}
