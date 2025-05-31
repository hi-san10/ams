@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('header')
    @if (Auth::guard('admins')->check())
        @include('layouts/admin')
    @else
        @include('layouts/in_work')
    @endif
@endsection

@section('content')
<div class="detail-container">
    <div class="detail-title">
        <h1 class="title__text">勤怠詳細</h1>
    </div>
    <form action="@if(Auth::check()){{ route('correction', ['id' => $attendance->id]) }}
        @else{{ route('admin_correction', ['id' => $attendance->id]) }}
        @endif" method="post">
        @csrf
        <table>
            @if (is_null($hasStampCorrectionRequest) or $is_approval == true or Auth::guard('admins')->check() && $is_approval == false)
            <!-- 詳細リンクから(未申請 or 修正承認済み or 管理者&未承認) -->
            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $attendance->date->format('Y') }}年</td>
                <td>{{ $attendance->date->format('m') }}月{{ $attendance->date->format('d') }}日</td>
            </tr>
            @else
            <!-- 申請後未承認 -->
            <tr>
                <th>名前</th>
                <td>{{ $hasStampCorrectionRequest->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $hasStampCorrectionRequest->target_date->format('Y') }}年</td>
                <td>{{ $hasStampCorrectionRequest->target_date->format('m') }}月{{ $hasStampCorrectionRequest->target_date->format('d') }}日</td>
            </tr>
            @endif
            <tr>
                <th>出勤・退勤</th>
                <td><input type="text" class="attendance-time" name="start" value="{{ old('start', substr($attendance->start_time, 0, 5)) }}"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="end" value="{{ old('end', substr($attendance->end_time, 0, 5)) }}"></td>
            </tr>
            @if ($errors->has('start') or $errors->has('end'))
            <tr>
                <th></th>
                <td>@error('start') {{ $message }} @enderror</td>
                <td></td>
                <td>@error('end') {{ $message }} @enderror</td>
            </tr>
            @endif
            @if ($attendance->rests)
            <!-- 休憩があった場合 -->
            @foreach($attendance->rests as $index => $rest)
            <tr>
                <th>休憩</th>
                <td><input type="text" class="attendance-time" name="rest_start[{{ $index }}]" value="{{ old('rest_start.'.$index, substr($rest->start_time, 0, 5)) }}"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="rest_end[{{ $index }}]" value="{{ old('rest_end.'.$index, substr($rest->end_time, 0, 5)) }}"></td>
            </tr>
            @if ($errors->has('rest_start.*') or $errors->has('rest_end.*'))
            <tr>
                <th></th>
                <td>@error('rest_start.'.$index) {{ $message }} @enderror</td>
                <td></td>
                <td>@error('rest_end.'.$index) {{ $message }} @enderror</td>
            </tr>
            @endif
            @endforeach
            @endif
            <!-- 新規休憩入力フィールド -->
            <tr>
                <th>休憩</th>
                <td><input type="text" class="attendance-time" name="newRest_start" value="{{ old('newRest_start') }}"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="newRest_end" value="{{ old('newRest_end') }}"></td>
            </tr>
            @if ($errors->has('newRest_start') or $errors->has('newRest_end'))
            <tr>
                <th></th>
                <td>@error('newRest_start') {{ $message }} @enderror</td>
                <td></td>
                <td>@error('newRest_end') {{ $message }} @enderror</td>
            </tr>
            @endif
            <tr>
                <th>備考</th>
                <td>
                    <textarea name="remarks" id="" class="remarks" value="">
                        @if (is_null($hasStampCorrectionRequest))
                            {{ old('remarks') }}
                        @else
                            {{ $hasStampCorrectionRequest->request_reason }}
                        @endif
                    </textarea>
                </td>
            </tr>
            @error('remarks')
            <tr>
                <th></th>
                <td>{{ $message }}</td>
            </tr>
            @enderror
        </table>
        @if (is_null($hasStampCorrectionRequest) or Auth::guard('admins')->check() && $is_approval == false)
        <!-- 未申請 or 管理者&未承認 -->
        <input type="submit" value="修正">
        @elseif (Auth::check() && $is_approval == false)
        <!-- 一般ユーザー&未承認 -->
        <p class="pending__text">*承認待ちのため修正はできません</p>
        @elseif ($is_approval == true)
        <!-- 承認済み -->
        <p>修正承認済み</p>
        @endif
    </form>
</div>
@endsection