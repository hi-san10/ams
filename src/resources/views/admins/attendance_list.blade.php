@extends('layouts.app')

@section('css')
@endsection

@section('content')
<div class="attendance_list-container">
    <div class="attendance_list-title">
        <h1 class="title-text">{{ $carbon->format('Y年m月d日') }}の勤怠一覧</h1>
    </div>
    <div class="attendance_list-date">
        <a href="{{ route('admin_attendance_list', ['day' => $previousDay]) }}" class="date">←前日</a>
        <p class="date">{{ $carbon->format('Y/m/d') }}</p>
        <a href="{{ route('admin_attendance_list', ['day' => $nextDay]) }}" class="date">→翌日</a>
    </div>
    <table>
        <tr>
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        @foreach($attendances as $attendance)
        <tr>
            <td>{{ $attendance->user->name }}</td>
            <td>{{ substr($attendance->start_time, 0, 5) }}</td>
            <td>{{ substr($attendance->end_time, 0, 5) }}</td>
            <td>{{ substr($attendance->rest_total_time, 0, 5) }}</td>
            <td>{{ substr($attendance->total_working_time, 0, 5) }}</td>
            <td><a href="{{ route('attendance_detail', ['id' => $attendance->id]) }}" class="detail">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection