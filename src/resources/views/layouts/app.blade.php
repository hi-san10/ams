<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance management system</title>
    <link rel="stylesheet" href="{{ asset('css/reset.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="ams-header">
        <div class="header-logo">
            <img class="header-logo__img" src="{{ asset('img/logo.svg') }}" alt="">
        </div>
        @if (Auth::check())
        <div class="header__nav">
            <ul class="nav-list">
                <li><a class="list-menu" href="/attendance">勤怠</a></li>
                <li><a class="list-menu" href="/attendance/list">勤怠一覧</a></li>
                <li><a class="list-menu" href="/stamp_correction_request/list">申請</a></li>
                <li><a class="list-menu" href="/logout">ログアウト</a></li>
            </ul>
        </div>
        @elseif (Auth::guard('admins')->check())
        <div class="header__nav">
            <ul class="nav-list">
                <li><a class="list-menu" href="/admin/attendance/list">勤怠一覧</a></li>
                <li><a class="list-menu" href="/admin/staff/list">スタッフ一覧</a></li>
                <li><a class="list-menu" href="/stamp_correction_request/list">申請一覧</a></li>
                <li><a class="list-menu" href="/logout">ログアウト</a></li>
            </ul>
        </div>
        @endif
    </header>
    @yield('content')
</body>

</html>