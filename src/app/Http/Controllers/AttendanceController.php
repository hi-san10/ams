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
        $currentTime = CarbonImmutable::now();

        $user = Attendance::where('user_id', Auth::id())->whereDate('start_time', $currentTime)->first();
        $rest = Rest::when($user, fn ($query) => $query->where('attendance_id', $user->id)->whereDate('start_time', $currentTime)
        ->where('end_time', null))->exists();
        $workEnd = Attendance::where('user_id', Auth::id())->whereDate('end_time', $currentTime)->exists();

        return view('attendance', compact('currentTime', 'user', 'workEnd', 'rest'));
    }

    public function start(Request $request)
    {
        Attendance::create([
            'user_id' => Auth::id(),
            'start_time' => CarbonImmutable::now()
        ]);

        return redirect('attendance');
    }

    public function end()
    {
        Attendance::where('user_id', Auth::id())->whereDate('start_time', CarbonImmutable::today())
            ->update(['end_time' => CarbonImmutable::now()]);

        return redirect('attendance');
    }
}
