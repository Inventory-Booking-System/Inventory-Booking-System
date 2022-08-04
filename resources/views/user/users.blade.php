@extends('layouts.app')

@section('navItems')
    <li class="nav-item">
        <a id="addLoan" href="/users/create" class="nav-link">
        <i class="nav-icon fas fa-plus"></i>
        <p>
            New User
        </p>
        </a>
    </li>
@endsection

@section('mainContent')
    <div id="userTable" class="card-body">
        <table id='usersTable' class='table yajra-datatable' width="100%">
            <thead>
                <tr>
                    <th>Forename</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/users.js') }}"></script>
@endpush