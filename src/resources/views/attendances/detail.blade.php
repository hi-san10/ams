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
        <h1 class="title-text">勤怠詳細</h1>
    </div>
    <form action="@if(Auth::check()){{ route('correction', ['attendance' => $attendance->id]) }}
        @else{{ route('admin_correction', ['id' => $attendance->id]) }}
        @endif" method="post" class="detail__form">
        @csrf
        <table>
            <tr>
                <th class="left-top">名前</th>
                <td>{{ $userName }}</td>
                <td></td>
                <td class="right-top"></td>
            </tr>
            <tr>
                <th>日付</th>
                <td>{{ $date->format('Y') }}年</td>
                <td></td>
                <td>{{ $date->format('m') }}月{{ $date->format('d') }}日</td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td><input type="text" class="attendance-time" name="start" value="{{ old('start', $attendance->start_time->format('H:i')) }}"></td>
                <td>~</td>
                <td><input type="text" class="attendance-time" name="end" value="{{ old('end', $attendance->end_time->format('H:i')) }}"></td>
            </tr>

            @if ($errors->has('start') or $errors->has('end'))
            <tr>
                <th></th>
                <td style="color: red;">@error('start') {{ $message }} @enderror</td>
                <td></td>
                <td style="color: red;">@error('end') {{ $message }} @enderror</td>
            </tr>
            @endif

            @foreach($rests as $index => $rest)
            <tr>
                <th>休憩</th>
                <td>
                    <input
                        type="text"
                        class="attendance-time"
                        name="rests[{{ $index }}][start_time]"
                        value="{{ old('rests.'.$index.'.start_time', $rest['start_time']) }}"
                    >
                    <div style="color: red;">
                        @error('rests.'.$index.'.start_time')
                            {{ $message }}
                        @enderror
                    </div>
                </td>
                <td>~</td>
                <td>
                    <input
                        type="text"
                        class="attendance-time"
                        name="rests[{{ $index }}][end_time]"
                        value="{{ old('rests.'.$index.'.end_time', $rest['end_time']) }}"
                    >
                <div style="color: red;">
                    @error('rests.'.$index.'.end_time')
                        {{ $message }}
                    @enderror
                </div>
                </td>
            </tr>
            @endforeach

            <tr>
                <th class="left-bottom">備考</th>
                <td colspan="3">
                    <textarea name="remarks" id="" class="remarks" value="">
                        @if (is_null($hasStampCorrectionRequest))
                            {{ old('remarks') }}
                        @else
                            {{ $hasStampCorrectionRequest->request_reason }}
                        @endif
                    </textarea>
                    <div style="color: red;">
                        @error('remarks')
                            {{ $message }}
                        @enderror
                    </div>
                </td>
                <td></td>
            </tr>
        </table>

        <div class="bottom"></div>
        <!-- 承認済み -->
        @if ($is_approval == true)
        <p class="approval__text">修正承認済み</p>

        <!-- 未申請 or 管理者&未承認 -->
        @elseif (is_null($hasStampCorrectionRequest) || Auth::guard('admins')->check())
        <input type="submit" value="修正" class="correct__btn">

        <!-- 一般ユーザー&未承認 -->
        @elseif (Auth::check())
        <p class="pending__text">*承認待ちのため修正はできません</p>
        @endif
    </form>
</div>
@endsection
