<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AdminUser;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\StampCorrectionRequest;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonImmutable;
use App\Services\AttendanceService;
use App\Services\CsvService;

class AdminController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService, CsvService $csvService)
    {
        $this->attendanceService = $attendanceService;
        $this->csvService = $csvService;
    }

    public function getLogin(Request $request)
    {
        return view('admins.admin_login');
    }

    public function postLogin(LoginRequest $request)
    {
        $admin = AdminUser::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()
                ->with('message', 'ログイン情報が登録されていません')
                ->withInput();
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
        $previousDay = $carbon->subDay(1);
        $nextDay = $carbon->addDay(1);

        $allAttendances = Attendance::with('user', 'rests')
            ->whereDate('date', $carbon)
            ->get();
        $attendances = $this->attendanceService->calculate($allAttendances);

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
        if (is_null($baseDate)) {
            $carbon = CarbonImmutable::today();
        } else {
            $carbon = new CarbonImmutable($baseDate);
        }
        $previousMonth = $carbon->subMonthNoOverflow(1);
        $nextMonth = $carbon->addMonthNoOverflow(1);


        $user = User::where('id', $request->id)
                ->select('id', 'name')
                ->first();
        $userMonthlyAttendances = Attendance::with('user', 'rests')
            ->where('user_id', $user->id)
            ->whereYear('date', $carbon)
            ->whereMonth('date', $carbon)
            ->get();
        $attendances = $this->attendanceService->calculate($userMonthlyAttendances);

        return view('admins.staff_attendance_list', compact('attendances', 'carbon', 'previousMonth', 'nextMonth', 'user'));
    }

    public function approval_detail(Request $request)
    {
        $request_id = $request->attendance_correct_request;
        $correction = StampCorrectionRequest::with('user')
            ->where('id', $request_id)
            ->first();
        if ($correction->is_approval == false) {
            $attendance = CorrectionAttendance::with('rests')
                ->where('stamp_correction_request_id', $request_id)
                ->first();
        } else {
            $attendance = Attendance::with('rests')
                ->where('id', $correction->attendance_id)
                ->first();
        }

        return view('admins.approve', compact('correction', 'attendance'));
    }

    public function approve(Request $request)
    {
        $stamp_correction_request = StampCorrectionRequest::where('id', $request->id)->first();
        $correctionAttendance = CorrectionAttendance::with('rests')->where('stamp_correction_request_id', $stamp_correction_request->id)->first();
        $attendance = Attendance::with('rests')->where('id', $stamp_correction_request->attendance_id)->first();
        DB::transaction(function () use(
            $stamp_correction_request,
            $correctionAttendance,
            $attendance,
        ) {
            $stamp_correction_request->update(['is_approval' => true]);
            $attendance->update(['start_time' => $correctionAttendance->start_time, 'end_time' => $correctionAttendance->end_time]);
            $attendance->rests()->delete();
            $attendance->rests()->createMany(
                $correctionAttendance->rests
                    ->map(fn ($rest) => [
                        'start_time' => $rest->start_time,
                        'end_time' => $rest->end_time,
                    ])
                    ->toArray());
        });

        return redirect()->route('approval_detail', ['attendance_correct_request' => $request->id]);
    }

    public function correction(CorrectionRequest $request)
    {
        $attendance = Attendance::with('user', 'rests')
            ->where('id', $request->id)
            ->first();
        $stamp_correction_request = StampCorrectionRequest::where('attendance_id', $attendance->id)->first();

        DB::transaction(function () use(
            $request,
            $attendance,
            // $rests,
            $stamp_correction_request,
        ) {
            $attendance->update(['start_time' => $request->start, 'end_time' => $request->end]);
            $attendance->rests()->delete();

            if ($request->has('rests')) {
                $attendance->rests()->createMany($request->rests);
            }

            if ($stamp_correction_request) {
                $stamp_correction_request->update([
                    'is_approval' => true,
                    'request_date' => CarbonImmutable::today(),
                    'request_reason' => $request->remarks,
                ]);
            } else {
                StampCorrectionRequest::create([
                    'user_id' => $attendance->user->id,
                    'attendance_id' => $attendance->id,
                    'is_approval' => true,
                    'target_date' => $attendance->date,
                    'request_date' => CarbonImmutable::today(),
                    'request_reason' => $request->remarks,
                ]);
            }
        });

        return redirect()->route('attendance_detail', ['id' => $attendance->id]);
    }

    public function csv(Request $request)
    {
        $carbon = new CarbonImmutable($request->month);

        $userAttendance = Attendance::with('rests')
            ->where('user_id', $request->id)
            ->whereYear('date', $carbon)
            ->whereMonth('date', $carbon)
            ->oldest('date')
            ->get();

        $attendances = $this->attendanceService->calculate($userAttendance);
        $csv = $this->csvService->format($attendances);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=SJIS-win',
            'Content-Disposition' => 'attachment; filename="attendance.csv"',
        ]);
    }
}
