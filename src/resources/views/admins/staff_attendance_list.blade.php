@extends('layouts/app')

@section('css')
@endsection

@section('content')
<div class="attendance_list-container">
    <div class="attendance_list-title">
        <h1 class="title-text">{{ $user->name }}さんの勤怠</h1>
    </div>
    <div class="attendance_list-date">
        <a href="{{ route('staff_attendance_list', ['month' => $previousMonth, 'id' => $user->id]) }}" class="date">←前月</a>
        <p class="date">{{ $carbon->format('Y/m') }}</p>
        <a href="{{ route('staff_attendance_list', ['month' => $nextMonth, 'id' => $user->id]) }}" class="date">→翌月</a>
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
            <td>{{ substr($attendance->totalRest, 0, 5) }}</td>
            <td>{{ substr($attendance->totalWork, 0, 5) }}</td>
            <td><a href="{{ route('attendance_detail', ['id' => $attendance->id]) }}" class="detail">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection