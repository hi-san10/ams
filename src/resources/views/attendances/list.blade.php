@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance_list-container">
    <div class="attendance_list__title">
        <h1 class="title-text">勤怠一覧</h1>
    </div>
    <div class="attendance_list-date">
        <a href="{{ route('attendance_list', ['month' => $previousMonth]) }}" class="date">←前月</a>
        <p class="date">{{ $carbon->format('Y/m') }}</p>
        <a href="{{ route('attendance_list', ['month' => $nextMonth]) }}" class="date">→翌月</a>
    </div>
    <table>
        <tr>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        @foreach($attendances as $attendance)
        <tr>
            <td>{{ $attendance->date->isoFormat('MM/D(ddd)') }}</td>
            <td>{{ substr($attendance->start_time, 0, 5) }}</td>
            <td>{{ substr($attendance->end_time, 0, 5) }}</td>
            <td>{{ substr($attendance->rest_total_time, 0, 5) }}</td>
            <td>{{ substr($attendance->total_working_time, 0, 5) }}</td>
            <td><a href="" class="detail">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection