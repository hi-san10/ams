@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="container-inner">
        <p class="work-status">
            @if (is_null($user))
                勤務外
            @elseif ($workEnd)
                退勤済
            @else
                出勤中
            @endif
        </p>
        <p class="work-day">{{ $currentTime->isoFormat('YYYY年M月D日(ddd)') }}</p>
        <p class="current-time">{{ $currentTime->format('H:i') }}</p>
    </div>
    <div class="container-inner">
        @if (!$user)
            <form action="/attendance/start" class="attendance__form" method="post">
                @csrf
                <input type="submit" class="stamp" value="出勤">
            </form>
        @elseif ($workEnd)
            <p class="attendance-massage">お疲れ様でした。</p>
        @elseif (!$rest)
            <form action="/attendance/end" class="attendance__form" method="post">
                @method('patch')
                @csrf
                <input type="submit" class="stamp" value="退勤">
            </form>
            <form action="/rest/start" class="attendance_form" method="post">
                @csrf
                <input type="submit" class="stamp" value="休憩入">
            </form>
        @else
            <form action="/rest/end" class="attendance__form" method="post">
                @method('patch')
                @csrf
                <input type="submit" class="stamp" value="休憩戻">
            </form>
        @endif
    </div>
</div>
@endsection