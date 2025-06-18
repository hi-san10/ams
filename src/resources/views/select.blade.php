@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/select.css') }}">
@endsection

@section('content')
<div class="select-container">
    <a class="link" href="/admin/login">管理者はこちら</a>
    <a class="link" href="/login">一般ユーザーはこちら</a>
</div>
@endsection