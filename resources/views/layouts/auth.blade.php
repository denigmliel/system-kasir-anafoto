<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo-rounded.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('img/logo-rounded.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo-rounded.png') }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    @stack('head')
</head>
<body class="@yield('body-class')">
    @yield('content')

    @stack('scripts')
</body>
</html>
