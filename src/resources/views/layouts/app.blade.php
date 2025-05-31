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
        @yield('header')
    </header>
    @yield('content')
</body>

</html>