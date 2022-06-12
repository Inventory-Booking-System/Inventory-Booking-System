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

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
        @stack('scripts')
    </head>

    <!-- Navbar -->
    <body class="hold-transition sidebar-mini">
        <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-dark navbar-gray-dark">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link" href="">Loans</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link" href="">Setups</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link" href="">Incidents</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link" href="">Consumables</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link" href="">Assets</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a class="nav-link" href="">Accounts</a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item d-none d-sm-inline-block">
            <a class="nav-link" href="">Settings</a>
            </li>
        </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="https://github.com/Dragnogd/SEAS-Booking-System" class="brand-link">
                <span class="brand-text font-weight-light text-center">{{ config('app.name', 'Inventory Booking System') }}</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">

            <!-- Sidebar Menu -->

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                    @yield('navItems')
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
            <div class="container-fluid">
            </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
            <div class="container-fluid">
                <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        @yield('mainContent')
                    </div>
                </div>
                <!-- /.col-md-6 -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->
    </body>
</html>