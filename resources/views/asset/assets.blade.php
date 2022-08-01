@extends('layouts.app')

@section('navItems')
    <li class="nav-item">
        <a id="addAsset" href="/assets/create" class="nav-link">
        <i class="nav-icon fas fa-plus"></i>
        <p>
            New Asset
        </p>
        </a>
    </li>
@endsection

@section('mainContent')
    <div id="assetTable" class="card-body">
        <table id='assetsTable' class='table yajra-datatable' width="100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Asset Tag</th>
                    <th>Description</th>
                    <th>Cost</th>
                    <th>Bookable</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@include('asset.modals.add')
@include('asset.modals.delete')

@push('scripts')
    <script src="{{ asset('js/assets.js') }}"></script>
@endpush