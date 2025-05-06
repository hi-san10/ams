@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail-container">
    <div class="detail-title">
        <h1 class="title__text">勤怠詳細</h1>
    </div>
    @if (is_null($attendance->pending))
    <!-- 詳細リンクから(未申請) -->
    <form action="{{ route('correction', ['id' => $attendance->id]) }}" method="post">
        @csrf
        <table>
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $attendance->date->format('Y') }}年</td>
                <td>{{ $attendance->date->format('m') }}月{{ $attendance->date->format('d') }}日</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td><input type="text" class="attendance-time" name="start" value="{{ substr($attendance->start_time, 0, 5) }}"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="end" value="{{ substr($attendance->end_time, 0, 5) }}"></td>
            </tr>
            @if ($rests)
            @foreach($rests as $rest)
            <tr>
                <th>休憩</th>
                <td><input type="text" class="attendance-time" name="rest_start[]" value="{{ substr($rest->start_time, 0, 5) }}"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="rest_end[]" value="{{ substr($rest->end_time, 0, 5) }}"></td>
            </tr>
            @endforeach
            @endif
            <tr>
                <th>休憩</th>
                <td><input type="text" class="attendance-time" name="newRest_start"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="newRest_end"></td>
            </tr>
            <tr>
                <th>備考</th>
                <td><textarea name="remarks" id="" class="remarks"></textarea></td>
            </tr>
        </table>
        <input type="submit" value="修正">
    </form>
    @else
    <!-- 修正リンクから -->
    <table>
        <tr>
            <th>出勤・退勤</th>
            <td><input type="text" class="attendance-time" value="{{ $request_start }}"></td>
            <td>~</td>
            <td><input type="text" class="attendance-time" value="{{ $request_end }}"></td>
        </tr>
        @if ($rests)
        @foreach($rests as $rest)
        <tr>
            <th>休憩</th>
            <td><input type="text" class="attendance-time" value="{{ substr($request_restStart, 0, 5) }}"></td>
            <td>~</td>
            <td><input type="text" class="attendance-time" value="{{ substr($request_restEnd, 0, 5) }}"></td>
        </tr>
        @endforeach
        @endif
        <tr>
            <th>休憩</th>
            <td><input type="text" class="attendance-time" name="rest_start"></td>
            <td>~</td>
            <td><input type="text" class="attendance-time" name="rest-end"></td>
        </tr>
        <tr>
            <th>備考</th>
            <td><textarea name="remarks" id="" class="remarks"></textarea></td>
        </tr>
    </table>
    <p class="pending__text">*承認待ちのため修正はできません。</p>
    @endif
</div>
@endsection