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
        $carbon = new CarbonImmutable;

        $user = Attendance::where('user_id', Auth::id())->whereDate('date', $carbon)->first();

        Rest::create([
            'attendance_id' => $user->id,
            'start_time' => $carbon
        ]);

        return redirect('attendance');
    }

    public function end()
    {
        $carbon = new CarbonImmutable;

        $user = Attendance::where('user_id', Auth::id())->whereDate('date', $carbon)->first();

        Rest::where([['attendance_id', $user->id], ['end_time', null]])
            ->update(['end_time' => $carbon]);

        return redirect('attendance');
    }
}
