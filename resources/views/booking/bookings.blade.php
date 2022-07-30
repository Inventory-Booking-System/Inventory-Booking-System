@extends('layouts.app')

@section('navItems')
    <li class="nav-item">
        <a id="addLoan" href="/bookings/create" class="nav-link">
        <i class="nav-icon fas fa-plus"></i>
        <p>
            New Setup
        </p>
        </a>
    </li>
@endsection

@section('mainContent')
    <div id="assetTable" class="card-body">
        <table id='loansTable' class='table yajra-datatable' width="100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Details</th>
                    <th>Assets</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection