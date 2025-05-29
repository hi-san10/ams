@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail-container">
    <div class="detail-title">
        <h1 class="title__text">勤怠詳細</h1>
    </div>
    <form action="{{ route('approve', ['id' => $correction->id]) }}" method="post">
        @csrf
        <table>
            <tr>
                <th>名前</th>
                <td>{{ $correction->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $correction->target_date->format('Y') }}年</td>
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
                <td><textarea id="" class="remarks" readonly>{{ $correction->request_reason }}</textarea></td>
            </tr>
        </table>
        @if ($correction->is_approval == false)
        <input type="submit" value="承認">
        @else
        <p>承認済み</p>
        @endif
    </form>
</div>
@endsection