@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="detail-container">
    <div class="detail-title">
        <h1 class="title__text">勤怠詳細</h1>
    </div>
    @if (is_null($hasStampCorrectionRequest))
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
            @if ($errors->any())
            <tr>
                <th></th>
                <td>@error('start'){{ $message }}@enderror</td>
                <td>~</td>
                <td>@error('end'){{ $message }}@enderror</td>
            </tr>
            @endif
            @if ($rests)
            @foreach($rests as $rest)
            <tr>
                <th>休憩</th>
                <td><input type="text" class="attendance-time" name="rest_start[]" value="{{ substr($rest->start_time, 0, 5) }}"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="rest_end[]" value="{{ substr($rest->end_time, 0, 5) }}"></td>
            </tr>
            @if ($errors->any())
            <tr>
                <th></th>
                <td>@error('rest_start'){{ $message }}@enderror</td>
                <td>~</td>
                <td>@error('rest_end'){{ $message }}@enderror</td>
            </tr>
            @endif
            @endforeach
            @endif
            <tr>
                <th>休憩</th>
                <td><input type="text" class="attendance-time" name="newRest_start" value="{{ old('newRest_start') }}"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="newRest_end" value="{{ old('newRest_end') }}"></td>
            </tr>
            @if ($errors->any())
            <tr>
                <th></th>
                <td>@error('newRest_start'){{ $message }}@enderror</td>
                <td>~</td>
                <td>@error('newRest_end'){{ $message }}@enderror</td>
            </tr>
            @endif
            <tr>
                <th>備考</th>
                <td>
                    <textarea name="remarks" id="" class="remarks">@error('remarks'){{$message}}@enderror</textarea>
                </td>
            </tr>
        </table>
        <input type="submit" value="修正">
    </form>
    @else
    <!-- 修正リンクから(修正待ち) -->
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
            <td><input type="text" class="attendance-time" value="{{ $correctionAttendance->start_time }}" readonly></td>
            <td>~</td>
            <td><input type="text" class="attendance-time" value="{{ $correctionAttendance->end_time }}" readonly></td>
        </tr>
        @if ($correctionRests)
        @foreach($correctionRests as $correctionRest)
        <tr>
            <th>休憩</th>
            <td><input type="text" class="attendance-time" value="{{ substr($correctionRest->start_time, 0, 5) }}" readonly></td>
            <td>~</td>
            <td><input type="text" class="attendance-time" value="{{ substr($correctionRest->end_time, 0, 5) }}" readonly></td>
        </tr>
        @endforeach
        @endif
        <tr>
            <th>休憩</th>
            <td><input type="text" class="attendance-time" readonly></td>
            <td>~</td>
            <td><input type="text" class="attendance-time" readonly></td>
        </tr>
        <tr>
            <th>備考</th>
            <td><textarea id="" class="remarks">{{ $hasStampCorrectionRequest->request_reason }}</textarea></td>
        </tr>
    </table>
    <p class="pending__text">*承認待ちのため修正はできません。</p>
    @endif
</div>
@endsection