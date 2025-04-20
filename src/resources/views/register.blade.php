@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-container">
    <h1 class="register__title">会員登録</h1>
    <div class="register-inner">
        <form action="register/store" class="register__form" method="post">
            @csrf
            <div class="form-item">
                <p class="form-item__text">名前</p>
                <input type="text" class="form-item__input" name="name">
                @error('name')
                    <p class="error-message">{{ $message}}</p>
                @enderror
            </div>
            <div class="form-item">
                <p class="form-item__text">メールアドレス</p>
                <input type="text" class="form-item__input" name="email">
                @error('email')
                    <p class="error-message">{{ $message}}</p>
                @enderror
            </div>
            <div class="form-item">
                <p class="form-item__text">パスワード</p>
                <input type="text" class="form-item__input" name="password">
                @error('password')
                    <p class="error-message">{{ $message}}</p>
                @enderror
            </div>
            <div class="form-item">
                <p class="form-item__text">パスワード確認</p>
                <input type="text" class="form-item__input" name="password_confirmation">
                @error('password_confirmation')
                    <p class="error-message">{{ $message}}</p>
                @enderror
            </div>
            <div class="form-item">
                <button class="form-item__button">登録する</button>
            </div>
        </form>
    </div>
    <a href="/login" class="login__link">ログインはこちら</a>
</div>
@endsection