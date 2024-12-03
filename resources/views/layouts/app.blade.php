<x-layouts.base>
    <div class="wrapper">
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-xl navbar-dark bg-dark">
            <a href="#" class="navbar-brand"><i class="fa fa-cube"></i>Inventory Booking System</b></a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div id="navbarCollapse" class="collapse navbar-collapse justify-content-start">
                <form class="navbar-form form-inline">

                </form>
                <div class="navbar-nav mr-auto">

                    <a href="/loans" class="nav-item nav-link {{ \Request::route()->getName() === 'loans.index' ? 'active' : '' }}"><i class="fa-solid fa-cart-shopping"></i><span>Loans</span></a>
                    <a href="/setups" class="nav-item nav-link {{ \Request::route()->getName() === 'setups.index' ? 'active' : '' }}"><i class="fa-solid fa-truck-ramp-box"></i><span>Setups</span></a>
                    <a href="/incidents" class="nav-item nav-link {{ \Request::route()->getName() === 'incidents.index' ? 'active' : '' }}"><i class="fa-solid fa-triangle-exclamation"></i><span>Incidents</span></a>
                    <a href="/asset-groups" class="nav-item nav-link {{ \Request::route()->getName() === 'asset-groups.index' ? 'active' : '' }}"><i class="fa-solid fa-box-open"></i><span>Asset Groups</span></a>
                    <a href="/assets" class="nav-item nav-link {{ \Request::route()->getName() === 'assets.index' ? 'active' : '' }}"><i class="fa-solid fa-camera"></i><span>Assets</span></a>
                    <a href="/users" class="nav-item nav-link {{ \Request::route()->getName() === 'users.index' ? 'active' : '' }}"><i class="fa fa-users"></i><span>Accounts</span></a>
                    <a href="/locations" class="nav-item nav-link {{ \Request::route()->getName() === 'locations.index' ? 'active' : '' }}"><i class="fa fa-house-chimney"></i><span>Locations</span></a>
                    <a href="/distributionGroups" class="nav-item nav-link {{ \Request::route()->getName() === 'distributionGroups.index' ? 'active' : '' }}"><i class="fa-sharp fa-solid fa-user-group"></i><span>Distribution Groups</span></a>
                    <a href="/equipmentIssues" class="nav-item nav-link {{ \Request::route()->getName() === 'equipmentIssues.index' ? 'active' : '' }}"><i class="fa-solid fa-circle-exclamation"></i><span>Equipment Issues</span></a>
                    <a href="/settings" class="nav-item nav-link {{ \Request::route()->getName() === 'settings.index' ? 'active' : '' }}"><i class="fa-solid fa-gear"></i><span>Settings</span></a>
                </div>

                <div class="navbar-nav ml-auto">
                    <div class="nav-item dropdown">
                        <a href="#" data-toggle="dropdown" class="nav-item nav-link dropdown-toggle user-action">{{ Auth::user()->forename }} {{ Auth::user()->surname }}<b class="caret"></b></a>
                        <div class="dropdown-menu">
                            <a href="/profile" class="dropdown-item"><i class="fa-solid fa-user"></i> Profile</a>
                            <div class="divider dropdown-divider"></div>
                            <a href="{{ route('logout') }}" class="dropdown-item"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Wrapper. Contains page content -->
        <div style="background:#f5f7f7;">
            <div class="content-header">
                <div class="container-fluid">
                </div>
            </div>

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid px-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.base>