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
        <form action="/login" method="post" class="login__form">
            @csrf
            <div class="form-item">
                <p class="form-item__text">メールアドレス</p>
                <input type="email" name="email">
                @error('email')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-item">
                <p class="form-item__text">パスワード</p>
                <input type="password" name="password">
                @error('password')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-item">
                <input type="submit" value="ログインする">
            </div>
        </form>
    </div>
    <a href="register" class="register__link">会員登録はこちら</a>
</div>
@endsection