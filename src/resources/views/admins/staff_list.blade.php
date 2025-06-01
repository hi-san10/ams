@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('header')
@include('layouts/admin')
@endsection

@section('content')
<div class="staff_list-container">
    <div class="staff_list-title">
        <h1 class="title-text">スタッフ一覧</h1>
    </div>
    <table>
        <tr>
            <th class="left-top">名前</th>
            <th>メールアドレス</th>
            <th class="right-top">月次勤怠</th>
        </tr>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><a href="{{ route('staff_attendance_list', ['id' => $user->id]) }}" class="detail">詳細</a></td>
        </tr>
        @endforeach
    </table>
    <div class="bottom"></div>
</div>
@endsection