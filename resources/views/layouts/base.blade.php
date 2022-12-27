<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Inventory Booking System">
        <meta name="author" content="Ryan Coombes 2018-2022">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <link rel="icon" href="favicon.ico">
        <title>{{ config('app.name', 'Inventory Booking System') }}</title>

        <!-- CSS Mix -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">

        <!-- Alpine -->
        <script src="https://unpkg.com/alpinejs@3.10.3/dist/cdn.min.js" defer></script>

        <!-- JS Mix -->
        <script src="{{ mix('js/app.js') }}"></script>

        <!-- Livewire -->
        <livewire:styles />

        <style>
            .toast {
                opacity: 1 !important;
            }
            body {
                background: #f5f7f7;;
                font-family: 'Varela Round', sans-serif;
            }
            .navbar {
                color: #fff;
                background: #3c9edf !important;
                padding: 5px 16px;
                border-radius: 0;
                border: none;
                box-shadow: 0 0 4px rgba(0,0,0,.1);
            }
            .navbar img {
                border-radius: 50%;
                width: 36px;
                height: 36px;
                margin-right: 10px;
            }
            .navbar .navbar-brand {
                color: #efe5ff;
                padding-left: 0;
                padding-right: 50px;
                font-size: 24px;
            }
            .navbar .navbar-brand:hover, .navbar .navbar-brand:focus {
                color: #fff;
            }
            .navbar .navbar-brand i {
                font-size: 25px;
                margin-right: 5px;
            }
            .search-box {
                position: relative;
            }
            .search-box input {
                padding-right: 35px;
                min-height: 38px;
                border: none;
                background: #faf7fd;
                border-radius: 3px !important;
            }
            .search-box input:focus {
                background: #fff;
                box-shadow: none;
            }
            .search-box .input-group-addon {
                min-width: 35px;
                border: none;
                background: transparent;
                position: absolute;
                right: 0;
                z-index: 9;
                padding: 10px 7px;
                height: 100%;
            }
            .search-box i {
                color: #a0a5b1;
                font-size: 19px;
            }
            .navbar .nav-item i {
                font-size: 18px;
            }
            .navbar .nav-item span {
                position: relative;
                top: 3px;
            }
            .navbar .navbar-nav > a {
                color: #efe5ff;
                padding: 8px 15px;
                font-size: 14px;
            }
            .navbar .navbar-nav > a:hover, .navbar .navbar-nav > a:focus {
                color: #fff;
                text-shadow: 0 0 4px rgba(255,255,255,0.3);
            }
            .navbar .navbar-nav > a > i {
                display: block;
                text-align: center;
            }
            .navbar .dropdown-item i {
                font-size: 16px;
                min-width: 22px;
            }
            .navbar .dropdown-item .material-icons {
                font-size: 21px;
                line-height: 16px;
                vertical-align: middle;
                margin-top: -2px;
            }
            .navbar .nav-item.open > a, .navbar .nav-item.open > a:hover, .navbar .nav-item.open > a:focus {
                color: #fff;
                background: none !important;
            }
            .navbar .dropdown-menu {
                border-radius: 1px;
                border-color: #e5e5e5;
                box-shadow: 0 2px 8px rgba(0,0,0,.05);
            }
            .navbar .dropdown-menu a {
                color: #777 !important;
                padding: 8px 20px;
                line-height: normal;
                font-size: 15px;
            }
            .navbar .dropdown-menu a:hover, .navbar .dropdown-menu a:focus {
                color: #333 !important;
                background: transparent !important;
            }
            .navbar .navbar-nav .active a, .navbar .navbar-nav .active a:hover, .navbar .navbar-nav .active a:focus {
                color: #fff;
                text-shadow: 0 0 4px rgba(255,255,255,0.2);
                background: transparent !important;
            }
            .navbar .navbar-nav .user-action {
                padding: 9px 15px;
                font-size: 15px;
            }
            .navbar .navbar-toggle {
                border-color: #fff;
            }
            .navbar .navbar-toggle .icon-bar {
                background: #fff;
            }
            .navbar .navbar-toggle:focus, .navbar .navbar-toggle:hover {
                background: transparent;
            }
            .navbar .navbar-nav .open .dropdown-menu {
                background: #faf7fd;
                border-radius: 1px;
                border-color: #faf7fd;
                box-shadow: 0 2px 8px rgba(0,0,0,.05);
            }
            .navbar .divider {
                background-color: #e9ecef !important;
            }
            @media (min-width: 1200px){
                .form-inline .input-group {
                    width: 350px;
                    margin-left: 30px;
                }
            }
            @media (max-width: 1199px){
                .navbar .navbar-nav > a > i {
                    display: inline-block;
                    text-align: left;
                    min-width: 30px;
                    position: relative;
                    top: 4px;
                }
                .navbar .navbar-collapse {
                    border: none;
                    box-shadow: none;
                    padding: 0;
                }
                .navbar .navbar-form {
                    border: none;
                    display: block;
                    margin: 10px 0;
                    padding: 0;
                }
                .navbar .navbar-nav {
                    margin: 8px 0;
                }
                .navbar .navbar-toggle {
                    margin-right: 0;
                }
                .input-group {
                    width: 100%;
                }
            }
            </style>
    </head>

    <body class="hold-transition sidebar-mini">
        {{ $slot }}

        <livewire:scripts />
    </body>
</html>