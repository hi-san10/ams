@extends('layouts/app')

@section('css')
@endsection

@section('content')
<div class="request_list-container">
    <div class="request_list-title">
        <h1 class="title__text">申請</h1>
    </div>
    <div class="status_bar"></div>
    <table>
        <tr>
            <th>状態</th>
            <th>名前</th>
            <th>対象日時</th>
            <th>申請理由</th>
            <th>申請日時</th>
            <th>詳細</th>
        </tr>
        @foreach($attendances as $attendance)
        <tr>
            <th></th>
            <th>{{ $attendance->user->name }}</th>
            <th>{{ $attendance->date->format('Y/m/d') }}</th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
        @endforeach
    </table>
</div>
@endsection