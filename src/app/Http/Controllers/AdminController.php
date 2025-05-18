<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Http\Requests\LoginRequest;

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

        $attendances = Attendance::with('user')->whereDate('date', $carbon)->get();

        return view('admins.attendance_list', compact('carbon', 'attendances', 'previousDay', 'nextDay'));
    }

}
