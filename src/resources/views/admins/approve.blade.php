@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('header')
@include('layouts/admin')
@endsection

@section('content')
<div class="detail-container">
    <div class="detail-title">
        <h1 class="title-text">勤怠詳細</h1>
    </div>
    <form action="{{ route('approve', ['id' => $correction->id]) }}" method="post" class="detail__form">
        @csrf
        <table>
            <tr>
                <th class="left-top">名前</th>
                <td>{{ $correction->user->name }}</td>
                <td></td>
                <td class="right-top"></td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $correction->target_date->format('Y') }}年</td>
                <td></td>
                <td>{{ $correction->target_date->format('m') }}月{{ $correction->target_date->format('d') }}日</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td><input type="text" class="attendance-time" value="{{ substr($attendance->start_time, 0, 5) }}" readonly></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" value="{{ substr($attendance->end_time, 0, 5) }}" readonly></td>
            </tr>
            @if ($attendance->rests)
            @php $i=1; @endphp
            @foreach($attendance->rests as $index => $rest)
            <tr>
                <th>休憩@if ($index == 0) @else {{ $index+1 }} @endif</th>
                <td><input type="text" class="attendance-time" name="rest_start[{{ $index }}]" value="{{ substr($rest->start_time, 0, 5) }}" readonly></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="rest_end[{{ $index }}]" value="{{ substr($rest->end_time, 0, 5) }}" readonly></td>
            </tr>
            @endforeach
            @endif
            <tr>
                <th>休憩<span class="i">{{ $index+2 }}</span></th>
                <td><input type="text" class="attendance-time" readonly></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" readonly></td>
            </tr>
            <tr>
                <th>備考</th>
                <td colspan="3"><textarea id="" class="remarks" readonly>{{ $correction->request_reason }}</textarea></td>
                <td></td>
            </tr>
        </table>
        <div class="bottom"></div>
        @if ($correction->is_approval == false)
        <input type="submit" class="correct__btn" value="承認">
        @else
        <p class="approval__text is_approval">承認済み</p>
        @endif
    </form>
</div>
@endsection