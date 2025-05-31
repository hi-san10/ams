@extends('layouts.app')

@section('css')
@endsection

@section('header')
    @include('layouts/admin')
@endsection

@section('content')
<div class="staff_list-container">
    <h1 class="container__title">スタッフ一覧</h1>
</div>
<div class="list-inner">
    <table>
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><a href="{{ route('staff_attendance_list', ['id' => $user->id]) }}">詳細</a></td>
        </tr>
        @endforeach
    </table>
</div>
@endsection