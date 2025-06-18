@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('header')
@include('layouts/in_work')
@endsection

@section('content')
<div class="attendance_list-container">
    <div class="attendance_list-title">
        <h1 class="title-text">勤怠一覧</h1>
    </div>
    <div class="attendance_list-date">
        <a href="{{ route('attendance_list', ['month' => $previousMonth]) }}" class="date gray">←前月</a>
        <p class="date">{{ $carbon->format('Y/m') }}</p>
        <a href="{{ route('attendance_list', ['month' => $nextMonth]) }}" class="date gray">→翌月</a>
    </div>
    <table>
        <tr class="top">
            <th class="left-top">日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th class="right-top">詳細</th>
        </tr>
        @foreach($attendances as $attendance)
        <tr>
            <td>{{ $attendance->date->isoFormat('MM/D(ddd)') }}</td>
            <td>{{ substr($attendance->start_time, 0, 5) }}</td>
            <td>
                @if (is_null($attendance->end_time))
                @else {{ substr($attendance->end_time, 0, 5) }}
                @endif
            </td>
            <td>
                @if ($attendance->totalRest == '00:00:00')
                @else {{ substr($attendance->totalRest, 0, 5) }}
                @endif
            </td>
            <td>
                @if (is_null($attendance->end_time))
                @else {{ substr($attendance->totalWork, 0, 5) }}
                @endif
            </td>
            <td><a href="{{ route('attendance_detail', ['id' => $attendance->id]) }}" class="detail">詳細</a></td>
        </tr>
        @endforeach
    </table>
    <div class="bottom"></div>
</div>
@endsection