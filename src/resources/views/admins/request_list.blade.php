@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request_list.css') }}">
@endsection

@section('header')
@include('layouts/admin')
@endsection

@section('content')
<div class="request_list-container">
    <div class="request_list-title">
        <h1 class="title-text">申請一覧</h1>
    </div>
    <div class="status_bar">
        <a href="{{ route('request_list') }}" class="status_bar__link" @if(is_null($prm)) style="color: red;" @endif>承認待ち</a>
        <a href="{{ route('request_list') }}?page=approved" class="status_bar__link" @if($prm=="approved" ) style="color:red;" @endif>承認済み</a>
    </div>
    <table>
        <tr>
            <th class="left-top">状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th class="right-top">詳細</th>
        </tr>
        @foreach($correction_requests as $correction_request)
        <tr>
            <th>
                @if ($correction_request->is_approval == false)
                承認待ち
                @else
                承認済み
                @endif
            </th>
            <th>{{ $correction_request->user->name }}</th>
            <th>{{ $correction_request->target_date->format('Y/m/d') }}</th>
            <th>{{ $correction_request->request_reason }}</th>
            <th>{{ $correction_request->request_date->format('Y/m/d') }}</th>
            <th><a href="{{ route('approval_detail', ['attendance_correct_request' => $correction_request->id]) }}" class="detail">詳細</a></th>
        </tr>
        @endforeach
    </table>
    <div class="bottom"></div>
</div>
@endsection