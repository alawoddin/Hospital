<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laboratory Dashboard</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/images/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('backend/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
</head>
<body>
<div id="preloader"><div class="sk-three-bounce"><div class="sk-child sk-bounce1"></div><div class="sk-child sk-bounce2"></div><div class="sk-child sk-bounce3"></div></div></div>
<div id="main-wrapper">
    <div class="nav-header">
        <a href="{{ route('laboratory.dashboard') }}" class="brand-logo">
            <img class="logo-abbr" src="{{ asset('backend/images/logo-white.png') }}" alt="">
        </a>
        <div class="nav-control"><div class="hamburger"><span class="line"></span><span class="line"></span><span class="line"></span></div></div>
    </div>
    @include('backend.recieption.recieption_body.recieption_header')
    @include('backend.laboratory.laboratory_body.laboratory_sidebar')
    <div class="content-body">@yield('laboratory')</div>
    @include('backend.recieption.recieption_body.recieption_footer')
</div>
<script src="{{ asset('backend/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('backend/js/deznav-init.js') }}"></script>
<script src="{{ asset('backend/js/custom.min.js') }}"></script>
</body>
</html>
