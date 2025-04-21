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

        // $user = User::whereId(Auth::id())->first();
        $user = Attendance::where('user_id', Auth::id())->whereDate('start_time', CarbonImmutable::today())->first();
        $end = Attendance::where('user_id', Auth::id())->whereDate('end_time', CarbonImmutable::today())->exists();
        $rest = Rest::where('attendance_id', $user->id)->whereDate('end_time', CarbonImmutable::today())->exists();
        // dd($user);

        return view('attendance', compact('currentTime', 'user', 'end', 'rest'));
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
