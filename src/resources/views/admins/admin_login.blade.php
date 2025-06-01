@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-container">
    <h1 class="login__title">管理者ログイン</h1>
    @if (session('message'))
    <p class="session-message">{{ session('message') }}</p>
    @endif
    <div class="login-inner">
        <form action="/admin/login" method="post" class="login__form">
            @csrf
            <div class="form-item">
                <p class="form-item__text">メールアドレス</p>
                <input type="email" class="form-item__input" name="email" value="{{ old('email') }}">
                @error('email')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-item">
                <p class="form-item__text">パスワード</p>
                <input type="password" class="form-item__input" name="password">
                @error('password')
                <p class="error-message">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-item">
                <input type="submit" class="form-item__submit" value="管理者ログインする">
            </div>
        </form>
    </div>
</div>
@endsection