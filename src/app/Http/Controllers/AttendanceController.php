<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $currentTime = CarbonImmutable::now();

        return view('attendance', compact('currentTime'));
    }
}
