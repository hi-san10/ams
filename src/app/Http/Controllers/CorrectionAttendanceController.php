<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\StampCorrectionRequest;
use App\Http\Requests\CorrectionRequest;
use App\Services\CorrectionService;
use Illuminate\Support\Facades\Auth;

class CorrectionAttendanceController extends Controller
{
    public function __construct(CorrectionService $correctionService)
    {
        $this->correctionService = $correctionService;
    }

    public function correction(CorrectionRequest $request, Attendance $attendance)
    {
        $this->correctionService->correction($request, $attendance);

        return redirect()->route('attendance_list');
    }

    public function list(Request $request)
    {
        $prm = $request->page;
        $approval = $prm === 'approved';

        $query = StampCorrectionRequest::with('user', 'attendance')
        ->where('is_approval', $approval);

        if (Auth::check()) {
            $correction_requests = $query
            ->where('user_id', Auth::id())
            ->get();

            return view('request_list', compact('correction_requests', 'prm'));

        } elseif (Auth::guard('admins')->check()) {
            $correction_requests = $query
            ->get();

            return view('admins.request_list', compact('correction_requests', 'prm'));
        }
    }
}
