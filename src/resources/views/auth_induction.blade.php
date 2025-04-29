@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth_induction.css') }}">
@endsection

@section('content')
<div class="authInduction-container">
    <div class="container-inner">
        <p class="inner__text">登録していただいたメールアドレスに認証メールを送付しました。</p>
        <p class="inner__text">メール認証を完了してください。</p>
    </div>
    <div class="verification__link">
        <a href="{{ route('verification', ['email' => $email]) }}" class="link-content">認証はこちらから</a>
    </div>
    <div class="resend__link">
        <a href="{{ route('resend', ['email' => $email]) }}" class="link-content--blue">認証メールを再送する</a>
    </div>
</div>
@endsection