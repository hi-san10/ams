@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-container">
    <h1 class="login__title">ログイン</h1>
    @if (session('message'))
        <p class="session-message">{{ session('message') }}</p>
    @endif
    <div class="login-inner">
        <form action="" class="login__form">
            <div class="form-item">
                <p class="form-item__text">メールアドレス</p>
                <input type="email">
            </div>
            <div class="form-item">
                <p class="form-item__text">パスワード</p>
                <input type="password">
            </div>
            <div class="form-item">
                <button class="form-item__button">ログインする</button>
            </div>
        </form>
    </div>
    <a href="register" class="register__link">会員登録はこちら</a>
</div>
@endsection