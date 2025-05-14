@extends('layouts/app')

@section('css')
@endsection

@section('content')
<div class="select-container">
    <a href="">管理者はこちら</a>
    <a href="{{ route('login') }}">一般ユーザーはこちら</a>
</div>
@endsection