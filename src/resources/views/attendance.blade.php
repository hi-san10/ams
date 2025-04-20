@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="container-inner">
        <p class="work-status">勤務外</p>
        <p class="work-day">{{ $currentTime->isoFormat('YYYY年M月D日(ddd)') }}</p>
        <p class="current-time">{{ $currentTime->format('H:i') }}</p>
    </div>
    <div class="container-inner">
        <form action="" class="attendance__form">
            <input type="submit" class="stamp" value="出勤">
        </form>
    </div>
</div>
@endsection