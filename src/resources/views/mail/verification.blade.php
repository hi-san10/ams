@extends('layouts/app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verification.css') }}">
@endsection

@section('content')
<div class="verification-container">
    <a href="{{ route('verification', ['email' => $email]) }}">認証を完了する</a>
</div>
@endsection