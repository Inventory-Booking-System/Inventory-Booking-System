<x-layouts.base>
    <div class="wrapper">
            <nav class="navbar navbar-expand-xl navbar-dark bg-dark">
                <a href="#" class="navbar-brand"><i class="fa fa-cube"></i>Inventory Booking System</b></a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Collection of nav links, forms, and other content for toggling -->
                <div id="navbarCollapse" class="collapse navbar-collapse justify-content-start">
                    <form class="navbar-form form-inline">

                    </form>
                    <div class="navbar-nav mr-auto">

                        <a href="/loans" class="nav-item nav-link active"><i class="fa-solid fa-cart-shopping"></i><span>Loans</span></a>
                        <a href="/setups" class="nav-item nav-link"><i class="fa-solid fa-truck-ramp-box"></i><span>Setups</span></a>
                        <a href="/incidents" class="nav-item nav-link"><i class="fa-solid fa-triangle-exclamation"></i><span>Incidents</span></a>
                        <a href="/assets" class="nav-item nav-link"><i class="fa-solid fa-camera"></i><span>Assets</span></a>
                        <a href="/users" class="nav-item nav-link"><i class="fa fa-users"></i><span>Accounts</span></a>
                        <a href="/settings" class="nav-item nav-link"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
                    </div>

                    <div class="navbar-nav ml-auto">
                        <div class="nav-item dropdown">
                            <a href="#" data-toggle="dropdown" class="nav-item nav-link dropdown-toggle user-action">Antonio Moreno<b class="caret"></b></a>
                            <div class="dropdown-menu">
                                <a href="#" class="dropdown-item"><i class="fa fa-envelope"></i> Profile</a>
                                <a href="#" class="dropdown-item"><i class="fa fa-envelope"></i> Calendar</a>
                                <a href="#" class="dropdown-item"><i class="fa fa-sliders"></i> Settings</a>
                                <div class="divider dropdown-divider"></div>
                                <a href="#" class="dropdown-item"><i class="fa fa-envelope"></i> Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

        {{-- <!-- Main Sidebar Container -->
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

                    </ul>
                </nav>
            </div>
        </aside> --}}

        <!-- Content Wrapper. Contains page content -->
        <div>
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                              <li class="breadcrumb-item"><a href="#">Home</a></li>
                              <li class="breadcrumb-item"><a href="#">Assets</a></li>
                              <li class="breadcrumb-item active" aria-current="page">New Asset</li>
                            </ol>
                        </nav>
                    </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    {{-- <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                @yield('mainContent')
                            </div>
                        </div>
                    </div> --}}

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.base>