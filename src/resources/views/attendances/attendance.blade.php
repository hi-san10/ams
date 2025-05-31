@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('header')
    @if (is_null($workEnd))
        @include('layouts/in_work')
    @elseif (Auth::guard('admins')->check())
        @include('layouts/admin')
    @else
        @include('layouts/out_work')
    @endif
@endsection

@section('content')
<div class="attendance-container">
    <div class="container-inner">
        <p class="work-status">
            @if (is_null($user))
            勤務外
            @elseif (!is_null($workEnd))
            退勤済
            @elseif ($rest)
            休憩中
            @else
            出勤中
            @endif
        </p>
        <p class="work-day">{{ $carbon->isoFormat('YYYY年M月D日(ddd)') }}</p>
        <p class="current-time">{{ $carbon->format('H:i') }}</p>
    </div>
    <div class="container-inner">
        @if (!$user)
        <a href="/attendance/start" class="attendance_link">出勤</a>
        @elseif (!is_null($workEnd))
        <p class="attendance-massage">お疲れ様でした。</p>
        @elseif (!$rest)
        <a href="/attendance/end" class="attendance_link">退勤</a>
        <a href="/rest/start" class="attendance_link--white">休憩入</a>
        @else
        <a href="/rest/end" class="attendance_link--white">休憩戻</a>
        @endif
    </div>
</div>
@endsection