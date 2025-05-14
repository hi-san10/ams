@extends('layouts/app')

@section('css')
@endsection

@section('content')
<div class="select-container">
    <a href="{{ route('login') }}?page=admin">管理者はこちら</a>
    <a href="{{ route('login') }}">一般ユーザーはこちら</a>
</div>
@endsection